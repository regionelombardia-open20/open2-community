<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\models
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

namespace open20\amos\community\models;

use open20\amos\attachments\behaviors\FileBehavior;
use open20\amos\community\AmosCommunity;
use open20\amos\community\i18n\grammar\CommunityGrammar;
use open20\amos\community\models\clients\MsGraphBuilder;
use open20\amos\community\models\clients\MsTeamsClient;
use open20\amos\community\models\search\CommunitySearch;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\widgets\icons\WidgetIconCommunityDashboard;
use open20\amos\community\widgets\UserNetworkWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\user\User;
use open20\amos\cwh\AmosCwh;
use open20\amos\cwh\behaviors\TaggableInterestingBehavior;
use open20\amos\cwh\models\CwhAuthAssignment;
use open20\amos\cwh\models\CwhConfig;
use open20\amos\notificationmanager\behaviors\NotifyBehavior;
use open20\amos\notificationmanager\models\NotificationconfNetwork;
use open20\amos\notificationmanager\models\NotificationsConfOpt;
use open20\amos\notificationmanager\utility\NotifyUtility;
use open20\amos\seo\behaviors\SeoContentBehavior;
use open20\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\log\Logger;
use open20\amos\admin\models\UserProfile;

/**
 * Class Community
 * This is the model class for table "community".
 *
 * @property \open20\amos\community\models\CommunityUserMm[] $communityManagerMms
 * @property \open20\amos\core\user\User[] $communityManagers
 *
 * @package open20\amos\community\models
 */
class Community extends \open20\amos\community\models\base\Community implements CommunityContextInterface
{
    /**
     * @var    string    COMMUNITY_WORKFLOW    Community Workflow ID
     */
    const COMMUNITY_WORKFLOW = 'CommunityWorkflow';

    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_DRAFT        ID status draft in the model workflow (editing in progress)
     */
    const COMMUNITY_WORKFLOW_STATUS_DRAFT = 'CommunityWorkflow/DRAFT';

    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE        ID status to be validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE = 'CommunityWorkflow/TOVALIDATE';

    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_VALIDATED        ID status validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_VALIDATED = 'CommunityWorkflow/VALIDATED';

    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_NOT_VALIDATED        ID status not validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_NOT_VALIDATED = 'CommunityWorkflow/NOTVALIDATED';

    /** @var string */
    const ROLE_COMMUNITY_MANAGER = 'COMMUNITY_MANAGER';

    /**
     * @var    string    COMMUNITY_DEFAULT_NETWORK_ID        ID default newtwork configuration id = 3 for community
     */
    const COMMUNITY_DEFAULT_NETWORK_ID = 3;

    /**
     * @var mixed $communityLogo Community logo.
     */
    //public $communityLogo;
    /**
     * @var mixed $communityCoverImage Community cover image.
     */
    public $communityCoverImage;

