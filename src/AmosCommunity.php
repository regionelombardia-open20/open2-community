<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community;

use lispa\amos\community\controllers\CommunityController;
use lispa\amos\community\exceptions\CommunityException;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityInterface;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\utilities\CommunityUtil;
use lispa\amos\community\utilities\EmailUtil;
use lispa\amos\community\widgets\icons\WidgetIconCommunity;
use lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard;
use lispa\amos\community\widgets\icons\WidgetIconCreatedByCommunities;
use lispa\amos\community\widgets\icons\WidgetIconMyCommunities;
use lispa\amos\community\widgets\icons\WidgetIconToValidateCommunities;
use lispa\amos\core\interfaces\SearchModuleInterface;
use lispa\amos\core\interfaces\CmsModuleInterface;
use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\core\user\User;
use yii\db\ActiveQuery;
use yii\log\Logger;

/**
 * Class AmosCommunity
 * community module definition class
 * @package lispa\amos\community
 */
class AmosCommunity extends AmosModule implements ModuleInterface, SearchModuleInterface, CmsModuleInterface
{
    public static $CONFIG_FOLDER = 'config';
    
    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\community\controllers';
    public $newFileMode = 0666;
    public $name = 'Community';
    
    /**
     * Define if subcommunities are visible in the lists (created by, my communities, etc..)
     * @var bool|true $showSubcommunities
     */
    public $showSubcommunities = true;
    
    /**
     * Define if the widget of subCommunities is visible in the community dashboard
     * @var bool
     */
    public $showSubcommunitiesWidget = false;
    
    /**
     * @var bool|false $bypassWorkflow - if ignore community workflow
     */
    public $bypassWorkflow = false;
    
    /**
     * @var bool|true $enableWizard - if wizard for community creation is enabled
     */
    public $enableWizard = false;
    
    /**
     * @var int|null $communityType - null if all community types are enabled, to have a fixed community type set this field
     */
    public $communityType = null;
    
    /**
     * @var bool|true $viewTabContents - if tab contents in community view mode is visible
     */
    public $viewTabContents = true;
    
    /**
     * @var bool|true $extendRoles - if true additional roles Author and Reader are considered
     */
    public $extendRoles = false;
    
    /**
     *
     * @var bool|true $customInvitationForm - if true associate or create user.
     */
    public $customInvitationForm = false;
    
    /**
     * @var bool|true $disableButtonsUserNetworks - hide the butttons community associate, and delete in Network UserProfile
     */
    public $disableCommunityAssociationUserProfile = false;
    
    /**
     * @var array $communityRequiredFields - mandatory fields in community form
     */
    public $communityRequiredFields = ['name', 'community_type_id', 'description'];
    
    /**
     * task OPEN-2303 with defaul values
     * @var array $hideContentsModels - hide this models in tab contents
     */
    public $hideContentsModels = [
        'lispa\amos\showcaseprojects\models\ShowcaseProject',
        'lispa\amos\een\models\EenPartnershipProposal',
        'lispa\amos\events\models\Event',
    ];
    
    /**
     * @var bool $inviteUserOfcommunityParent
     */
    public $inviteUserOfcommunityParent = false;
    
    /**
     * @var bool $hideWidgetGraphicsActions
     */
    public $hideWidgetGraphicsActions = false;
    
    
    /**
     * @var array $htmlMailSubject
     */
    public $htmlMailSubject = [];
    
    /**
     * @var array $htmlMailContent
     */
    public $htmlMailContent = [];
    
    /**
     * @var bool $hideCommunityTypeSearchFilter
     */
    public $hideCommunityTypeSearchFilter = false;
    
    /**
     * @var bool $deleteCommunityWithSubcommunities
     */
    public $deleteCommunityWithSubcommunities = false;
    
    /**
     * @var bool $deleteCommunityWithContents
     */
    public $deleteCommunityWithContents = false;
    
    /**
     * @var array $defaultListViews This set the default order for the views in lists
     */
    public $defaultListViews = ['icon', 'grid'];
    
