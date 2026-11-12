<?php

namespace open20\amos\community\models\clients;

use open20\amos\community\models\base\Community;
use open20\amos\community\models\clients\msmodels\MsChannel;
use open20\amos\community\models\clients\msmodels\MsOrganization;
use open20\amos\community\models\clients\msmodels\MsTeamMember;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\core\forms\editors\DateTime;
use open20\amos\core\models\AccessTokens;
use open20\amos\core\views\IconView;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\log\Logger;

class MsTeamsClient extends Model
{
    const PLATFORM_KEY = 'ms_graph_application_access_token';

    public $useOnlyApplicationToken = false;

    public $provider;
    public $tenantId;
    public $clientId;
    public $secretValue;
    public $licensetype;


    public $baseApiUrl = "https://graph.microsoft.com/v1.0";

    public $error;
    public $loginHint;


    public function init()
    {
        parent::init();
        $moduleCommunity = \Yii::$app->getModule('community');
        if ($moduleCommunity) {
            $configs = $moduleCommunity->microsoftTeamsConfigs;
            if (!empty($configs['tenantId'])) {
                $this->tenantId = $configs['tenantId'];
            }
            if (!empty($configs['clientId'])) {
                $this->clientId = $configs['clientId'];
            }
            if (!empty($configs['secretValue'])) {
                $this->secretValue = $configs['secretValue'];
            }
            if (!empty($configs['licensetype'])) {
                $this->licensetype = $configs['licensetype'];
            }

        }
        $this->provider = $this->getMsProvider();
    }