    /**
     * @var bool $backToEdit - used in Community form
     * if true, name or description have been modified and community is in published status and community goes back to edit status
     */
    public $backToEdit;
    public $level = 0;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //in creation phase the status is set to initial status defined in community workflow
        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::COMMUNITY_WORKFLOW)->getInitialStatusId();
            if ($this->status == self::COMMUNITY_WORKFLOW_STATUS_VALIDATED) {
                $this->validated_once = 1;
            }
            if (!is_null($this->communityModule->communityType)) {
                $this->community_type_id = $this->communityModule->communityType;
            }
            if ($this->hasAttribute('force_workflow')) {
                $this->force_workflow = ($this->communityModule->forceWorkflow($this) === true) ? 1 : 0;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        //$this->communityLogo = $this->getCommunityLogo()->one();
        $this->communityCoverImage = $this->getCommunityCoverImage()->one();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        CommunityUtil::autoAddCommunityManagersToCommunity($this, CommunityUserMm::STATUS_ACTIVE);
    }

    /**
     * This method checks if a community is deleted.
     * @return bool
     */
    public function isDeleted()
    {
        return (!is_null($this->deleted_at));
    }

    /**
     * Getter for $this->communityLogo;
     * @return \yii\db\ActiveQuery
     */
    /* public function getCommunityLogo()
      {
      return $this->communityLogo = $this->hasOneFile('communityLogo')->one();
      } */

    public function getModelImage()
    {
        return $this->modelImage = $this->hasOneFile('communityLogo')->one();
    }

    /**
     * Getter for $this->communityCoverImage;
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityCoverImage()
    {
        return $this->hasOneFile('communityCoverImage');
    }

    /**
     * Url of community avatar img (logo)
     *
     * @param string $dimension Size of the image. Default = small.
     * @param bool $absolute - If the full link to the image is needed (eg. to render images in a email) or the relative url (default= false - relative url)
     * @return string $url
     */
    public function getAvatarUrl($dimension = 'small', $absolute = false, $getPublicUrl = false)
    {
        $dimensionsOldVsNew = [
            'small' => 'square_small',
            'medium' => 'square_medium',
            'large' => 'square_large',
        ];
        if (isset($dimensionsOldVsNew[$dimension])) {
            $dimension = $dimensionsOldVsNew[$dimension];
        }

        if (!is_null($this->communityLogo)) {
            if ($getPublicUrl) {
                $url = $this->communityLogo->getWebUrl($dimension, $absolute, true);
            } else {
                $url = $this->communityLogo->getUrl($dimension, $absolute, true);
            }
        } else {
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/img_default.jpg'));
        }
        return $url;
    }

    /**
     * Absolute Url of community avatar img (logo)
     *
     * @param string $dimension Size of the image. Default = small.
     * @return string $url
     */
    public function getAvatarWebUrl($dimension = 'small')
    {
        return $this->getAvatarUrl($dimension, true);
    }

    /**
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['communityLogo'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'minSize' => 1],
                [['communityCoverImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'minSize' => 1],
                [['backToEdit'], 'integer'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(),
            [
                'communityLogo' => AmosCommunity::t('amoscommunity', 'Logo')
            ]);
    }

    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
            'name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBaseRole()
    {
        $baseRole = CommunityUserMm::ROLE_PARTICIPANT;
        $moduleCommunity = Yii::$app->getModule('community');
        if ($moduleCommunity->extendRoles) {
            $baseRole = CommunityUserMm::ROLE_READER;
        }
        return $baseRole;
    }

    /**
     * @inheritdoc
     */
    public function getRolePermissions($role)
    {
        switch ($role) {
            case CommunityUserMm::ROLE_READER:
                return [];
                break;
            case CommunityUserMm::ROLE_GUEST:
                return [];
                break;
            case CommunityUserMm::ROLE_AUTHOR:
                return ['CWH_PERMISSION_CREATE'];
                break;
            case CommunityUserMm::ROLE_EDITOR:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            case CommunityUserMm::ROLE_PARTICIPANT:
                return ['CWH_PERMISSION_CREATE'];
                break;
            case CommunityUserMm::ROLE_COMMUNITY_MANAGER:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            default:
                return ['CWH_PERMISSION_CREATE'];
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getCommunityModel()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNextRole($role)
    {
        switch ($role) {
            case CommunityUserMm::ROLE_PARTICIPANT:
                return CommunityUserMm::ROLE_COMMUNITY_MANAGER;
                break;
            case CommunityUserMm::ROLE_COMMUNITY_MANAGER:
                return CommunityUserMm::ROLE_PARTICIPANT;
                break;
            default:
                return CommunityUserMm::ROLE_PARTICIPANT;
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
            [
                'workflow' => [
                    'class' => SimpleWorkflowBehavior::className(),
                    'defaultWorkflowId' => self::COMMUNITY_WORKFLOW,
                    'propagateErrorsToModel' => true,
                ],
                'NotifyBehavior' => [
                    'class' => NotifyBehavior::className(),
                    'conditions' => [],
                ],
                'fileBehavior' => [
                    'class' => FileBehavior::className()
                ],
                'WorkflowLogFunctionsBehavior' => [
                    'class' => WorkflowLogFunctionsBehavior::className(),
                ],
                'SeoContentBehavior' => [
                    'class' => SeoContentBehavior::className(),
                    'titleAttribute' => 'name',
                    'descriptionAttribute' => 'description',
                    'imageAttribute' => 'communityLogo',
                    'defaultOgType' => 'article',
                    'schema' => 'NewsArticle'
                ]
            ]);

        $cwhModule = Yii::$app->getModule('cwh');
        $tagModule = Yii::$app->getModule('tag');
        if (isset($cwhModule) && isset($tagModule)) {
            $cwhTaggable = [
                'interestingTaggable' => [
                    'class' => TaggableInterestingBehavior::className(),
                    'tagValueAttribute' => 'id',
                    'tagValuesSeparatorAttribute' => ',',
                    'tagValueNameAttribute' => 'nome',
                ]
            ];

            $behaviors = ArrayHelper::merge($behaviors, $cwhTaggable);
        }

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function getContextRoles()
    {
        $roles = [
            CommunityUserMm::ROLE_PARTICIPANT,
            CommunityUserMm::ROLE_COMMUNITY_MANAGER
        ];
        $moduleCommunity = Yii::$app->getModule('community');
        if ($moduleCommunity->extendRoles) {
            //add additional roles
            $roles = [
                CommunityUserMm::ROLE_READER,
                CommunityUserMm::ROLE_AUTHOR,
                CommunityUserMm::ROLE_EDITOR,
                CommunityUserMm::ROLE_COMMUNITY_MANAGER
            ];
        }
        return $roles;
    }

    /**
     *
     * @return string
     */
    public function getManagerRole()
    {
        $roleName = CommunityUserMm::ROLE_COMMUNITY_MANAGER;
        if (!empty($this->context) && strcmp($this->context, Community::className())) {
            /** @var CommunityContextInterface $obj */
            $obj = new $this->context;
            $roleName = $obj->getManagerRole();
        }
        return $roleName;
    }

    /**
     * @inheritdoc
     */
    public function getPluginModule()
    {
        return 'community';
    }

    /**
     * @inheritdoc
     */
    public function getPluginController()
    {
        return 'community';
    }

    /**
     * @inheritdoc
     */
    public function getRedirectAction()
    {
        return 'update';
    }

    public function getAdditionalAssociationTargetQuery($communityId)
    {
        $communityUserMms = CommunityUserMm::find()->andWhere(['community_id' => $communityId])->andWhere(['!=', 'role',
            CommunityUserMm::ROLE_GUEST]);
        return User::find()->andFilterWhere(['not in', 'id', $communityUserMms->select('user_id')]);
    }

    /**
     * Add CWH permissions based on the role for which a permissions array has been specified,
     * Remove CWH permissions on community domain in case of role degradation
     * or delete all permission in case of user-community association deletion
     *
     * @param CommunityUserMm $communityUserMmRow
     * @param bool|false $delete - if true remove all permission (case deletion user-community association)
     */
    public function setCwhAuthAssignments($communityUserMmRow, $delete = false)
    {

        /** @var AmosCwh $cwhModule */
        $cwhModule = Yii::$app->getModule("cwh");
        $moduleCommunity = Yii::$app->getModule('community');
        $cwhNodeId = 'community-' . $this->id;
        $userId = $communityUserMmRow->user_id;
        $cwhConfigId = self::getCwhConfigId();
        $cwhPermissions = CwhAuthAssignment::find()->andWhere([
            'user_id' => $userId,
            'cwh_config_id' => $cwhConfigId,
            'cwh_network_id' => $this->id
        ])->all();

        if ($delete) {
            if (!empty($cwhPermissions)) {
                /** @var CwhAuthAssignment $cwhPermission */
                foreach ($cwhPermissions as $cwhPermission) {
                    $cwhPermission->delete();
                }
            }
        } else {
            $existingPermissions = [];
            foreach ($cwhPermissions as $item) {
                $existingPermissions[$item->item_name] = $item;
            }

            /** @var Community $callingModel */
            $callingModel = Yii::createObject($this->context);
            /** @var array $rolePermissions */
            $rolePermissions = $callingModel->getRolePermissions($communityUserMmRow->role);
            $permissionsToAdd = [];
            if (!is_null($rolePermissions) && count($rolePermissions)) {
                // for each enabled Content model in Cwh
                foreach ($cwhModule->modelsEnabled as $modelClassname) {
                    foreach ($rolePermissions as $permission) {
                        $cwhAuthAssignment = new CwhAuthAssignment();
                        $cwhAuthAssignment->user_id = $userId;
                        $cwhAuthAssignment->item_name = $permission . '_' . $modelClassname;
                        $cwhAuthAssignment->cwh_nodi_id = $cwhNodeId;
                        $cwhAuthAssignment->cwh_config_id = $cwhConfigId;
                        $cwhAuthAssignment->cwh_network_id = $this->id;
                        $permissionsToAdd[$cwhAuthAssignment->item_name] = $cwhAuthAssignment;
                    }
                }
            }

            $enabledForContext = $moduleCommunity && in_array($this->context, $moduleCommunity->enableCwhAuthAssignmentContext);
            // if role is CM add permissions of creation/validation of subcommunities of current community (N.B. only for community not created by events/projects)
            if (($enabledForContext || $this->context == self::className()) && $communityUserMmRow->role == CommunityUserMm::ROLE_COMMUNITY_MANAGER) {
                $cwhCreateSubCommunities = new CwhAuthAssignment();
                $cwhCreateSubCommunities->user_id = $userId;
                $cwhCreateSubCommunities->item_name = $cwhModule->permissionPrefix . "_CREATE_" . self::className();
                $cwhCreateSubCommunities->cwh_nodi_id = $cwhNodeId;
                $cwhCreateSubCommunities->cwh_config_id = $cwhConfigId;
                $cwhCreateSubCommunities->cwh_network_id = $this->id;
                $permissionsToAdd[$cwhCreateSubCommunities->item_name] = $cwhCreateSubCommunities;
                $cwhValidateSubCommunities = new CwhAuthAssignment();
                $cwhValidateSubCommunities->user_id = $userId;
                $cwhValidateSubCommunities->item_name = $cwhModule->permissionPrefix . "_VALIDATE_" . self::className();
                $cwhValidateSubCommunities->cwh_nodi_id = $cwhNodeId;
                $cwhValidateSubCommunities->cwh_config_id = $cwhConfigId;
                $cwhValidateSubCommunities->cwh_network_id = $this->id;
                $permissionsToAdd[$cwhValidateSubCommunities->item_name] = $cwhValidateSubCommunities;
            }
            if (!empty($permissionsToAdd)) {
                /** @var CwhAuthAssignment $permissionToAdd */
                foreach ($permissionsToAdd as $key => $permissionToAdd) {
                    //if user has not already the permission for the community , add it to cwh auth assignment
                    if (!array_key_exists($key, $existingPermissions)) {
                        $permissionToAdd->save(false);
                    }
                }
            }
            // check if there are permissions to remove
            if (!empty($existingPermissions)) {
                /** @var CwhAuthAssignment $cwhPermission */
                foreach ($existingPermissions as $key => $cwhPermission) {
                    if (!array_key_exists($key, $permissionsToAdd)) {
                        $cwhPermission->delete();
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return Yii::$app->getUser()->id;
    }

    /**
     * @inheritdoc
     */
    public function getMmTableName()
    {
        return CommunityUserMm::tableName();
    }

    /**
     * @inheritdoc
     */
    public function getMmNetworkIdFieldName()
    {
        return 'community_id';
    }

    /**
     * @inheritdoc
     */
    public function getMmUserIdFieldName()
    {
        return 'user_id';
    }

    public function getMmClassName()
    {
        return CommunityUserMm::className();
    }

    /**
     * @inheritdoc
     */
    public function isNetworkUser($networkId = null, $userId = null, $onlyActiveStatus = false)
    {
        if (!isset($networkId)) {
            $networkId = $this->id;
        }
        if (!isset($userId)) {
            $userId = $this->getUserId();
        }
        $mmRow = CommunityUserMm::findOne([
            $this->getMmNetworkIdFieldName() => $networkId,
            $this->getMmUserIdFieldName() => $userId,
            'status' => CommunityUserMm::STATUS_ACTIVE
        ]);
        if (!is_null($mmRow)) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isValidated($networkId = null)
    {
        if (!isset($networkId)) {
            $community = $this;
        } else {
            $community = Community::findOne($networkId);
        }
        if (!isset($community) || $community->isNewRecord) {
            return false;
        }
        if ($community->status == self::COMMUNITY_WORKFLOW_STATUS_VALIDATED || ($community->validated_once && $community->visible_on_edit
                && $community->status == self::COMMUNITY_WORKFLOW_STATUS_DRAFT)) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE;
    }

    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_VALIDATED;
    }

    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_DRAFT;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'COMMUNITY_VALIDATOR';
    }

    /**
     * @param int|null $userId - if null the logged user id is considered
     * @return bool
     */
    public function isCommunityManager($userId = null)
    {
        if (!isset($userId)) {
            $userId = \Yii::$app->getUser()->id;
        }
        $managerRole = $this->getManagerRole();
        $communityMm = CommunityUserMm::findOne([
            'community_id' => $this->id,
            'role' => $managerRole,
            'status' => CommunityUserMm::STATUS_ACTIVE,
            'user_id' => $userId
        ]);
        return !is_null($communityMm);
    }

    /**
     * @param int|null $userId - if null the logged user id is considered
     * @return bool
     */
    public function hasRole($userId, $role)
    {
        if (!isset($userId)) {
            $userId = \Yii::$app->getUser()->id;
        }
        $communityMm = CommunityUserMm::findOne([
            'community_id' => $this->id,
            'role' => $role,
            'status' => CommunityUserMm::STATUS_ACTIVE,
            'user_id' => $userId
        ]);
        return !is_null($communityMm);
    }

    /**
     * @param null $userId
     * @param bool $isUpdate
     * @return string
     */
    public function getUserNetworkWidget($userId = null, $isUpdate = false)
    {
        if (is_null(Yii::$app->getModule(AmosCommunity::getModuleName()))) {
            return '';
        }
        return UserNetworkWidget::widget(['userId' => $userId, 'isUpdate' => $isUpdate]);
    }

    public static function getVisibility()
    {
        return "0";
    }

    public function getUserNetworkAssociationQuery($userId = null, $params = [], $onlyActiveStatus = false)
    {
        if (empty($userId)) {
            $userId = Yii::$app->user->id;
        }

        $communitySearch = new CommunitySearch();
        /** @var ActiveQuery $query */
        $query = $communitySearch->buildQuery($params, 'all', $onlyActiveStatus, $userId);

        /** @var ActiveQuery $queryJoined */
        $queryJoined = $this->getUserNetworkQuery($userId, $params, false)->select(static::tableName() . '.id')->column();
        if (!empty($queryJoined)) {
            $query->andWhere(['not in', static::tableName() . '.id', $queryJoined]);
        }
        $query->andWhere(static::tableName() . '.deleted_at is null');

        return $query;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getShortDescription()
    {
        return $this->__shortText($this->description, 100);
    }

    /**
     * @return string
     */
    public function getDescription($truncate)
    {
        $ret = $this->description;

        if ($truncate) {
            $ret = $this->__shortText($this->description, 200);
        }
        return $ret;
    }

    /**
     * @return string The url to view a single model
     */
    public function getViewUrl()
    {
        return "/community/join/open-join";
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new CommunityGrammar();
    }

    /**
     * @return array The columns ti show as default in GridViewWidget
     */
    public function getGridViewColumns()
    {
        // TODO: Implement getGridViewColumns() method.
        return [];
    }

    /**
     * @return string The classname of the generic dashboard widget to access the plugin
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconCommunityDashboard::className();
    }

    /**
     *
     * @return type
     */
    public function sendNotification()
    {
//    $ret = false;
//
//    if ($this->context == self::className()) {
//      $ret = true;
//    }
//
//    return $ret;

        return ($this->context == self::className());
    }

    /**
     * Get Id of configuration record for network model Community
     * @return int $cwhConfigId
     */
    public static function getCwhConfigId()
    {
        //default newtwork configuration id = 3 for community
        $cwhConfigId = self::COMMUNITY_DEFAULT_NETWORK_ID;
        $cwhConfig = CwhConfig::findOne(['tablename' => self::tableName()]);
        if (!is_null($cwhConfig)) {
            $cwhConfigId = $cwhConfig->id;
        }

        return $cwhConfigId;
    }

    /**
     *
     * @return boolean
     */
    public function hasSubNetworks()
    {
        return true;
    }

    /**
     * Query for communities in user network.
     * @param int|null $userId - if null the logged userId is considered.
     * @return ActiveQuery
     */
    public function getUserNetworkQuery($userId = null, $params = [], $onlyActiveStatus = false)
    {
        if (empty($userId)) {
            $userId = Yii::$app->user->id;
        }

        $communitySearch = new CommunitySearch();
        $query = $communitySearch->buildQuery($params, 'own-interest', $onlyActiveStatus, $userId);
        return $query->andWhere(['!=', CommunityUserMm::tableName() . '.role', CommunityUserMm::ROLE_GUEST]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityManagerMms()
    {
        return $this->getCommunityUserMms()
            ->andWhere([\open20\amos\community\models\CommunityUserMm::tableName() . '.status' => \open20\amos\community\models\CommunityUserMm::STATUS_ACTIVE])
            ->andWhere([\open20\amos\community\models\CommunityUserMm::tableName() . '.role' => \open20\amos\community\models\CommunityUserMm::ROLE_COMMUNITY_MANAGER]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityManagers()
    {
        return $this->hasMany(\open20\amos\core\user\User::className(), ['id' => 'user_id'])->via('communityManagerMms');
    }

    /**
     * Return the recordset of all recipients relative to the networks
     * associated to the relative user (they may be different? check it!)
     *
     * @param type $networkIds
     * @param type $usersId
     */
    public function getListOfRecipients($networkIds = [], $usersId = [])
    {
        $query = new \yii\db\Query();
        $query->select([
            "concat('" . Community::tableName() . "', '-', community_user_mm.community_id) AS objID",
            'community.id', 'community.name', 'community.status', 'validated_once', 'visible_on_edit',
            'community.created_by', 'community.deleted_at',
            'community_user_mm.id', 'community_user_mm.community_id', 'community_user_mm.community_id AS reference',
            'community_user_mm.status', 'community_user_mm.user_id', 'community_user_mm.deleted_at'
        ])
            ->from(static::tableName())
            ->leftJoin('community_user_mm',
                'community_user_mm.community_id = community.id
          AND community_user_mm.deleted_at IS NULL
          AND community_user_mm.status = \'' . CommunityUserMm::STATUS_ACTIVE . '\'')
            ->where(['community.id' => $networkIds])
            ->andWhere([
                'community_user_mm.user_id' => $usersId,
                'community.deleted_at' => null,
                'community.status' => self::COMMUNITY_WORKFLOW_STATUS_VALIDATED
            ]);

        return $query->all();
    }

    /**
     * @return bool
     */
    public function showParticipantWidget()
    {
        if ($this->getAmosWidgetsIcons()->count() > 0) {
            $count = $this->getAmosWidgetsIcons()->andWhere(['classname' => 'open20\amos\admin\widgets\icons\WidgetIconUserProfile'])->count();

            return $count > 0;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function showSubCommunityWidget()
    {
        if ($this->getAmosWidgetsIcons()->count() > 0) {
            $count = $this->getAmosWidgetsIcons()->andWhere(['classname' => 'open20\amos\community\widgets\icons\WidgetIconCommunityDashboard'])->count();
            return $count > 0;
        }
        return true;
    }

    /**
     *
     */
    public function saveDashboardCommunity()
    {
        $id = $this->id;
        $canPersonalize = \Yii::$app->user->can('COMMUNITY_WIDGETS_ADMIN_PERSONALIZE');

        $ids = \Yii::$app->request->post('amosWidgetsIds');
        $orderWidgets = [];
        if (isset($_POST['amosWidgetsIds'])) {
            if (isset($_POST['orderWidgets'])) {
                foreach ($_POST['orderWidgets'] as $order => $idWidget) {
                    $orderWidgets[$idWidget] = $order;
                }
            }
            if ($canPersonalize) {
                CommunityAmosWidgetsMm::deleteAll(['community_id' => $id]);
            } else {
                CommunityAmosWidgetsMm::deleteAll(['community_id' => $id, 'personalized' => 0]);
            }

            foreach ((array)$ids as $amos_widgets_id) {
                $communityWidget = new CommunityAmosWidgetsMm();
                $communityWidget->community_id = $id;
                $communityWidget->amos_widgets_id = $amos_widgets_id;
                $communityWidget->n_order = $orderWidgets[$amos_widgets_id] + 1;
                if ($canPersonalize) {
                    $communityWidget->personalized = 1;
                }
                $communityWidget->save(false);
            }
        };
    }

    public function isBslRegistered($userId)
    {
        $user = User::findOne($userId);
        if (!empty($user)) {
            $query = NotificationconfNetwork::find()
                ->joinWith('modelsClassname')
                ->andWhere(['models_classname.classname' => self::className()])
                ->andWhere(['notificationconf_network.record_id' => $this->id])
                ->andWhere(['notificationconf_network.user_id' => $user->id])
                ->andWhere(['!=', 'notificationconf_network.email', NotificationsConfOpt::EMAIL_OFF]);
            // VarDumper::dump( $query->createCommand()->rawSql, $depth = 10, $highlight = true);
            if (!empty($query->one())) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $id
     * @return ActiveQuery
     */
    public function getAssociationTargetQuery($id = null)
    {
        if (!is_null($id)) {
            $this->id = $id;
        }
        $userNetworkIds = $this->getCommunityUserMms()->select('community_user_mm.user_id')->column();

        /** @var ActiveQuery $userQuery */
        $userQuery = User::find()
            ->andFilterWhere(['not in', User::tableName() . '.id', $userNetworkIds])
            ->joinWith('userProfile')
            ->andWhere(['is not', UserProfile::tableName() . '.id', null])
            ->andWhere([User::tableName() . '.status' => User::STATUS_ACTIVE])
            ->andWhere([UserProfile::tableName() . '.attivo' => UserProfile::STATUS_ACTIVE])
            ->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);

        return $userQuery;
    }

    /**
     * @param Community $community
     * @return Community community
     */
    public static function getCommunityRoot($community)
    {
        if ($community->parent_id) {
            while ($community->parent_id) {
                $community = Community::findOne($community->parent_id);
            }
        }
        return $community;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function myCommunityUser()
    {
        return CommunityUserMm::find()
            ->andWhere(['community_id' => $this->id])
            ->andWhere(['user_id' => \Yii::$app->user->id])
            ->andWhere(['!=', 'role', CommunityUserMm::ROLE_GUEST])
            ->andWhere(['!=', 'status', CommunityUserMm::STATUS_GUEST])
            ->one();
    }

    /**
     *
     * @param type $user_id
     * @return type
     */
    public function getRoleByUser($user_id = null)
    {
        $isManager = $this->isCommunityManager($user_id);
        if ($isManager) {
            return $this->getManagerRole();
        } else {
            return $this->getBaseRole();
        }
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getRoleContextCommunity($communityRole = CommunityUserMm::ROLE_PARTICIPANT)
    {
        $role = $communityRole;
        $communityModelClassName = $this->communityModule->model('Community');
        if ($this->context != $communityModelClassName) {
            $callingModel = \Yii::createObject($this->context);
            $modelRoles = $callingModel::find(['community_id' => $this->id])->one();
            // se sono in una community con context quando invito devo
            if ($modelRoles && !empty($this->parent_id)) {
                if ($communityRole == CommunityUserMm::ROLE_COMMUNITY_MANAGER) {
                    $role = $modelRoles->getManagerRole();
                } else {
                    $role = $modelRoles->getBaseRole();
                }
            }
        }
        return $role;
    }

    /**
     * @inheritdoc
     */
    public function getPublicatedFrom()
    {
        return $this->created_at;
    }

    /** Ritorna gli ultimi N link
     * @return \yii\db\ActiveQuery
     */
    public function getSomeLinks()
    {
        return \open20\amos\community\models\Bookmarks::find()->andWhere([
            'community_id' => $this->id,
            'status' => \open20\amos\community\models\Bookmarks::BOOKMARKS_STATUS_PUBLISHED])
            ->orderBy('id DESC')
            ->limit(\open20\amos\community\models\Bookmarks::LINK_VIEW_NUMBER)
            ->all();
    }

    public function isSavedCommunityAmosDashoardMm()
    {
        return \open20\amos\community\models\CommunityAmosWidgetsMm::find()
            ->andWhere(['community_id' => $this->id])
            ->andWhere(['type' => \open20\amos\dashboard\models\AmosWidgets::TYPE_GRAPHIC])
            ->andWhere(['IS NOT', 'n_order', null])
            ->innerJoin('amos_widgets', 'amos_widgets.id = community_amos_widgets_mm.amos_widgets_id')->count();
    }

    public function getColorClassCommunityType()
    {
        $classCommunityType = '';
        if ($this->community_type_id == CommunityType::COMMUNITY_TYPE_OPEN) {
            $classCommunityType = 'community-open';
        } else if ($this->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED) {
            $classCommunityType = 'community-closed';
        } else if ($this->community_type_id == CommunityType::COMMUNITY_TYPE_PRIVATE) {
            $classCommunityType = 'community-private';
        }
        return $classCommunityType;
    }

    /**
     * @return bool
     */
    public function isSubChannel()
    {
        if (!empty($this->parent_id)) {
            $community = $this->parent;
            if ($community) {
                if (!empty($community->parent_id)) {
                    return true;
                }
            }
        }
        return false;
    }


    public function createMicrosoftTeam()
    {
        $msBuilder = new MsGraphBuilder();
        $msBuilder->assignLicenseToUser(\Yii::$app->user->id);
        if (!empty($this->parent_id)) {
            $parent = Community::getCommunityRoot($this);
            if ($parent && $parent->msgraph_team_id) {
                $channel = $msBuilder->createChannel($this, $parent->msgraph_team_id, $this->name, $this->description);
                $member = $msBuilder->getUserChannelMembership($parent->msgraph_team_id, $channel->id, \Yii::$app->user->id);
                $subscription = $msBuilder->subscribeToWebHookChannelMember($parent->msgraph_team_id);
                $subscription = $msBuilder->subscribeToWebHookDriveFolder($parent->msgraph_team_id, $this->msgraph_channel_id);

                //salvo folder id
                if ($this->enable_msgraph_teams && empty($this->msgraph_drive_id)) {
                    $parent = Community::getCommunityRoot($this);
                    if ($parent) {
                        $folder = $msBuilder->getChannelFolderInfo($parent->msgraph_team_id, $this->msgraph_channel_id);
                        $this->msgraph_drive_id = $folder->getDriveId();
                        $this->msgraph_folder_id = $folder->id;
                        $this->save(false);
                    }
                }

                $userMm = CommunityUserMm::find()->andWhere(['user_id' => \Yii::$app->user->id, 'community_id' => $this->id])->one();
                if ($member->id && $userMm) {
                    $userMm->msgraph_membership_id = $member->id;
                    $userMm->save(false);
                }
            }
        } else {
            $teamId = $msBuilder->createTeam($this->name, strip_tags($this->description));
            $channel = $msBuilder->getGeneralChannel($teamId);
            $member = $msBuilder->getUserChannelMembership($teamId, $channel->id, \Yii::$app->user->id);
            $subscription = $msBuilder->subscribeToWebHookTeamMember($teamId);
            $subscription = $msBuilder->subscribeToWebHookDriveFolder($teamId, $channel->id);


            if ($channel) {
                $this->msgraph_team_id = $teamId;
                $this->msgraph_channel_id = $channel->id;

                //salvo folder id
                if ($this->enable_msgraph_teams && empty($this->msgraph_driver_folder_id)) {
                    $parent = Community::getCommunityRoot($this);
                    if ($parent) {
                        $folder = $msBuilder->getChannelFolderInfo($parent->msgraph_team_id, $this->msgraph_channel_id);
                        $this->msgraph_drive_id = $folder->getDriveId();
                        $this->msgraph_folder_id = $folder->id;
                    }
                }
                $this->save(false);
            }
            $userMm = CommunityUserMm::find()->andWhere(['user_id' => \Yii::$app->user->id, 'community_id' => $this->id])->one();
            if ($member->id && $userMm) {
                $userMm->msgraph_membership_id = $member->id;
                $userMm->save(false);
            }
        }
    }

    /**
     * @return false|int|mixed
     */
    public function isEnableMsGraph()
    {
        $enableMsTeams = false;
        if (empty($this->parent_id)) {
            $enableMsTeams = $this->enable_msgraph_teams;
        } else {
            $parent = $this->parent;
            if ($parent) {
                $enableMsTeams = $parent->enable_msgraph_teams;
            }
        }
        return $enableMsTeams;
    }

    public function getMessageAlertMsTeams()
    {
        $communityRoot = Community::getCommunityRoot($this);
        $isTeamMember = false;
        $isAccountTeamsConnected = false;
        $isCommunityManager = $this->isCommunityManager(\Yii::$app->user->id);

        $moduleCommunity = \Yii::$app->getModule('community');
        if ($moduleCommunity && $moduleCommunity->enableMicrosoftTeams && $communityRoot->enable_msgraph_teams) {
            $title = '';
            $actionTeams = '';
            $tokenTeams = \open20\amos\community\models\CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
            $member = \open20\amos\community\models\CommunityUserMm::find()->andWhere([
                'user_id' => \Yii::$app->user->id, 'community_id' => $this->id,
                'status' => CommunityUserMm::STATUS_ACTIVE
            ])->one();
            if ($tokenTeams->msgraph_user_id) {
                $isAccountTeamsConnected = true;
            }
            if ($member && $member->msgraph_membership_id) {
                $isTeamMember = true;
            }
            if ($member) {
                if ($isCommunityManager && $communityRoot->enable_msgraph_teams && empty($communityRoot->msgraph_team_id)) {
                    $title = AmosCommunity::t('amoscommunity', 'Il Team di microsoft team non è collegato');
                    $actionTeams = AmosCommunity::t('amoscommunity', '<a href="{urlCreateTeam}">Crea</a> il team su Microsoft Teams.', [
                        'urlCreateTeam' => '/community/ms-teams/create-team-or-channel?communityId=' . $this->id,
                    ]);
                } else if ($communityRoot->msgraph_team_id && empty($this->msgraph_channel_id)) {
                    if (!empty($this->parent_id)) {
                        $title = AmosCommunity::t('amoscommunity', 'Il canale di microsoft team è stato eliminato');
                        $actionTeams = AmosCommunity::t('amoscommunity', 'Il canale continuerà a funzionare normalmente ma non interagirà più con Micrososft Teams.');
                       if($isCommunityManager) {
                           $actionTeams .= AmosCommunity::t('amoscommunity', 'Clicca <a href="{urlCreateTeam}">qui</a> per ricrearlo.', [
                               'urlCreateTeam' => '/community/ms-teams/create-team-or-channel?communityId=' . $this->id,
                           ]);
                       }
                    } else {
                        $title = AmosCommunity::t('amoscommunity', 'Il Team è stato eliminato');
                        $actionTeams = AmosCommunity::t('amoscommunity', 'La community continuerà a funzionare normalmente ma non interagirà più con Micrososft Teams');
                    }
                } else if (!$isAccountTeamsConnected) {
                    $title = AmosCommunity::t('amoscommunity', 'Abilita le funzionalità avanzate Microsoft con Open2.0');
                    $actionTeams = "<strong>" . Html::a(AmosCommunity::t('amoscommunity', 'Collega il tuo account Microsoft Teams'), ['/community/ms-teams/choose-mail-microsoft', 'urlRedirect' => Url::current()], ['title' => AmosCommunity::t('amoscomments', 'Collega il tuo account Microsoft')]) . "</strong>"
                        . ' ' . AmosCommunity::t('amoscommunity', 'per abilitare le funzionalità avanzate di Open 2.0, quali Canali Teams, TO-DO, File sharing e molto altro.');
                } else if (!$isTeamMember && $communityRoot->msgraph_team_id) {
                    $title = AmosCommunity::t('amoscommunity', 'Iscriviti al team per abilitare le funzionalità avanzate Microsoft con Open2.0');
                    $actionTeams = AmosCommunity::t('amoscommunity', 'Hai già collegato il tuo account Microsoft ma non fai parte di questo Team.') . ' '
                        . "<strong>" . Html::a(AmosCommunity::t('amoscommunity', 'Iscriviti ora'), ['/community/ms-teams/subscribe-to-team', 'community_id' => $this->id, 'urlRedirect' => Url::current()], ['title' => AmosCommunity::t('amoscomments', 'Iscriviti al team')]) . "</strong>";
                }
            }
            return [
                'textDescription' => $actionTeams,
                'title' => $title
            ];

        }
        return [];
    }

    /**
     * @param $user_id
     * @param $role
     * @return clients\msmodels\MsTeamMember|mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function addMemberToChannel($user_id, $role = CommunityUserMm::ROLE_PARTICIPANT, $createUser = true)
    {
        $moduleCommunity = \Yii::$app->getModule('community');
        if ($createUser) {
            $moduleCommunity->createCommunityUser($this->id, CommunityUserMm::STATUS_ACTIVE, $role, $user_id);
        }
        $communityUserMm = CommunityUserMm::find()->andWhere(['community_id' => $this->id, 'user_id' => $user_id])->one();
        if ($communityUserMm) {
            $parent = Community::getCommunityRoot($this);
            $msBuilder = new MsGraphBuilder();
            $msRole = CommunityUserMm::mapRoleMsTeams($role);
            $msTeamMember = $msBuilder->addMemberToChannel($communityUserMm, $parent->msgraph_team_id, $this->msgraph_channel_id, \Yii::$app->user->id, $msRole);
            return $msTeamMember;
        }
        return null;
    }

    public function deleteMsTeams()
    {
        $module = \Yii::$app->getModule('community');
        if ($module && $module->enableMicrosoftTeams) {
            $parent = Community::getCommunityRoot($this);
            if ($parent->enable_msgraph_teams) {
                $msBuilder = new MsGraphBuilder();
                if (empty($this->parent_id)) {
                    if ($this->msgraph_team_id) {
                        $msBuilder->deleteTeam($this->msgraph_team_id);
                    }
                } else {
                    if ($this->msgraph_channel_id) {
                        $msBuilder->deleteChannel($parent->msgraph_team_id, $this->msgraph_channel_id);
                    }
                }
            }
        }
    }

    /**
     * @param $member
     * @return void
     */
    public function addMemberToMicrosoftTeams($member)
    {
        $parent = Community::getCommunityRoot($this);
        if ($parent->enable_msgraph_teams) {
            $msBuilder = new MsGraphBuilder();
            if (empty($this->parent_id)) {
                $msTeamMember = $msBuilder->addMemberToTeam($member, $this->msgraph_team_id, $member->user_id);
            } else {
                $this->addMemberToChannel($member->user_id);
            }
        }
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \yii\base\InvalidConfigException
     */
    public function removeMemberMsTeamsFromCommunity($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = \Yii::$app->user->id;
        }
        $module = \Yii::$app->getModule('community');
        if ($module && $module->enableMicrosoftTeams) {
            $msBuilder = new MsGraphBuilder();
            $parent = Community::getCommunityRoot($this);
            if ($parent->enable_msgraph_teams) {
                $userMm = CommunityUserMm::find()->andWhere(['community_id' => $this->id, 'user_id' => $user_id])->one();
                if ($userMm && !empty($userMm->msgraph_membership_id)) {
                    if (!empty($this->parent_id)) {
                        $msBuilder->removeMemberFromChannel($parent->msgraph_team_id, $this->msgraph_channel_id, $userMm->msgraph_membership_id);
//                        \Yii::$app->session->addFlash('success', "Disiscritto da canale");
                    } else {
                        $msBuilder->removeMemberFromTeam($parent->msgraph_team_id, $userMm->msgraph_membership_id);
//                        \Yii::$app->session->addFlash('success', "Disiscritto da team");

                    }
                }
            }
        }
    }

    /**
     * @return void
     * @package open20\amos\core\record\Record
     */
    protected function setUseBullet()
    {
        parent::setUseBullet();

        // Don't update bullet count
        if ($this->context != $this::className() || $this->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED) {
            $this->useBullet = false;
        }
    }

}