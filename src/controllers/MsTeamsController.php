<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\community\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\comments\models\Comment;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\base\CommunityUserMm;
use open20\amos\community\models\ChooseEmailForm;
use open20\amos\community\models\clients\MsGraphBuilder;
use open20\amos\community\models\clients\msmodels\MsCalendarEvent;
use open20\amos\community\models\clients\msmodels\MsChannelMessage;
use open20\amos\community\models\clients\msmodels\MsDriveItem;
use open20\amos\community\models\clients\msmodels\MsInvitation;
use open20\amos\community\models\clients\msmodels\MsNotification;
use open20\amos\community\models\clients\msmodels\MsPlannerTask;
use open20\amos\community\models\clients\msmodels\MsSubscription;
use open20\amos\community\models\clients\msmodels\MsTeam;
use open20\amos\community\models\clients\msmodels\MsTeamMember;
use open20\amos\community\models\clients\MsTeamsClient;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\utilities\MsTeamsUtility;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\forms\FirstAccessForm;
use open20\amos\core\response\Response;
use open20\amos\core\user\User;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class MsTeamsController extends BackendController
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     * @return mixed
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge([],
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'auth',
                                'auth-done',
                                'test',
                                'disconnect-profile',
                                'connect-profile',
                                'register-web-hooks',
                                'subscribe-to-team',
                                'choose-mail-microsoft',
                                'unsubscribe-web-hooks',
                                'planner-index',
                                'jsoncalendar',
                                'update-task',
                                'import-document',
                                'create-team-or-channel',
                                'update-channel-messages',
                            ],
                            'roles' => ['@'],
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'change-channel-message',
                                'change-teams',
                                'change-channels',
                                'change-channel-members',
                                'change-team-members',
                                'change-groups',
                                'change-drive-folder',
                                'download-drive-item',
                                'lifecycle-notification'
                            ],

                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'show-web-hooks',
                                'change-teams',
                                'change-channels',
                                'change-channel-members',
                                'change-team-members',
                                'change-groups',
                                'change-drive-folder',
                                'download-drive-item',
                                'lifecycle-notification'
                            ],
                            'roles' => ['ADMIN'],


                        ],
                    ]
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post', 'get']
                    ]
                ]
            ]);
        return $behaviors;
    }

    public function actionAuth($urlRedirect = null, $email = null)
    {
        if (!empty($urlRedirect)) {
            \Yii::$app->session->set('redirectUrlConnectTeams', $urlRedirect);
        }
        $profile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        if (empty($email)) {
            $email = $profile->user->email;
        }
        $msTeamsClient = new MsTeamsClient(['loginHint' => $email]);
        $msTeamsClient->auth();
        $redirectUrlSession = \Yii::$app->session->get('redirectUrlConnectTeams');
        $msBuilder = new MsGraphBuilder();
        $msBuilder->assignLicenseToUser(\Yii::$app->user->id);
        if (!empty($redirectUrlSession)) {
            return $this->redirect($redirectUrlSession);
        }
        return $this->redirect(['/' . AmosAdmin::getModuleName() . '/user-profile/update', 'id' => $profile->id]);
    }

    public function actionChooseMailMicrosoft($urlRedirect = null)
    {
        $user = User::findOne(\Yii::$app->user->id);
        $model = new ChooseEmailForm();
        $model->email = $user->email;
        if (\Yii::$app->request->post() && $model->load(\Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect(['/community/ms-teams/connect-profile', 'urlRedirect' => $urlRedirect, 'email' => $model->email]);
        }
        return $this->render('choose-mail-microsoft', ['user' => $user, 'model' => $model]);
    }

    public function actionDisconnectProfile($urlRedirect)
    {
        CommunityMsgraphTokens::deleteAll(['user_id' => \Yii::$app->user->id]);
        if ($urlRedirect) {
            return $this->redirect($urlRedirect);
        }
        return $this->redirect('/');
    }


    public function actionConnectProfile($urlRedirect = null, $email = null)
    {

        $user = User::findOne(\Yii::$app->user->id);
        $model = new FirstAccessForm();
        $model->username = $user->username;
        if (empty($email)) {
            $email = $model->email;
        }
        $urlRedirectNew = \Yii::$app->urlManager->createAbsoluteUrl(['/community/ms-teams/auth', 'urlRedirect' => $urlRedirect, 'email' => $email]);

        if ($user) {
            $msBuilder = new MsGraphBuilder();
            try {
                $resp = $msBuilder->getUser($email);
                if (empty($resp)) {
                    $domains = $msBuilder->getOrganizationDomains();
                    $explode = explode('@', $email);

                    if (!in_array($explode[1], $domains)) {
                        $res = $msBuilder->inviteUser($email, $user->userProfile->nomeCognome, $urlRedirectNew, 'Member');
                        $msInvitation = new MsInvitation($res);
                        return $this->render('connect-profile', [
                            'redeemUrl' => $msInvitation->inviteRedeemUrl,
                            'displayName' => $msInvitation->invitedUserDisplayName,
                            'email' => $msInvitation->invitedUserEmailAddress,
                            'msInvitation' => $msInvitation
                        ]);
                    } else {
                        if (\Yii::$app->request->post() && $model->load(\Yii::$app->request->post()) && $model->validate()) {
                            $res = $msBuilder->createUser($email, $user->userProfile->nomeCognome, $model->password);
                            return $this->redirect($urlRedirectNew);
                        }
                        return $this->render('choose-password', ['model' => $model, 'user' => $user, 'urlRedirect' => $urlRedirect]);
                    }

                } else {
                    // se l'utente teams è già asssociato ad un utente do errore
                    if (!empty($resp['id'])) {
                        $token = CommunityMsgraphTokens::find()
                            ->andWhere(['msgraph_user_id' => $resp['id']])
                            ->andWhere(['<>', 'user_id', \Yii::$app->user->id])
                            ->one();
                        if ($token) {
                            \Yii::$app->session->addFlash('danger', AmosCommunity::t('amoscommunity', "L'account <strong>{email}</strong> di Microsoft Teams è già agganciato ad un utente.", ['email' => $email]));
                            return $this->redirect($urlRedirect);
                        }
                    }
//                        die;
//                    pr('utente già nel tenant');
                }
//                die;
            } catch (\Exception $e) {

            }
            return $this->redirect(['/community/ms-teams/auth', 'urlRedirect' => $urlRedirect, 'email' => $email]);

        }
    }

    public function actionTest()
    {

        $mariorossiId = '67797ea3-94c0-4bf5-8e24-fb95df27bb0e';
        $channelGeneral = '19:7GbkCu46G06fx7_AKGnTPBQm4bm_P3bxW-c829oX5Ug1@thread.tacv2';
        $team = 'd9d4e7a3-533a-4066-a6ac-0179abffb9fc';
        $msTeamsClient = new MsTeamsClient();
        /** @var  $msBuilder MsGraphBuilder */
        $msBuilder = new MsGraphBuilder();
//        pr($msBuilder->getOrganizationsLicenses());die;
//        $community = Community::findOne(3182);
        $community = Community::findOne(3146);
        $res = $msBuilder->getChannelMessages($community->msgraph_team_id, $community->msgraph_channel_id);

        pr($res);
        die;
        $folder = $msBuilder->getChannelFolderInfo($community->msgraph_team_id, $community->msgraph_channel_id);
        $items = $msBuilder->getFolderChildren($folder->getDriveId(), $folder->id);
        pr($items);
        die;
        $resp = $msBuilder->listMyCalendarEventsView(1, '2024-07-16 00:00:00', '2024-07-20 23:59:00');
        /** @var  $meet MsCalendarEvent */
        foreach ($resp as $meet) {
            if ($meet) {
                pr($meet->subject);
                pr($meet->start);
//                pr($meet->end);
            }
            pr($meet);
        }
//        pr($resp);
        die;
        $res = $msBuilder->listPlans($community->msgraph_team_id);
//        pr($res);
        $msBuilder->listMyCalendars(1);
        pr('#############');
        $msBuilder->listMyCalendarEvents(1);
        $planId = 'VdBrm29nJkmMvxtS_Y1yHpgAHZnF';
        $taskId = 'F6860HFoV02mMp5vstFzopgAIcxe';
        $conversationThreadId = 'AAQkADIxY2M0ODI4LTVhNTQtNDMyYi05NmM1LWE5NGI2OTE5NjU4MwMkABAA1pIQSxjQYESU8C_eoS-QHBAA1pIQSxjQYESU8C_eoS-QHA==';

//        $msBuilder->listCalendarEvents($community->msgraph_team_id);

        $res = $msBuilder->listPlanTasks($planId);
//        pr($res);
        $msBuilder->getPlannerPlanDetail($planId);
        $res = $msBuilder->getPlannerTaskDetails($taskId);
//        pr($res);
        $msBuilder->getPlannerTaskConversation($community->msgraph_team_id, $conversationThreadId);
        die;
        $res = $msBuilder->getChannelMessagesDelta($community, $community->msgraph_team_id, $community->msgraph_channel_id);
        pr($res);
        die;
        $channel = $msBuilder->getGeneralChannel('5183b417-8f23-4ceb-9191-e6e6b015148f');
        $member = $msBuilder->getUserChannelMembership('5183b417-8f23-4ceb-9191-e6e6b015148f', $channel->id, \Yii::$app->user->id);
//        $res = $msBuilder->getChannelMembers($team, $channelGeneral);
        pr($channel);

        pr($member);
        \Yii::getLogger()->log("Questo è u nerrore di test \n" . VarDumper::dumpAsString($channel, 5), \yii\log\Logger::LEVEL_ERROR);
        die;

        $msUserId = $msBuilder->getMsGraphUserId(\Yii::$app->user->id);
        $licence = $msBuilder->getLicense();
        $msBuilder->updateUsageLocation(\Yii::$app->user->id);
        $msBuilder->assignLicense(\Yii::$app->user->id, $licence->skuId);
//        TEAMS_EXPLORATORY
        pr($licence);
        die;
        pr($msUserId);
        $nextlink = 'https://graph.microsoft.com/v1.0/teams/d9d4e7a3-533a-4066-a6ac-0179abffb9fc/channels/19:7GbkCu46G06fx7_AKGnTPBQm4bm_P3bxW-c829oX5Ug1@thread.tacv2/messages/delta?$skiptoken=-FG3FPHv7HuyuazNLuy3eXlzQGbEjYLUsW9-pYkmXgkt4DVKFKnJKk_YLrGra8S_j0k40gC9WCiRX8kUA1JljYZaV1zVJZL-5QTif6MAG42PxJl8jladUwrt_aF5tsj4.CfBBrjYzwMN8KiB7gRBLD8kJk1txi3cQZLvB3jF20gM';
        $nextlink = 'https://graph.microsoft.com/v1.0/teams/d9d4e7a3-533a-4066-a6ac-0179abffb9fc/channels/19:7GbkCu46G06fx7_AKGnTPBQm4bm_P3bxW-c829oX5Ug1@thread.tacv2/messages/delta?$deltatoken=-FG3FPHv7HuyuazNLuy3eXlzQGbEjYLUsW9-pYkmXgl3mXyWDTCTa3Ue4rN77AWpkIl587rMVWkE0IMpsD82kqmdOIKSYmCZYDmhduq-o8jhpY01CBqaBIxcyOyx2Rwl.qQN2cZLY6GT67OhR52VV-V9ntkp-1KrcJJYTbj_tC40';
        $res = $msBuilder->getChannelMessagesDelta(null, $team, $channelGeneral, $nextlink);
        pr($res);
        die;
        foreach ($res as $message) {
            pr($msBuilder->getChannelMessageReplies($team, $channelGeneral, $message->id));
        }
//        pr($res);
        die;
        if ($msUserId) {
            $res = $msBuilder->createChannel(null, 'd9d4e7a3-533a-4066-a6ac-0179abffb9fc', 'Canale prova 23', 'testo prova', \Yii::$app->user->id);
        }
//        $res = $msTeamsClient->createChannel('d9d4e7a3-533a-4066-a6ac-0179abffb9fc', 'Canale prova proivato', 'testo prova', $msUserId, 'private');

        pr($res);
        $channelMessages = $msBuilder->getChannelMessages('d9d4e7a3-533a-4066-a6ac-0179abffb9fc', $channelGeneral);
//        pr($msTeamsClient->getTeamChannels('d9d4e7a3-533a-4066-a6ac-0179abffb9fc'));
//        $res = $msTeamsClient->postMessageToChannel('d9d4e7a3-533a-4066-a6ac-0179abffb9fc',$channelGeneral,  "messaggio test");
//        $res = $msTeamsClient->getChannelMessages('d9d4e7a3-533a-4066-a6ac-0179abffb9fc',$channelGeneral);
        pr($channelMessages);
//        $msTeamsClient->useOnlyApplicationToken = true;
//        $res = $msTeamsClient->getUser('pdrova.teams@open20.it');
//        pr($res);
        die;
//        $resinvit= $msTeamsClient->inviteUser('miclafrancesca@hotmail.it','Ajeje Brazorv',\Yii::$app->params['platform']['frontendUrl'].'/community/ms-teams/callback', 'Member');
//        $resinvit= $msTeamsClient->inviteUser('prova.teams@open20.it','Prova teams',\Yii::$app->params['platform']['frontendUrl'].'/community/ms-teams/callback', 'Member');
//        pr($resinvit);
//        $msTeamsClient->getAppOnlyAccessToken();
//        die;
//        $a = $msTeamsClient->getGeneralChannel('d9d4e7a3-533a-4066-a6ac-0179abffb9fc');
//        pr($a);
//        $res = $msTeamsClient->createTeam('Team di prova', 'ecco una descrizione');

        $data = [];
//        $idMembership = 'MCMjMSMjZDBlOGVmZTMtMzJmZi00YTY4LTg0YWItMGZkZGMwZDZkOTIzIyNkOWQ0ZTdhMy01MzNhLTQwNjYtYTZhYy0wMTc5YWJmZmI5ZmMjIzY3Nzk3ZWEzLTk0YzAtNGJmNS04ZTI0LWZiOTVkZjI3YmIwZQ==';
//        $token = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
//        $token = $token->msgraph_user_id;
//        $resteam = $msTeamsClient->getTeam('d9d4e7a3-533a-4066-a6ac-0179abffb9fc');
//        pr(new MsTeam($resteam));
//        $res = $msTeamsClient->getTeamMember('d9d4e7a3-533a-4066-a6ac-0179abffb9fc',$idMembership);
//        $memb = new MsTeamMember($res);
//        $res = $msTeamsClient->addMemberToTeam('d9d4e7a3-533a-4066-a6ac-0179abffb9fc',$token);
//        pr($memb);
        die;
    }

    public function actionChangeChannelMessage()
    {
        $myfile = fopen("changemessage.txt", "a") or die("Unable to open file!");
        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');

        try {
            $resp = json_decode($requestBody);
            fwrite($myfile, VarDumper::dumpAsString($resp) . "-----\n");
            $channelToNotify = [];
            foreach ($resp->value as $notification) {

                if ($notification->changeType == 'created') {
//                    fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");
                    $messageId = $notification->resourceData->id;
                    $pattern = "/teams\('(?P<team_id>[^\)]+)'\)\/channels\('(?P<channel_id>[^\)]+)'\)\/messages\('([^\)]+)'\)/";
                    fwrite($myfile, $notification->resource . "\n");

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['team_id'])) {
                        $teamId = $matches['team_id'];
                    }
                    if (!empty($matches['channel_id'])) {
                        $channelId = $matches['channel_id'];
                    }
                    $channelToNotify [] = $channelId;


                    // $notification->resource;
                }
            }
            fwrite($myfile, "CHANNELS IDS: " . json_encode($channelToNotify) . "\n");
            $msBuilder = new MsGraphBuilder();
            foreach ($channelToNotify as $channelId) {
                fwrite($myfile, 'INIZIO CANALE' . $channelId . "\n");
                /** @var  $community Community */
                $community = Community::find()->andWhere(['msgraph_channel_id' => $channelId])->one();
                if ($community) {
                    $parent = Community::getCommunityRoot($community);
                    fwrite($myfile, $community->id . ' - ' . $community->name . "\n");
                    $messages = $msBuilder->getChannelMessagesDelta($community, $parent->msgraph_team_id, $channelId);
                    fwrite($myfile, 'COUNT ' . count($messages) . "\n");

                    /** @var  $message MsChannelMessage */
                    $messages = array_reverse($messages);
                    foreach ($messages as $message) {
                        //  fwrite($myfile, "###########" . "\n");

                        fwrite($myfile, VarDumper::dumpAsString($message, 5) . "\n");

                        $createdDate = $message->getCreatedDate();
                        fwrite($myfile, 'data creazione ->' . $createdDate . "\n");

                        fwrite($myfile, 'messaggio ->' . $message->id . "\n");
                        $userProfile = $message->getCreatorUserProfile();
//                        fwrite($myfile, VarDumper::dumpAsString($userProfile->attributes,5). "\n");

                        if ($userProfile) {
                            $isMemberActive = CommunityUtil::userIsCommunityMemberActive($community->id, $userProfile->user_id);
                            $replyMessages = $msBuilder->getChannelMessageReplies($parent->msgraph_team_id, $channelId, $message->id);
                            if ($isMemberActive) {
//                                  fwrite($myfile, "ACTIVE ".$userProfile->user_id. "\n");
                                fwrite($myfile, "count replies -> " . count($replyMessages) . "\n");

                                if (count($replyMessages) == 0) {
                                    $text = AmosCommunity::t('amoscommunity', "{name} ha aperto un nuovo thread su <strong>Teams</strong>", ['name' => $userProfile->nome]);
                                    $comment = Comment::find()->andWhere(['msgraph_channel_message_id' => $message->id])->one();
                                    if (empty($comment)) {
                                        $htmlContent = $message->getFormattedMessage();
                                        if (empty($htmlContent)) {
                                            $htmlContent = $text;
                                        }
                                        $htmlContent = preg_replace('/<attachment[^>]*>.*?<\/attachment>/', '', $htmlContent);
                                        \open20\amos\comments\utility\CommentsUtility::createComment(get_class($community), $community->id, $userProfile->user_id, $htmlContent, $message->id, $message->webUrl, $createdDate);
                                    }
                                } else {
                                    foreach ($replyMessages as $messageReply) {
                                        fwrite($myfile, VarDumper::dumpAsString($messageReply, 5) . "\n");
                                        $userProfileReply = $messageReply->getCreatorUserProfile();
                                        $isMemberActive = CommunityUtil::userIsCommunityMemberActive($community->id, $userProfileReply->user_id);

//                                        fwrite($myfile, "profile reply -> ".$userProfile->user_id. "\n");
                                        $createdDate = $messageReply->getCreatedDate();
                                        fwrite($myfile, "creato il-> " . $createdDate . "\n");


                                        if ($userProfileReply && $isMemberActive) {
                                            $textReply = AmosCommunity::t('amoscommunity', "{name} ha risposto ad un thread di {name2} su <strong>Teams</strong>", ['name' => $userProfileReply->nome, 'name2' => $userProfile->nome]);
                                            $comment = Comment::find()->andWhere(['msgraph_channel_message_id' => $messageReply->id])->one();
                                            if (empty($comment)) {
                                                \open20\amos\comments\utility\CommentsUtility::createComment(get_class($community), $community->id, $userProfileReply->user_id, $textReply, $messageReply->id, $message->webUrl, $createdDate);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }

                }
            }
        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }
        fclose($myfile);
    }

    /**
     * @return void
     */
    public function actionChangeTeams()
    {
        $myfile = fopen("changeteam.txt", "a") or die("Unable to open file!");

        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');

        try {
            $resp = json_decode($requestBody);
            fwrite($myfile, VarDumper::dumpAsString($resp) . "-----\n");
            $channelToNotify = [];
            foreach ($resp->value as $notification) {
                if ($notification->changeType == 'deleted') {
                    fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");
                    $messageId = $notification->resourceData->id;
                    $pattern = "/teams\('(?P<team_id>[^\)]+)'\)/";

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['team_id'])) {
                        $teamId = $matches['team_id'];
                    }
                    fwrite($myfile, $teamId . "\n");

                    $TeamToNotify [] = $teamId;
                    $community = Community::find()->andWhere(['msgraph_team_id' => $teamId])->one();
                    if ($community) {
                        fwrite($myfile, $community->name . "\n");

                        $community->msgraph_team_id = null;
                        $community->msgraph_channel_id = null;
                        $community->enable_msgraph_teams = null;
                        $community->save(false);
                        $children = Community::find()->andWhere(['parent_id' => $community->id])->all();
                        \Yii::$app->db->createCommand()
                            ->update('community_user_mm', ['msgraph_membership_id' => null], ['community_id' => $community->id])
                            ->execute();

                        foreach ($children as $child) {
                            fwrite($myfile, "child->" . $child->name . "\n");
                            $child->msgraph_team_id = null;
                            $child->msgraph_channel_id = null;
                            $child->save(false);
                            \Yii::$app->db->createCommand()
                                ->update('community_user_mm', ['msgraph_membership_id' => null], ['community_id' => $child->id])
                                ->execute();

                        }
                    }


                    // $notification->resource;
                }
            }

        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }
    }

    /**
     * @return void
     */
    public function actionChangeDriveFolder()
    {
        $myfile = fopen("changedrivefolder.txt", "a") or die("Unable to open file!");

        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');

        try {
            $resp = json_decode($requestBody);
            $channelToNotify = [];
            foreach ($resp->value as $notification) {
                if ($notification->changeType == 'updated') {
                    fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");
                    $pattern = "/drives\/(?P<drive_id>[a-zA-Z0-9!_-]+)/";

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['drive_id'])) {
                        $driveId = $matches['drive_id'];
                    }
                    fwrite($myfile, '-DRIVE ID: '.$driveId . "\n");

                    $community = Community::find()->andWhere(['msgraph_drive_id' => $driveId])->one();
                    $itemIds = [];
                    if ($community) {
                        fwrite($myfile, "-COMMUNITY: " . $community->name .' '.$community->id. "\n");

                        $msBuilder = new MsGraphBuilder();
                        $items = $msBuilder->recursiveFolderChildren($driveId, $community->msgraph_folder_id, $community, true);

                        //delete documents
                        foreach ($items as $item) {
                            $itemIds[] = $item->id;
                        }
                        fwrite($myfile, "-ITEM IDS -------> " . implode(', ', $itemIds) . "\n");
//
                        $docToDelete = \open20\amos\documenti\models\Documenti::find()
                            ->andWhere(['NOT IN', 'msgraph_drive_item_id', $itemIds])
                            ->andWhere(['msgraph_channel_id' => $community->msgraph_channel_id])
                            ->all();
                        if (!empty($docToDelete)) {
                            foreach ($docToDelete as $doc) {
                                $doc->delete();
                            }
                        }

                    }

                }
            }

        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }
    }

    /**
     * @return void
     */
    public function actionChangeTeamMembers()
    {
        $myfile = fopen("changeteammember.txt", "a") or die("Unable to open file!");

        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');

        try {
            $resp = json_decode($requestBody);
            fwrite($myfile, VarDumper::dumpAsString($resp) . "-----\n");
            $channelToNotify = [];
            foreach ($resp->value as $notification) {
                if ($notification->changeType == 'deleted') {
//                    fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");
                    $pattern = "/teams\('(?P<team_id>[^\)]+)'\)\/members\('(?P<member_id>[^\)]+)'\)/";
//                    fwrite($myfile, $notification->resource . "\n");

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['team_id'])) {
                        $teamId = $matches['team_id'];
                    }
                    if (!empty($matches['channel_id'])) {
                        $channelId = $matches['channel_id'];
                    }

                    if (!empty($matches['member_id'])) {
                        $memberId = $matches['member_id'];
                    }
                    if ($memberId) {
                        fwrite($myfile, "DENTRO" . "\n");

                        /** @var  $member CommunityUserMm */
                        $member = CommunityUserMm::find()->andWhere(['msgraph_membership_id' => $memberId])->one();
                        if ($member) {
                            fwrite($myfile, "TROVATO: " . $member->user_id . "\n");
                            fwrite($myfile, VarDumper::dumpAsString($member->attributes, 4) . "\n");
                            $member->msgraph_membership_id = null;
                            $member->save(false);
                            fwrite($myfile, VarDumper::dumpAsString($member->getErrors(), 4) . "\n");

                        }
                    }

                    fwrite($myfile, 'TEAMID: ' . $teamId . "\n");
                    fwrite($myfile, "MEMBER: " . $memberId . "\n");

                }
            }

        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }
    }


    /**
     * @return void
     */
    public function actionChangeChannelMembers()
    {
        $myfile = fopen("changechannelmember.txt", "a") or die("Unable to open file!");

        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');

        try {
            $resp = json_decode($requestBody);
            fwrite($myfile, VarDumper::dumpAsString($resp) . "-----\n");
            $channelToNotify = [];
            foreach ($resp->value as $notification) {
                if ($notification->changeType == 'deleted') {
//                    fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");
                    $pattern = "/teams\('(?P<team_id>[^\)]+)'\)\/channels\('(?P<channel_id>[^\)]+)'\)\/members\('(?P<member_id>[^\)]+)'\)/";
//                    fwrite($myfile, $notification->resource . "\n");

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['team_id'])) {
                        $teamId = $matches['team_id'];
                    }
                    if (!empty($matches['channel_id'])) {
                        $channelId = $matches['channel_id'];
                    }

                    if (!empty($matches['member_id'])) {
                        $memberId = $matches['member_id'];
                    }
                    if ($memberId) {
                        /** @var  $member CommunityUserMm */
                        $member = CommunityUserMm::find()->andWhere(['msgraph_membership_id' => $memberId])->one();
                        if ($member) {
                            $member->msgraph_membership_id = null;
                            $member->save(false);
                        }
                    }

                    fwrite($myfile, $teamId . ' -> ' . $channelId . "\n");
                    fwrite($myfile, "MEMBER: " . $memberId . "\n");

                }
            }

        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }
    }


    public function actionChangeChannels()
    {
        $myfile = fopen("changechannel.txt", "a") or die("Unable to open file!");

        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        $requestBody = file_get_contents('php://input');
        try {
            $resp = json_decode($requestBody);
            fwrite($myfile, VarDumper::dumpAsString($resp) . "-----\n");
            $channelToNotify = [];
            foreach ($resp->value as $notification) {
                fwrite($myfile, VarDumper::dumpAsString($notification) . "\n");

                if ($notification->changeType == 'deleted') {
//                    $messageId = $notification->resourceData->id;
                    $pattern = "/teams\('(?P<team_id>[^\)]+)'\)\/channels\('(?P<channel_id>[^\)]+)'\)/";
                    fwrite($myfile, $notification->resource . "\n");

                    preg_match($pattern, $notification->resource, $matches);
                    if (!empty($matches['team_id'])) {
                        $teamId = $matches['team_id'];
                    }
                    if (!empty($matches['channel_id'])) {
                        $channelId = $matches['channel_id'];
                    }
                    $channelToNotify [] = $channelId;

                    fwrite($myfile, 'CANALE ' . $channelId . "\n");

                    if (!empty($channelId)) {
                        $community = Community::find()->andWhere(['msgraph_channel_id' => $channelId])->one();
                        $community->msgraph_channel_id = null;
                        $community->save(false);
                    }

                    // $notification->resource;
                }
            }
            pr($channelToNotify);


        } catch (\Exception $e) {
            fwrite($myfile, $e->getMessage() . "\n");
            fwrite($myfile, $e->getFile() . ' ' . $e->getLine() . "\n");
            fwrite($myfile, $e->getTraceAsString() . "\n");
        }

    }

    public function actionSubscribeToTeam($community_id, $urlRedirect)
    {
        $member = CommunityUserMm::find()->andWhere(['user_id' => \Yii::$app->user->id, 'community_id' => $community_id])->one();
        /** @var  $community Community */
        $community = $member->community;
        if ($community) {
            $msBuilder = new MsGraphBuilder();
            if (empty($community->parent_id)) {
                $msTeamMember = $msBuilder->addMemberToTeam($member, $community->msgraph_team_id, \Yii::$app->user->id);
            } else {
                $community->addMemberToChannel(\Yii::$app->user->id);
            }
            if ($msTeamMember->id) {
                $member->msgraph_membership_id = $msTeamMember->id;
                $member->save(false);
                \Yii::$app->session->addFlash('success', "Iscrizione al team effettuata correttamente");
            }
        }
        return $this->redirect($urlRedirect);
    }

    public function actionImportDocument($id, $urlRedirect = null)
    {
        $msBuilder = new MsGraphBuilder();
//        pr($msBuilder->getOrganizationsLicenses());die;
        $community = Community::findOne($id);
        $folder = $msBuilder->getChannelFolderInfo($community->msgraph_team_id, $community->msgraph_channel_id);
//        $items = $msBuilder->getFolderChildren($folder->getDriveId(), $folder->id);
        $items = $msBuilder->recursiveFolderChildren($folder->getDriveId(), $folder->id, $community);
//pr($items);
        $idsFiles = [];
        /** @var  $item MsDriveItem */

        //delete documents
        foreach ($items as $item) {
            $itemIds[] = $item->id;
        }
        $docToDelete = \open20\amos\documenti\models\Documenti::find()
            ->andWhere(['NOT IN', 'msgraph_drive_item_id', $itemIds])
            ->andWhere(['msgraph_channel_id' => $community->msgraph_channel_id])
            ->all();
        if (!empty($docToDelete)) {
            foreach ($docToDelete as $doc) {
                $doc->delete();
            }
        }
//        $docToDelete = Documenti::find()
//            ->andWhere(['NOT IN', 'msgraph_drive_item_id', $idsFiles])
//            ->andWhere(['msgraph_channel_id' =>  $community->msgraph_channel_id])
//            ->all();
//        foreach($docToDelete as $doc){
//            $doc->delete();
//        }

        if(!empty($urlRedirect)) {
            return $this->redirect($urlRedirect);
        }
        return $this->redirect(['/community/join/open-join','id' => $id]);
    }


    public function actionRegisterWebHooks()
    {
        MsTeamsUtility::registerWebHooks();
//        pr('Web hooks registrati');
//        die;
        return $this->redirect('/community/ms-teams/show-web-hooks');

    }

    public function actionLifecycleNotification()
    {
        try {
            $Validationtoken = $_GET['validationToken'];
            if (!empty($Validationtoken)) {
                header('Content-Type: text/plain');
                echo $_GET['validationToken'];
                exit;
            }
        } catch (\Exception $e) {

        }
        $myfile = fopen("lifecycle.txt", "a") or die("Unable to open file!");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");

        $txt = json_encode($_POST) . "\n";
        fwrite($myfile, $txt);
        $txt = json_encode($_GET) . "\n";
        fwrite($myfile, $txt);
        $requestBody = file_get_contents('php://input');
        fwrite($myfile, json_encode($requestBody) . '----\n');
        try {
            $msBuilder = new MsGraphBuilder();
            // "{\"value\":[{\"lifecycleEvent\":\"reauthorizationRequired\",\"subscriptionId\":\"a94fdbbf-0914-4011-a5d7-093eb32ec53e\",\"resource\":\"Subscriptions\/a94fdbbf-0914-4011-a5d7-093eb32ec53e\",\"clientState\":\"OpeninnovationTeams\",\"sequence\":null,\"resourceData\":{\"@odata.type\":\"#microsoft.graph.subscription\",\"@odata.id\":\"subscriptions\/a94fdbbf-0914-4011-a5d7-093eb32ec53e\",\"id\":\"a94fdbbf-0914-4011-a5d7-093eb32ec53e\"},\"encryptedContent\":null,\"organizationId\":\"d0e8efe3-32ff-4a68-84ab-0fddc0d6d923\",\"subscriptionExpirationDateTime\":\"2024-06-15T05:44:00-07:00\"}]}"----\nArray
            $notification = json_decode($requestBody, true);
            if (!empty($notification->value)) {
                foreach ($notification->value as $subscription) {
                    if ($subscription->lifecycleEvent == 'reauthorizationRequired') {
                        $resp = $msBuilder->reauthorizeWebHook($subscription->subscriptionId);
                        $resp = $msBuilder->renewSubscriptionWebHook($subscription->subscriptionId);
                    } else if ($subscription->lifecycleEvent == 'subscriptionRemoved') {
                        $subscription = $msBuilder->subscribeToWebHookMessages();
                    }
                }
            }
            fwrite($myfile, $notification . "\n");

        } catch (\Exception $e) {
            fwrite($myfile, 'ERROR ' . json_encode($requestBody) . '----\n');

        }
        exit;

    }

    public function actionUnsubscribeWebHooks()
    {
        $msBuilder = new MsGraphBuilder();
        $subscriptions = $msBuilder->subscriptionList();
//        pr(count($subscriptions)); die;
//            pr($subscriptions);
        foreach ($subscriptions as $sub) {
            $resp = $msBuilder->unsubscribeWebHook($sub->id);

//                pr("---".$sub->notificationUrl);
//                if($sub->notificationUrl == 'https://front-poi-dev2.devel.demotestwip.it/community/ms-teams/change-channel-members') {
//                    $resp = $msBuilder->unsubscribeWebHook($sub->id);
//                }//
//                if($sub->notificationUrl == 'https://front-poi-dev2.devel.demotestwip.it/community/ms-teams/change-team-members') {
//                    $resp = $msBuilder->unsubscribeWebHook($sub->id);
//                }
        }
        return $this->redirect('/community/ms-teams/show-web-hooks');
    }

    public function actionPlannerIndex($id = null, $view = null, $current_date = null)
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        $dataProvider = null;
        if ($id) {
            $communityId = $id;
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (isset($moduleCwh)) {
                $moduleCwh->setCwhScopeInSession([
                    'community' => $id
                ], [
                    'mm_name' => 'community_user_mm',
                    'entity_id_field' => 'community_id',
                    'entity_id' => $id
                ]);
            }
        } else if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $communityId = $scope['community'];
            }
        }
        $isCommunityManager = CommunityUtil::isLoggedCommunityManager();
        if (!$isCommunityManager) {
            if (empty($view)) {
                $view = 'date';
            }
        } else {
            if (empty($view)) {
                $view = 'users';
            }
        }
        $taskUsers = [];
        $allTasks = [];
        $model = Community::findOne($communityId);
        $parent = Community::getCommunityRoot($model);
        $msBuilder = new MsGraphBuilder();

        if ($current_date) {
            $firstDayOfMonth = new \DateTime($current_date);
            $lastDayOfMonth = clone $firstDayOfMonth;
            $firstDayOfMonth->modify('first day of this month');
            $lastDayOfMonth->modify('last day of this month');
        } else {
            $firstDayOfMonth = new \DateTime('first day of this month');
            $lastDayOfMonth = new \DateTime('last day of this month');
        }
        if ($view == 'date') {
            $interval = new \DateInterval('P1D'); // Intervallo di 1 giorno
            $period = new \DatePeriod($firstDayOfMonth, $interval, $lastDayOfMonth->modify('+1 day'));

            foreach ($period as $date) {
                $taskUsers[$date->format('Y-m-d')] = [];
            }
        }
        if ($view == 'calendar' || $view == 'date') {
            $meets = $msBuilder->listMyCalendarEventsView(\Yii::$app->user->id, $firstDayOfMonth->format('Y-m-d') . ' 00:00:00', $lastDayOfMonth->format('Y-m-d') . ' 23:59:59');
            foreach ($meets as $meet) {
                if ($view == 'date') {
                    $startDateTime = $meet->getObjStartDateTime();
                    $endDateTime = $meet->getObjEndDateTime();
//                   pr($meet->subject);
//                   pr($startDateTime, $meet->start['dateTime']);
//                   pr($endDateTime,  $meet->end['dateTime']);
//                   pr($meet);
//                   pr('--------');
                    if ($startDateTime) {
                        if (isset($taskUsers [$startDateTime->format('Y-m-d')])) {
                            $channelId = $meet->getChannelId();
                            if (!empty($channelId) && $model->msgraph_channel_id == $channelId) {
                                $taskUsers [$startDateTime->format('Y-m-d')][] = $meet;
                            }
                        }
                    }
                } else {
                    $channelId = $meet->getChannelId();
                    if (!empty($channelId) && $model->msgraph_channel_id == $channelId) {
                        $allTasks[] = $meet;
                    }
                }
            }
        } else {
            $members = CommunityUserMm::find()
                ->innerJoin('community_msgraph_tokens', 'community_msgraph_tokens.user_id = community_user_mm.user_id')
                ->andWhere(['community_id' => $communityId])
                ->andWhere(['community_msgraph_tokens.deleted_at' => null])
                ->andWhere(['status' => CommunityUserMm::STATUS_ACTIVE])->asArray()->all();
            foreach ($members as $member) {
                $meets = $msBuilder->listMyCalendarEventsView($member['user_id'], $firstDayOfMonth->format('Y-m-d') . ' 00:00:00', $lastDayOfMonth->format('Y-m-d') . ' 23:59:59');
                foreach ($meets as $meet) {
                    $channelId = $meet->getChannelId();
                    if (!empty($channelId) && $model->msgraph_channel_id == $channelId) {
                        $taskUsers [$member['user_id']][] = $meet;
                    }
//                   }
                }
            }
        }

        $tenantId = $msBuilder->getTenantId();
        $plans = $msBuilder->listPlans($parent->msgraph_team_id);