    public function getMsProvider()
    {
        $provider = new GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->secretValue,
            'redirectUri' => \Yii::$app->params['platform']['frontendUrl'] . '/community/ms-teams/auth',
            'urlAuthorize' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize" . ($this->loginHint ? '?login_hint=' . $this->loginHint : ''),
            'urlAccessToken' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            'urlResourceOwnerDetails' => $this->baseApiUrl . '/me',
            'scopes' => 'openid profile email offline_access',
        ]);
        return $provider;
    }

    function getAppOnlyAccessToken()
    {
        $tenantId = $this->tenantId;
        $clientId = $this->clientId;
        $clientSecret = $this->secretValue;
        $url = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";
        $client = new Client();

        try {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            $this->saveApplicationTokenOnDb($data);
            return $data;
        } catch (\Exception $e) {
            echo 'Errore: ' . $e->getMessage();
            return null;
        }
    }

    /**
     * @param $result
     * @return void
     */
    public function saveApplicationTokenOnDb($result)
    {
        $data = [];
        $accessTokenData = \open20\amos\utility\models\PlatformConfigs::find()->andWhere(['module' => 'community', 'key' => self::PLATFORM_KEY])->one();
        if (empty($accessTokenData)) {
            $accessTokenData = new \open20\amos\utility\models\PlatformConfigs();
            $accessTokenData->module = 'community';
            $accessTokenData->key = self::PLATFORM_KEY;
            $accessTokenData->value = null;
            $accessTokenData->save(false);
        }
        if (!empty($result['access_token'])) {
            $now = new \DateTime();
            $now->add(new \DateInterval('PT' . $result['expires_in'] . 'S'));
            $data['expire_date'] = $now->format('Y-m-d H:i:s');
            $data['access_token'] = $result['access_token'];
            $accessTokenData->key = self::PLATFORM_KEY;
            $accessTokenData->value = json_encode($data);
            $accessTokenData->save(false);
        }
    }

    public function getTokenFromDatabase()
    {
        $accessTokenData = \open20\amos\utility\models\PlatformConfigs::find()->andWhere(['module' => 'community', 'key' => self::PLATFORM_KEY])->one();
        if (!empty($accessTokenData) && !empty($accessTokenData->value)) {
            $data = json_decode($accessTokenData->value);
            $now = new \DateTime();

            if (!empty($data->expire_date)) {
                $expireDate = new \DateTime($data->expire_date);
                if ($now <= $expireDate) {
                    return [
                        'access_token' => $data->access_token,
                        'expire_date' => $data->expire_date,
                    ];
                }
            }
        }
        $access_token = $this->getAppOnlyAccessToken();
        return [
            'access_token' => $access_token['access_token'],
            'expire_date' => $access_token['expire_in'],
        ];
    }


    public function auth($application = false)
    {
        $provider = $this->provider;
        if (!isset($_GET['code'])) {
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        } else {
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $resourceOwner = $provider->getResourceOwner($token);
                $this->saveAccessToken($token, null, $resourceOwner);
//                $this->listUsers($token->getToken());
//                $this->listAllTeams($token->getToken());

            } catch (\Exception $e) {
                echo 'Errore: ' . $e->getMessage();
                exit('Oh dear...');
            }
        }
    }

    public function getAccessToken()
    {
        if ($this->useOnlyApplicationToken) {
            $token = $this->getTokenFromDatabase();
            return $token['access_token'];
        }
        // per autenticazione delegata
        $token = $this->refreshToken();
        if (empty($token)) {
            $token = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        }
        if (empty($token)) {
            return \Yii::$app->controller->redirect('/community/community/auth');
        } else {

            return $token->access_token;
        }
    }

    public function getCurrentToken()
    {
        $token = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        if (!empty($token)) {
            return $token;
        }
        return null;
    }


    /**
     * @param $token
     * @param $user_id
     * @param $sourceOwner
     * @return CommunityMsgraphTokens
     * @throws \yii\base\InvalidConfigException
     */
    public function saveAccessToken($token, $user_id = null, $sourceOwner = null)
    {
        if (empty($user_id)) {
            $user_id = \Yii::$app->user->id;
        }
        /**
         * @var $token AccessToken
         */
        $msToken = CommunityMsgraphTokens::find()->andWhere(['user_id' => $user_id])->one();
        if (empty($msToken)) {
            $msToken = new CommunityMsgraphTokens();
        }
        $msToken->access_token = $token->getToken();
        $msToken->refresh_token = $token->getRefreshToken();
        $msToken->expire_date = $token->getExpires();
        $msToken->user_id = $user_id;
        if ($sourceOwner) {
            $msToken->msgraph_user_id = $sourceOwner->getId();
        }
        $msToken->save(false);
        return $msToken;
    }

    /**
     * @return CommunityMsgraphTokens|false|void
     * @throws \yii\base\InvalidConfigException
     */
    public function refreshToken()
    {
        $provider = $this->provider;
        /** @var  $token  CommunityMsgraphTokens */
        $token = $this->getCurrentToken();

        if ($token->isExpired()) {
            try {
                $newAccessToken = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $token->refresh_token
                ]);
                if ($newAccessToken) {
                    return $this->saveAccessToken($newAccessToken);
                }
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return false;
                // Handle the case where the refresh token is invalid
//                echo 'Errore durante il refresh del token: ' . $e->getMessage();
                // Redirect user to re-authenticate

            }
        }
        return $token;
    }

    /**
     * @param $api
     * @param $accessToken
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call($api, $method = 'GET', $params = [], $return = 'Body', $headers = [])
    {
        $this->error = null;
        $accessToken = $this->getAccessToken();
        $client = new \yii\httpclient\Client();
        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($api)
            ->addHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ]);
        if ($headers) {
            $request->addHeaders($headers);
        }
        if ($method === 'POST' || $method === 'PATCH') {
            $request->addHeaders(['Content-Type' => 'application/json'])
                ->setContent(json_encode($params));
        } else if ($method === 'GET' && !empty($params)) {
            $request->setData($params);
        }

        $response = $request->send();

        if ($response->isOk) {
            if ($return == 'Content-Location') {
                return $response->headers['Content-Location'];
            }
            if ($return == 'Etag') {
                return $response->headers['Etag'];
            }

            $data = $response->data;
            if (isset($data['error'])) {
                // Gestione dell'errore
            } else {
                return $data;
            }
        } else {
//            pr($response);
            if (!empty($response->content)) {
                $this->error = json_decode($response->content);
                \Yii::getLogger()->log('MSGRAPH ERROR - ' . $api . ' ' . $method . ': ' . VarDumper::dumpAsString($this->error, 5), \yii\log\Logger::LEVEL_ERROR);

                return null;
            }
//pr($response); die;
            // Gestione dell'errore
            \Yii::getLogger()->log('MSGRAPH Errore nella richiesta: ' . $response->statusCode, \yii\log\Logger::LEVEL_ERROR);

//            throw new \Exception('Errore nella richiesta: ' . $response->statusCode);
        }
    }

    /**
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listAllTeams()
    {
        return $this->call($this->baseApiUrl . '/teams');
    }

    /**
     * @param $teamName
     * @param $description
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createTeam($teamName, $description, $userId, $visibility = 'Private')
    {
        // response
        // Content-Location [0] => /teams('7beaced3-a814-42cd-b9c8-831ca96fcdb1')
        $data = [
            'template@odata.bind' => $this->baseApiUrl . "/teamsTemplates('standard')",
            'visibility' => $visibility,
            'displayName' => $teamName,
            'description' => $description,
            'members' => [
                [
                    '@odata.type' => '#microsoft.graph.aadUserConversationMember',
                    'user@odata.bind' => "https://graph.microsoft.com/v1.0/users/{$userId}",
                    'roles' => ['owner']
                ]
            ],
        ];

        $contentLocation = $this->call($this->baseApiUrl . '/teams', 'POST', $data, 'Content-Location');
        if (!empty($contentLocation)) {
            if (is_string($contentLocation)) {
                $str = $contentLocation;
            } else {
                $str = $contentLocation[0];
            }

            $pattern = "/\/teams\('(?P<team_id>[0-9a-fA-F-]+)'\)/";
            preg_match($pattern, $str, $matches);
            if (!empty($matches['team_id'])) {
                return $matches['team_id'];
            }
        }
        return null;
    }

    public function updateTeam($teamId, $displayName, $description)
    {
        $data = [
            'displayName' => $displayName,
            'description' => $description,
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}", 'PATCH', $data);
    }

    /**
     * @param $teamId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteTeam($teamId)
    {
        return $this->call($this->baseApiUrl . "/groups/{$teamId}", 'DELETE');
    }

    public function getTeam($teamId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}");
    }

    public function addMemberToTeam($teamId, $userId, $roles = [])
    {
        $data = [
            '@odata.type' => '#microsoft.graph.aadUserConversationMember',
            'roles' => $roles, // Può essere un array vuoto per il ruolo standard
            'user@odata.bind' => $this->baseApiUrl . "/users('$userId')"
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/members", 'POST', $data);
    }

    /**
     * @param $teamId
     * @param $userId
     * @param $roles
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeMemberFromToTeam($teamId, $membershipId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/members/{$membershipId}", 'DELETE');
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $membershipId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeMemberFromChannel($teamId, $channelId, $membershipId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/members/{$membershipId}", 'DELETE');
    }

    /**
     * @param $teamId
     * @param $membershipId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTeamMember($teamId, $membershipId)
    {

        return $this->call($this->baseApiUrl . "/teams/$teamId/members/{$membershipId}");
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $message
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postMessageToChannel($teamId, $channelId, $message)
    {
        $data = [
            'body' => [
                'content' => $message
            ]
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/messages", 'POST', $data);
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $msUserId
     * @param $type
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addMemberToChannel($teamId, $channelId, $msUserId, $type = MsTeamMember::ROLE_MEMBER)
    {
        $data = [
            "@odata.type" => "#microsoft.graph.aadUserConversationMember",
            "roles" => [$type],
            "user@odata.bind" => "https://graph.microsoft.com/v1.0/users('{$msUserId}')"
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/members", 'POST', $data);
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelMessages($teamId, $channelId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/messages");
    }


    /**
     * @param $teamId
     * @param $channelId
     * @param $skipLink
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelMessagesDelta($teamId, $channelId, $skipLink = null)
    {

        if (!empty($skipLink)) {
            return $this->call($skipLink);
        }

        $a = $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/messages/delta");
        return $a;
    }


    public function getChannelMessageReplies($teamId, $channelId, $messageId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/messages/{$messageId}/replies");
    }

    /**
     * @param $teamId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTeamChannels($teamId)
    {
        return $this->call($this->baseApiUrl . "/teams/$teamId/channels");
    }


    public function getTeamAllChannels($teamId)
    {
        return $this->call($this->baseApiUrl . "/teams/$teamId/allChannels");
    }

    /**
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listAllUsers()
    {
        return $this->call($this->baseApiUrl . "/users");
    }

    public function inviteUser($email, $invitedUserDisplayName, $redirectUrl, $invitedUserType = 'Guest')
    {
        $data = [
            "invitedUserEmailAddress" => $email,
            'invitedUserDisplayName' => $invitedUserDisplayName,
            "inviteRedirectUrl" => $redirectUrl,
            'invitedUserType' => $invitedUserType
        ];
        return $this->call($this->baseApiUrl . '/invitations', 'POST', $data);
    }

    /**
     * @param $email
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUser($email)
    {
        return $this->call($this->baseApiUrl . "/users('$email')");
    }

    /**
     * @param $userId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserById($userId)
    {
        return $this->call($this->baseApiUrl . "/users/{$userId}");
    }

    public function createUser($email, $nameSurname, $password = null)
    {
        $forceChangePasswordNextSignIn = false;
        if (empty($password)) {
            $password = 'YourStrongPassword123';
            $forceChangePasswordNextSignIn = true;
        }
        $userDetails = [
            "accountEnabled" => true,
            "displayName" => $nameSurname,
            "mailNickname" => explode('@', $email)[0],
            "userPrincipalName" => $email,
            "passwordProfile" => [
                "forceChangePasswordNextSignIn" => $forceChangePasswordNextSignIn,
                "password" => $password
            ]
        ];
        return $this->call($this->baseApiUrl . '/users', 'POST', $userDetails);

    }

    public function getOrganization()
    {
        return $this->call($this->baseApiUrl . '/organization');
    }

    public function SubscribeToWebHook($resource, $notificationUrl, $lifecycleNotificationUrl, $expirationDateTime, $changeType = 'created,updated')
    {
        $data = [
            "changeType" => $changeType,
            "notificationUrl" => $notificationUrl,
            "lifecycleNotificationUrl" => $lifecycleNotificationUrl,
            "resource" => $resource,
            "expirationDateTime" => $expirationDateTime,
            "clientState" => "OpeninnovationTeams"
        ];
        return $this->call($this->baseApiUrl . '/subscriptions', 'POST', $data);
    }

    public function unsubscribeWebHook($subscriptionId)
    {
//        pr($subscriptionId);
        return $this->call($this->baseApiUrl . "/subscriptions/{$subscriptionId}", 'DELETE');
    }

    public function subscriptionList($nextLink = null)
    {
        if(!empty($nextLink)) {
            return $this->call($nextLink);
        }
        return $this->call($this->baseApiUrl . '/subscriptions');
    }

    public function reauthorizeWebHook($idSubscription)
    {
        $data = [];
        return $this->call($this->baseApiUrl . "/subscriptions/{$idSubscription}/reauthorize", 'POST', $data);
    }

    public function renewSubscriptionWebHook($idSubscription, $expireDate)
    {
        $data = ['expirationDateTime' => $expireDate];
        return $this->call($this->baseApiUrl . "/subscriptions/{$idSubscription}", 'PATCH', $data);
    }

    /**
     * @param $teamId
     * @param $channelName
     * @param $description
     * @param $msUserId
     * @param $type
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createChannel($teamId, $channelName, $description, $msUserId, $type = 'private')
    {
        {
            $data = [
                "@odata.type" => "#Microsoft.Graph.channel",
                "membershipType" => $type,
                "displayName" => $channelName,
                "description" => $description,
                "members" =>
                    [
                        [
                            "@odata.type" => "#microsoft.graph.aadUserConversationMember",
                            "user@odata.bind" => "https://graph.microsoft.com/v1.0/users('{$msUserId}')",
                            "roles" => ["owner"]
                        ]
                    ]
            ];
        }
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels", 'POST', $data);
    }


    public function updateChannel($teamId, $channelId, $displayName, $description)
    {
        $data = [
            'displayName' => $displayName,
            'description' => $description,
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}", 'PATCH', $data);
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteChannel($teamId, $channelId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}", 'DELETE');
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannel($teamId, $channelId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}");
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelMembers($teamId, $channelId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/members");
    }

    /**
     * @param $teamId
     * @param $channelId
     * @param $memberId
     * @param $memberType
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateChannelMemberRole($teamId, $channelId, $memberId, $memberType)
    {
        $data = [
            "@odata.type" => "#microsoft.graph.aadUserConversationMember",
            "roles" => [$memberType]
        ];
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/$channelId/members/$memberId", 'PATCH', $data);

    }

    public function getOrganizationsLicenses()
    {
        return $this->call($this->baseApiUrl . '/subscribedSkus');
    }

    public function assignLicense($userId, $skuId)
    {
        $data = [
            'addLicenses' => [
                [
                    'skuId' => $skuId,
                ],
            ],
            'removeLicenses' => []
        ];
//        pr($userId);
//        pr($data);
//        die;
        return $this->call($this->baseApiUrl . "/users/{$userId}/assignLicense", 'POST', $data);
    }

    public function updateUser($userId, $data)
    {
        return $this->call($this->baseApiUrl . "/users/{$userId}", 'PATCH', $data);
    }

    /**
     * @param $groupId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listPlans($groupId)
    {
        return $this->call($this->baseApiUrl . "/groups/{$groupId}/planner/plans");
    }

    /**
     * @param $groupId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlanDetail($planId)
    {
        return $this->call($this->baseApiUrl . "/planner/plans/{$planId}");
    }


    /**
     * @param $planId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listPlanTasks($planId)
    {
        return $this->call($this->baseApiUrl . "/planner/plans/{$planId}/tasks");
    }

    /**
     * @param $taskId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePlanTasks($taskId, $data, $etag)
    {
        $headers = ['If-Match' => $etag];
        return $this->call($this->baseApiUrl . "/planner/tasks/{$taskId}", 'PATCH', $data, 'Body', $headers);
    }

    /**
     * @param $taskId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlanTask($taskId, $etag = false)
    {
        if ($etag) {
            return $this->call($this->baseApiUrl . "/planner/tasks/{$taskId}", 'GET', [], 'Etag');
        }
        return $this->call($this->baseApiUrl . "/planner/tasks/{$taskId}");
    }

    /**
     * @param $planId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlannerPlanDetails($planId)
    {
        return $this->call($this->baseApiUrl . "/planner/plans/{$planId}/details");
    }

    /**
     * @param $taskId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlannerTaskDetails($taskId)
    {
        return $this->call($this->baseApiUrl . "/planner/tasks/{$taskId}");
    }

    /**
     * @param $teamId
     * @param $conversationId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlannerTaskConversationThreads($teamId, $threadId)
    {
        return $this->call($this->baseApiUrl . "/groups/{$teamId}/threads/{$threadId}");

    }

    /**
     * @param $teamId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listCalendarEvents($teamId)
    {
        return $this->call($this->baseApiUrl . "/groups/{$teamId}/calendar/events");
    }

    /**
     * @param $userId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listMyCalendarEvents($userId)
    {
        return $this->call($this->baseApiUrl . "/users/{$userId}/calendar/events");
    }

    /**
     * @param $userId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listMyCalendars($userId)
    {
        return $this->call($this->baseApiUrl . "/users/{$userId}/calendars");
    }

    /**
     * @param $userId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listMyCalendarEventsView($userId, $startDateTime, $endDateTime)
    {

        $data = [
            'startDateTime' => date("c", strtotime($startDateTime)),
            'endDateTime' => date("c", strtotime($endDateTime)),
        ];
        return $this->call($this->baseApiUrl . "/users/{$userId}/calendar/calendarView", 'GET', $data);
    }

    /**
     * @param $teamId
     * @param $channelId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelFolderInfo($teamId, $channelId)
    {
        return $this->call($this->baseApiUrl . "/teams/{$teamId}/channels/{$channelId}/filesFolder");
    }

    /**
     * @param $driveId
     * @param $folderId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFolderChildren($driveId, $folderId)
    {
        return $this->call($this->baseApiUrl . "/drives/{$driveId}/items/{$folderId}/children");

    }

    /**
     * @param $driveId
     * @param $itemId
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDriveItem($driveId, $itemId)
    {
        return $this->call($this->baseApiUrl . "/drives/{$driveId}/items/{$itemId}");

    }


    // -----------------------------------------

    public function processUser($email, $invitedUserDisplayName, $redirectUrl, $invitedUserType = 'Guest')
    {
        $respOrg = $this->getOrganization();
        $organization = new MsOrganization($respOrg);
        $tenantDomains = $organization->getDomains();
        $emailDomain = substr(strrchr($email, "@"), 1);

        if (in_array($emailDomain, $tenantDomains)) {
            try {
                $resp = $this->getUser($email);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return $this->createUser($email, $invitedUserDisplayName);
            }
        } else {
            return $this->inviteUser($email, $invitedUserDisplayName, $redirectUrl, $invitedUserType);
        }
        return null;
    }


    public function getMsError()
    {
        return $this->error;
    }

}