    /**
     * @var bool $forceDefaultViewType
     */
    public $forceDefaultViewType = false;
    
    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return 'community';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers/');
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this,
            require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php'));
    }
    
    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconCommunity::className(),
            WidgetIconCreatedByCommunities::className(),
            WidgetIconMyCommunities::className(),
            WidgetIconCommunityDashboard::className(),
            WidgetIconToValidateCommunities::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [
            'Community' => __NAMESPACE__ . '\\' . 'models\Community',
        ];
    }
    
    /**
     * Method to create a new validated community and add the current logged user as the manager.
     * @param string $title
     * @param int $type
     * @param string $context
     * @param string $managerRole
     * @param string $description
     * @param \lispa\amos\core\record\Record|null $model
     * @param string $managerStatus
     * @param int|null $managerId
     * @return int
     * @throws CommunityException
     */
    public function createCommunity($title, $type, $context, $managerRole, $description = '', $model = null,
                                    $managerStatus = CommunityUserMm::STATUS_ACTIVE, $managerId = null)
    {
        self::verifyUserStatus($managerStatus, true);
        
        try {
            /** @var Community $community */
            $community = AmosCommunity::instance()->createModel('Community');
            $community->name = $title;
            $community->description = $description;
            $community->community_type_id = $type;
            $community->cover_image_id = null; // TODO gestire quando le community useranno il campo
            $community->status = $community->getWorkflowSource()->getWorkflow(Community::COMMUNITY_WORKFLOW)->getInitialStatusId();
            $community->context = $context;
            if ($this->bypassWorkflow) {
                $community->validated_once = 1;
            }
            $ok = $community->save(false);
            if ($ok) {
                if ($managerId === null) {
                    $managerId = \Yii::$app->getUser()->id;
                }
                $this->createCommunityUser($community->id, $managerStatus, $managerRole, $managerId);
                
                if (!is_null($model) && ($model instanceof CommunityInterface)) {
                    $model->communityId = $community->id;
                }
                $community->status = Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED;
                $community->validated_once = 1;
                $community->detachBehavior('workflow');
                $ok = $community->save(false);
            }
            if (!$ok) {
                return 0;
            }
        } catch (\Exception $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            throw new CommunityException(AmosCommunity::t('amoscommunity', 'Unable to create community'), null,
                $exception);
        }
        
        return $community->id;
    }
    
    /**
     * Method to create a new community user if do not exists
     * @param int $idCommunity
     * @param string $userStatus
     * @param string $userRole
     * @param int $userId
     * @throws CommunityException
     */
    public function createCommunityUser($idCommunity, $userStatus, $userRole, $userId)
    {
        try {
            self::verifyUserStatus($userStatus);
            $searchUser = CommunityUserMm::findOne(['user_id' => $userId, 'community_id' => $idCommunity]);
            if (empty($searchUser)) {
                $userCommunityMm = new CommunityUserMm();
                $userCommunityMm->community_id = $idCommunity;
                $userCommunityMm->user_id = $userId;
                $userCommunityMm->status = $userStatus;
                $userCommunityMm->role = $userRole;
                $userCommunityMm->save(false);
                $community = Community::findOne($idCommunity);
                $community->setCwhAuthAssignments($userCommunityMm);
            }
        } catch (\Exception $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            throw new CommunityException(AmosCommunity::t('amoscommunity', 'Unable to create user-community MM'), null,
                $exception);
        }
    }

    /**
     * @param $communityId
     * @param $userId
     * @return bool
     * @throws \yii\db\StaleObjectException
     */
    public function deleteCommunityUser($communityId, $userId)
    {
        /** @var Community $community */
        $community = Community::findOne($communityId);
        if($community) {
            $communityUserMmRow = CommunityUserMm::findOne(['community_id' => $communityId, 'user_id' => $userId]);
            //remove all cwh permissions for domain = community
            $community->setCwhAuthAssignments($communityUserMmRow, true);
            $communityUserMmRow->delete();
            return true;
        }
        return false;
    }

    /**
     * @param $communityId
     * @param $userId
     * @param $role
     */
    public function changeRoleCommunityUser($communityId, $userId, $role){
        $userCommunity = CommunityUserMm::find()->andWhere(['community_id' => $communityId, 'user_id' => $userId])->one();
        if (!is_null($userCommunity)) {
            $nomeCognome = " ";
            $communityName = '';
            $userCommunity->role = $role;
            $ok = $userCommunity->save(false);
            if ($ok) {
                $userCommunity->community->setCwhAuthAssignments($userCommunity);
                /** @var UserProfile $userProfile */
                $user = User::findOne($userId);
                $userProfile = $user->getProfile();
                if (!is_null($userProfile)) {
                    $nomeCognome = " '" . $userProfile->nomeCognome . "' ";
                }
                if (!is_null($userCommunity->community)) {
                    $communityName = " '" . $userCommunity->community->name . "'";
                }
                $message = $nomeCognome . " " . AmosCommunity::tHtml('amoscommunity',
                        "is now") . " " . $userCommunity->role . " " . AmosCommunity::tHtml('amoscommunity',
                        "of") . " '" . $communityName . "'";
                $emailUtil = new EmailUtil(EmailUtil::CHANGE_ROLE, $userCommunity->role, $userCommunity->community,
                    $userProfile->nomeCognome, '', null, $userProfile->user_id);
                $subject = $emailUtil->getSubject();
                $text = $emailUtil->getText();
                $communityController = new CommunityController('community', $this);
                $communityController->sendMail(null, $user->email, $subject, $text, [], []);
            }
            return true;
        }
        return false;
    }


    /**
     * If the state is not allowed, it generates an exception
     * @param string $userStatus
     * @param boolean $manager
     * @throws CommunityException
     */
    protected static function verifyUserStatus($userStatus, $manager = false)
    {
        $communityUserMmStates = CommunityUserMm::getUserStates();
        if (!is_string($userStatus) || !strlen($userStatus) || !in_array($userStatus, $communityUserMmStates)) {
            throw new CommunityException(AmosCommunity::t('amoscommunity', '{typeUser} status not allowed',
                [
                    'typeUser' => ($manager ? AmosCommunity::t('amoscommunity', 'Manager') : AmosCommunity::t('amoscommunity',
                        'User'))
                ]));
        }
    }
    
    /**
     * This method return an array of Community objects representing all the communities of a user.
     * @param int $userId
     * @param bool $onlyIds
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public function getCommunitiesByUserId($userId, $onlyIds = false)
    {
        return CommunityUtil::getCommunitiesByUserId($userId, $onlyIds);
    }
    
    /**
     * @param int $userId
     * @param bool $onlyIds
     * @return Community[]|int[]
     * @throws CommunityException
     */
    public function getCommunitiesManagedByUserId($userId, $onlyIds = false)
    {
        return CommunityUtil::getCommunitiesManagedByUserId($userId, $onlyIds);
    }
    
    /**
     * This method return a string array of community context classnames present in the community table.
     * @return array
     */
    public function getAllCommunityContexts()
    {
        return CommunityUtil::getAllCommunityContexts();
    }
    
    /**
     * This method return a string array of community managers of all community contexts.
     * @return array
     */
    public function getAllCommunityManagerRoles()
    {
        return CommunityUtil::getAllCommunityManagerRoles();
    }
    
    /**
     * This method return the session key that must be used to add in session
     * the url from the user have started the content creation.
     * @return string
     */
    public static function beginCreateNewSessionKey()
    {
        return 'beginCreateNewUrl_' . self::getModuleName();
    }
    
    public static function getModuleIconName()
    {
        return 'groups';
    }
    
    
    /****
     * CmsModuleInterface
     */
     public static function getModelSearchClassName() {
        return __NAMESPACE__.'\models\search\CommunitySearch';
    }

    public static function getModelClassName() {
        return __NAMESPACE__ . '\models\Community';
    }
}