//        $plan = $plans[0];
//        $msBuilder->getPlanDetail($plan->id);
        $userIds = [];
        $linkPlannerTeams = null;
        $taskLinks = [];
        foreach ($plans as $plan) {
            $tasks = $msBuilder->listPlanTasks($plan->id);
            $planWebUrl = "https://tasks.office.com/{$tenantId}/Home/PlanViews/{$plan->id}";
            if (is_null($linkPlannerTeams)) {
                $linkPlannerTeams = $planWebUrl;
//                $linkPlannerTeams = "https://teams.microsoft.com/l/entity/com.microsoft.teamspace.tab.planner/{$plan->id}/teamsEmbeddedPlannerLink?groupId={$parent->msgraph_team_id}&planId={$plan->id}&webUrl={$planWebUrl}";
            }

            /** @var  $task MsPlannerTask */
            foreach ($tasks as $task) {
                if ($view == 'date') {
//                    pr($task);
                    $taskLinks[$task->id] = "https://tasks.office.com/{$tenantId}/IT-IT/Home/Planner/#/plantaskboard?groupId={$parent->msgraph_team_id}&planId={$plan->id}&taskId={$task->id}";
                    $taskUserIds = [];
                    $userAssigned = $task->getAssignedUsers();
                    foreach ($userAssigned as $userAssign) {
                        $taskUserIds [] = $userAssign->id;
                    }
//                    pr($task)
                    $dueDateTime = $task->getObjDueDateTime();
                    if (in_array(\Yii::$app->user->id, $taskUserIds) && $dueDateTime) {
                        if (isset($taskUsers [$dueDateTime->format('Y-m-d')])) {
                            $taskUsers [$dueDateTime->format('Y-m-d')][] = $task;
                        }
                    }
                } else if ($view == 'calendar') {
                    $taskUserIds = [];
                    $userAssigned = $task->getAssignedUsers();
                    foreach ($userAssigned as $userAssign) {
                        $taskUserIds [] = $userAssign->id;
                    }
                    if (in_array(\Yii::$app->user->id, $taskUserIds)) {
                        $allTasks[] = $task;
                    }
                } else {
                    $taskLinks[$task->id] = "https://tasks.office.com/{$tenantId}/IT-IT/Home/Planner/#/plantaskboard?groupId={$parent->msgraph_team_id}&planId={$plan->id}&taskId={$task->id}";
                    $users = $task->getAssignedUsers();
                    foreach ($users as $user) {
                        $userIds [] = $user->id;
                        $taskUsers [$user->id][] = $task;
                    }

                }
            }
        }
        if ($view == 'users') {
//            $tmpTaskUser = [];
            foreach ($taskUsers as $userId => &$tasks) {
                usort($tasks, function ($a, $b) {
                    if (get_class($a) == MsPlannerTask::className()) {
                        $dateA = $a->getObjDueDateTime();
                    } else {
                        $dateA = $a->getObjStartDateTime();
                    }

                    if (get_class($b) == MsPlannerTask::className()) {
                        $dateB = $b->getObjDueDateTime();
                    } else {
                        $dateB = $b->getObjStartDateTime();
                    }

                    $timestampA = $dateA ? $dateA->getTimestamp() : PHP_INT_MAX;
                    $timestampB = $dateB ? $dateB->getTimestamp() : PHP_INT_MAX;

                    return ($timestampA > $timestampB) ? 1 : -1;
                });
//                $tmpTaskUser[$userId]= $tasks;
            }
            unset($tasks); // Rompere il riferimento all'ultimo elemento
//            $taskUsers = $tmpTaskUser;
        }


        if ($allTasks) {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $allTasks
            ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                'allModels' => []
            ]);
        }
        $linkTeamChannel = "https://teams.microsoft.com/l/channel/{$model->msgraph_channel_id}?groupId={$model->msgraph_team_id}&tenantId={$tenantId}";

//        pr($taskUsers);
//        pr($tasks);
        return $this->render('planner-index', [
            'community' => $model,
            'tasks' => $taskUsers,
            'userIds' => $userIds,
            'communityId' => $communityId,
            'dataProvider' => $dataProvider,
            'plans' => $plans,
            'isCommunityManager' => $isCommunityManager,
            'linkPlannerTeams' => $linkPlannerTeams,
            'date' => $current_date,
            'currentView' => $view,
            'taskLinks' => $taskLinks,
            'linkTeamChannel' => $linkTeamChannel
        ]);
    }

    public function actionEventCalendarWidget()
    {
        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {
            if (!is_null($_POST['id'])) {
                $event = null;
                return $this->renderAjax('eventCalendarWidget', ['model' => $event]);
            }
        }
        return '';
    }

    /**
     * @param $id
     * @return
     */
    public function actionUpdateTask($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $msBuilder = new MsGraphBuilder();

//        $ok = $msBuilder->updatePlanTasks($id);
        $etag = $msBuilder->getPlanTaskEtag($id);
        $task = $msBuilder->getPlanTask($id);
        $percentComplete = 100;
        $ret = true;
        if ($task->percentComplete == 100) {
            $percentComplete = 0;
            $ret = false;
        }
        $ok = $msBuilder->updatePlanTasks($id, $etag, $percentComplete);

        return $ret;
    }

    public function actionJsoncalendar($start = NULL, $end = NULL, $_ = NULL)
    {
        $events = [];
        $community_id = null;
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $community_id = $scope['community'];
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $community = Community::findOne($community_id);
        if ($community) {
            $parent = Community::getCommunityRoot($community);

            $msBuilder = new MsGraphBuilder();
            $tenantId = $msBuilder->getTenantId();
            $firstDayOfMonth = new \DateTime('first day of this month');
            $lastDayOfMonth = new \DateTime('last day of this month');
            $meets = $msBuilder->listMyCalendarEventsView(\Yii::$app->user->id, $firstDayOfMonth->format('Y-m-d') . ' 00:00:00', $lastDayOfMonth->format('Y-m-d') . ' 23:59:59');
            /** @var  $meet MsCalendarEvent */
            foreach ($meets as $meet) {
                $channelId = $meet->getChannelId();
                if (!empty($channelId) && $community->msgraph_channel_id == $channelId) {
                    $startDate = $meet->getObjStartDateTime();
                    $endDate = $meet->getObjEndDateTime();
                    if ($startDate && $endDate) {
                        $Event = new \yii2fullcalendar\models\Event();
                        $Event->id = $meet->uid;
                        $Event->title = $meet->subject;
                        $Event->nonstandard = [
                            'is_meet' => 1,
                            'hour' => $startDate->format('H:i') . ' - ' . $endDate->format('H:i'),
                            'joinUrl' => $meet->onlineMeeting['joinUrl'],
                            'date' => $startDate->format('d/m/Y'),
                            'tags' => '',
                            'debug' => VarDumper::dumpAsString($startDate) . ' | ' . VarDumper::dumpAsString($endDate)
//                        'html' => $meet->body['content']
                        ];
                        $Event->start = $startDate->format('Y-m-d H:i:s');
                        $Event->end = $endDate->format('Y-m-d H:i:s');
                        $Event->color = '#e6e7f2';
                        $Event->url = null;
                        $events[] = $Event;
                    }
                }
            }

            $plans = $msBuilder->listPlans($parent->msgraph_team_id);
            foreach ($plans as $plan) {
                $tasks = $msBuilder->listPlanTasks($plan->id);
                $i = 0;
                /** @var  $task MsPlannerTask */
                foreach ($tasks as $task) {
                    $dueDateTime = $task->getObjDueDateTime();
                    $tags = '';
                    foreach ($task->getCategories() as $category) {
                        $tags .= '<span class="mdi mdi-tag-outline"></span>' . $category;
                    }
                    $userAssigned = $task->getAssignedUsers();
                    foreach ($userAssigned as $userAssign) {
                        $taskUserIds [] = $userAssign->id;
                    }
                    if (in_array(\Yii::$app->user->id, $taskUserIds)) {
                        $taskUrl = "https://tasks.office.com/{$tenantId}/IT-IT/Home/Planner/#/plantaskboard?groupId={$parent->msgraph_team_id}&planId={$plan->id}&taskId={$task->id}";

                        if ($dueDateTime) {
                            $Event = new \yii2fullcalendar\models\Event();
                            $Event->id = $i;
                            $Event->title = $task->title;
                            $Event->nonstandard = [
                                'is_meet' => 0,
                                'hour' => '',
                                'date' => $dueDateTime->format('d/m/Y'),
                                'completed' => ($task->percentComplete == 100) ? true : false,
                                'id' => $task->id,
                                'tags' => $tags,
                                'taskUrl' => $taskUrl
                            ];
                            $startDate = $task->getObjDueDateTime();
                            $date = clone $startDate;
                            $endDate = $date->sub(new \DateInterval('PT1H'));

                            $Event->start = $startDate->format('Y-m-d H:i:s');
                            $Event->end = $endDate->format('Y-m-d H:i:s');
                            $Event->color = '#fff';
                            $Event->url = null;
                            $events[] = $Event;
                        }
                        $i++;
                    }

                }
            }
        }
        return $events;
    }

    public function actionDownloadDriveItem($communityId, $itemId)
    {
        $doc = \open20\amos\documenti\models\Documenti::find()->andWhere(['msgraph_drive_item_id' => $itemId])->one();
        $community = Community::findOne($communityId);
//        pr($doc->attributes);
//        pr($community->attributes);die;
        if ($doc && $community) {
            $msBuilder = new MsGraphBuilder();
            $driveId = $community->msgraph_drive_id;
            $item = $msBuilder->getDriveItem($driveId, $itemId);
            if (!empty($item->microsoftGraphDownloadUrl)) {
//                pr($item->microsoftGraphDownloadUrl); die;
                return $this->redirect($item->microsoftGraphDownloadUrl);
            }
        }
        throw new NotFoundHttpException('Document not found');

    }

    public function actionCreateTeamOrChannel($communityId)
    {
        $community = Community::findOne($communityId);
        if ($community) {
            $community->createMicrosoftTeam();
        }
        return $this->redirect(['/community/join/open-join', 'id' => $communityId]);
    }

    /**
     * @param $communityId
     * @return \yii\web\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateChannelMessages($communityId){
        $community = Community::findOne($communityId);
        if ($community) {
            $msBuilder = new MsGraphBuilder();
            $parent = Community::getCommunityRoot($community);
            $messages = $msBuilder->getChannelMessagesDelta($community, $parent->msgraph_team_id, $community->msgraph_channel_id);
            /** @var  $message MsChannelMessage */
//            $messages = array_reverse($messages);
            foreach ($messages as $message) {
                $createdDate = $message->getCreatedDate();
                $userProfile = $message->getCreatorUserProfile();
//                        fwrite($myfile, VarDumper::dumpAsString($userProfile->attributes,5). "\n");

                if ($userProfile) {
                    $isMemberActive = CommunityUtil::userIsCommunityMemberActive($community->id, $userProfile->user_id);
                    $replyMessages = $msBuilder->getChannelMessageReplies($parent->msgraph_team_id, $community->msgraph_channel_id, $message->id);
                    if ($isMemberActive) {
//                                  fwrite($myfile, "ACTIVE ".$userProfile->user_id. "\n");

                        if (count($replyMessages) == 0) {
                            $text = AmosCommunity::t('amoscommunity', "{name} ha aperto un nuovo thread su <strong>Teams</strong>", ['name' => $userProfile->nome]);
                            $comment = Comment::find()->andWhere(['msgraph_channel_message_id' => $message->id])->one();
                            if (empty($comment)) {
                                $htmlContent = $message->getFormattedMessage();
                                if (empty($htmlContent)) {
                                    $htmlContent = $text;
                                }
                                $htmlContent = preg_replace('/<attachment[^>]*>.*?<\/attachment>/', '', $htmlContent);
                                \open20\amos\comments\utility\CommentsUtility::createComment(get_class($community), $community->id, $userProfile->user_id, $htmlContent, $message->id, $message->webUrl, $createdDate);
                            }
                        } else {
                            foreach ($replyMessages as $messageReply) {
                                $userProfileReply = $messageReply->getCreatorUserProfile();
                                $isMemberActive = CommunityUtil::userIsCommunityMemberActive($community->id, $userProfileReply->user_id);

//                                        fwrite($myfile, "profile reply -> ".$userProfile->user_id. "\n");
                                $createdDate = $messageReply->getCreatedDate();

                                if ($userProfileReply && $isMemberActive) {
                                    $textReply = AmosCommunity::t('amoscommunity', "{name} ha risposto ad un thread di {name2} su <strong>Teams</strong>", ['name' => $userProfileReply->nome, 'name2' => $userProfile->nome]);
                                    $comment = Comment::find()->andWhere(['msgraph_channel_message_id' => $messageReply->id])->one();
                                    if (empty($comment)) {
                                        \open20\amos\comments\utility\CommentsUtility::createComment(get_class($community), $community->id, $userProfileReply->user_id, $textReply, $messageReply->id, $message->webUrl, $createdDate);
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }
        return $this->redirect(['/community/join/open-join', 'id' => $communityId]);
    }

    public function actionShowWebHooks(){
//        $this->setUpLayout('list');
        $msBuilder = new MsGraphBuilder();
        $subscriptions = $msBuilder->subscriptionList();
//        die;
        if(\Yii::$app->request->get('dominio') || \Yii::$app->request->get('subId')){
            $filteredSubscriptions = [];

            foreach ($subscriptions as $subscription) {
                if(!empty(\Yii::$app->request->get('subId'))){
//                    pr($subscription->id, \Yii::$app->request->get('subId'));
                    if(\Yii::$app->request->get('subId') == $subscription->id) {
                        $filteredSubscriptions [] = $subscription;
                    }
                }else {
                    if (strpos($subscription->notificationUrl, \Yii::$app->request->get('dominio')) !== false) {
                        $filteredSubscriptions [] = $subscription;
                    }
                }
            }
//            die;
        }else{
            $filteredSubscriptions = $subscriptions;
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $filteredSubscriptions,
        ]);
        return $this->render('show-web-hooks', ['dataProvider' => $dataProvider]);
    }
}