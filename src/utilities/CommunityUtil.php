<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\utilities
 * @category   CategoryName
 */

namespace open20\amos\community\utilities;

use open20\amos\admin\models\UserProfile;
use open20\amos\community\AmosCommunity;
use open20\amos\community\exceptions\CommunityException;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityContextInterface;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\community\models\CommunityType;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\cwh\models\CwhAuthAssignment;
use open20\amos\news\models\News;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\log\Logger;
use open20\amos\core\user\User;

/**
 * Class CommunityUtil
 * @package open20\amos\community\utilities
 */
class CommunityUtil
{

    /**
     * This method translate the array values.
     * @param array $arrayValues
     * @return array
     */
    public static function translateArrayValues($arrayValues)
    {
        $translatedArrayValues = [];
        foreach ($arrayValues as $key => $value) {
            $translatedArrayValues[$key] = AmosCommunity::t('amoscommunity', $value);
        }
        return $translatedArrayValues;
    }

    /**
     * @param Community $model
     * @return bool
     */
    public function isManagerLoggedUser($model)
    {
        $foundRow = CommunityUserMm::findOne([
                'community_id' => $model->getCommunityModel()->id,
                'user_id' => \Yii::$app->getUser()->getId(),
                'role' => $model->getManagerRole()
        ]);
        return (!is_null($foundRow));
    }

    /**
     *
     * @param Community $model
     * @param string $field_community
     * @param integer $user_id
     * @return bool
     */
    public static function isManagerUser($model, $field_community = 'community_id', $user_id = null)
    {
        $foundRow = CommunityUserMm::findOne([
                'community_id' => $model->{$field_community},
                'user_id' => (empty($user_id) ? \Yii::$app->getUser()->getId() : $user_id),
                'role' => $model->getManagerRole()
        ]);
        return (!is_null($foundRow));
    }

    /**
     * Method useful to confirm a community manager. The method return true only if it found the manager
     * in the to confirm state and the update goes fine.
     * @param int $communityId
     * @param int $userId
     * @param string $managerRole
     * @return bool
     */
    public static function confirmCommunityManager($communityId, $userId, $managerRole)
    {
        if (is_numeric($communityId) && is_numeric($userId) && is_string($managerRole)) {
            $communityManagers = CommunityUserMm::find()->andWhere([
                    'community_id' => $communityId,
                    'user_id' => $userId,
                    'role' => $managerRole
                ])->all();
            if (count($communityManagers) == 1) {
                foreach ($communityManagers as $communityManager) {
                    /** @var CommunityUserMm $communityManager */
                    if ($communityManager->status == CommunityUserMm::STATUS_MANAGER_TO_CONFIRM) {
                        $communityManager->status = CommunityUserMm::STATUS_ACTIVE;
                        $ok                       = $communityManager->save(false);
                        if ($ok) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * This method return an array of Community objects representing all the communities of a specific user.
     * The list includes the communities validated at least once in the generic context (Community) and the
     * user must have the active status in CommunityUserMm.
     * @param int $userId
     * @param bool $onlyIds
     * @param bool $returnQuery
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public static function getCommunitiesByUserId($userId, $onlyIds = false, $returnQuery = false,
                                                  $ignoreValidatedOnce = false)
    {
        if (!is_numeric($userId) || ($userId <= 0)) {
            throw new CommunityException(AmosCommunity::t('amoscommunity',
                'getCommunitiesByUserId: userId is not a number or is not positive'));
        }
        /** @var ActiveQuery $query */
        $query = Community::find();
        $query->innerJoinWith('communityUserMms')
            ->andWhere([CommunityUserMm::tableName().'.user_id' => $userId])
            ->andWhere([CommunityUserMm::tableName().'.status' => CommunityUserMm::STATUS_ACTIVE])
            ->andWhere([Community::tableName().'.context' => Community::className()]);
        if (!$ignoreValidatedOnce) {
            $query->andWhere([Community::tableName().'.validated_once' => 1]);
        }
        if ($returnQuery) {
            return $query;
        }
        $communities = $query->all();
        if ($onlyIds) {
            $userCommunityIds = [];
            foreach ($communities as $community) {
                $userCommunityIds[] = $community->id;
            }
            return $userCommunityIds;
        }
        return $communities;
    }

    /**
     * @param int $userId
     * @param string $role
     * @param bool $onlyIds
     * @param bool $returnQuery IF true return the ActiveQuery object
     * @param bool $ignoreValidatedOnce
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public static function getCommunitiesByRoleAndUserId($userId, $role, $onlyIds = false, $returnQuery = false,
                                                         $ignoreValidatedOnce = false)
    {
        /** @var ActiveQuery $query */
        $query = self::getCommunitiesByUserId($userId, false, true, $ignoreValidatedOnce);
        $query->andWhere([CommunityUserMm::tableName().'.role' => $role]);
        if ($returnQuery) {
            return $query;
        }
        $communities = $query->all();
        if ($onlyIds) {
            $userCommunityIds = [];
            foreach ($communities as $community) {
                $userCommunityIds[] = $community->id;
            }
            return $userCommunityIds;
        }
        return $communities;
    }

    /**
     * @param int $userId
     * @param bool $onlyIds
     * @param bool $returnQuery IF true return the ActiveQuery object
     * @param bool $ignoreValidatedOnce
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public static function getCommunitiesManagedByUserId($userId, $onlyIds = false, $returnQuery = false,
                                                         $ignoreValidatedOnce = false)
    {
        return self::getCommunitiesByRoleAndUserId($userId, CommunityUserMm::ROLE_COMMUNITY_MANAGER, $onlyIds,
                $returnQuery, $ignoreValidatedOnce);
    }

    /**
     * @param int $userId
     * @param bool $onlyIds
     * @param bool $returnQuery IF true return the ActiveQuery object
     * @param bool $ignoreValidatedOnce
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public static function getCommunitiesParticipateByUserId($userId, $onlyIds = false, $returnQuery = false,
                                                             $ignoreValidatedOnce = false)
    {
        return self::getCommunitiesByRoleAndUserId($userId, CommunityUserMm::ROLE_PARTICIPANT, $onlyIds, $returnQuery,
                $ignoreValidatedOnce);
    }

    /**
     * Get the list of communities of which the user has permission to create subcommunities
     *
     * @param int|null $userId - if null the logged user id is considered
     * @param int|null $parentId - The only parent community to choose if a community creation process started from a the community with id $parentId
     * @return array [communityId] = community->name
     */
    public static function getParentList($userId = null, $parentId = null)
    {
        $communities = [];
        if (!isset($userId)) {
            $userId = \Yii::$app->getUser()->id;
        }
        $cwhModule = \Yii::$app->getModule('cwh');
        $classname = Community::className();

        $permissionCreateName   = $cwhModule->permissionPrefix."_CREATE_".$classname;
        $cwhAuthTable           = CwhAuthAssignment::tableName();
        /** @var ActiveQuery $parentCommunitiesQuery */
        $parentCommunitiesQuery = Community::find()
            ->innerJoin($cwhAuthTable,
                $cwhAuthTable.'.cwh_config_id = '.Community::getCwhConfigId().' AND cwh_network_id = community.id')
            ->andWhere([
            'item_name' => $permissionCreateName,
            'user_id' => $userId,
            'context' => Community::className()
        ]);
        if (!is_null($parentId)) {
            $parentCommunitiesQuery->andWhere(['community.id' => $parentId]);
        }
        $parentCommunities = $parentCommunitiesQuery->all();
        if (count($parentCommunities)) {
            $communities = ArrayHelper::map($parentCommunities, 'id', 'name');
        }
        return $communities;
    }

    /**
     * This method return a string array of community context classnames present in the community table.
     * @return array
     */
    public static function getAllCommunityContexts()
    {
        $managerQuery = new Query();
        $managerQuery->select('context');
        $managerQuery->from(Community::tableName());
        $managerQuery->groupBy('context');
        $contexts     = $managerQuery->column();
        return $contexts;
    }

    /**
     * This method return a string array of community managers of all community contexts.
     * @return array
     */
    public static function getAllCommunityManagerRoles()
    {
        $contexts              = self::getAllCommunityContexts();
        $communityManagerRoles = [];
        foreach ($contexts as $context) {
            $context                 = '\\'.$context;
            /** @var \open20\amos\community\models\CommunityContextInterface $model */
            $model                   = new $context();
            $communityManagerRoles[] = $model->getManagerRole();
        }
        return $communityManagerRoles;
    }

    /**
     * @param Community $community
     * @return int[]
     */
    public static function getCommunityAndSubcommunitiesIds($community)
    {
        return array_unique(self::getCommunityAndSubcommunitiesIdsRecursive($community));
    }

    /**
     * @param Community $community
     * @return int[]
     */
    public static function getCommunityAndSubcommunitiesIdsRecursive($community)
    {
        $subCommunities = $community->subcommunities;
        $communityIds   = [$community->id];

        if (!empty($subCommunities)) {
            foreach ($subCommunities as $subCommunity) {
                $communityIds[] = $subCommunity->id;
            }
            foreach ($subCommunities as $subCommunity) {
                $communityIds = ArrayHelper::merge($communityIds,
                        self::getCommunityAndSubcommunitiesIdsRecursive($subCommunity));
            }
        }

        return $communityIds;
    }

    /**
     * @return Community[]
     */
    public static function getAllValidatedCommunity()
    {
        return Community::find()
                ->andWhere(['parent_id' => null])
                ->andWhere(['context' => Community::className()])
                ->andWhere(['status' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED])
                ->all();
    }

    /**
     * Return CommunityUserMms of current community
     * @return ActiveQuery
     */
    public static function getCurrentCommunityMembers()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        $query     = CommunityUserMm::find()->andWhere(0);
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $community = Community::findOne($scope['community']);
                $query     = $community->getCommunityUserMms();
            }
        }
        return $query;
    }


    /**
     * Return Community info of current community
     * @return ActiveQuery
     */
    public static function getCommunityTypeConfigs($community)
    {
        $array_info = [];
        $isChannel = false;
        $isSubChannel = false;
        if($community->parent_id){
            $isChannel = true;
            $parent = Community::findOne($community->parent_id);
            if($parent->parent_id){
                $isSubChannel = true;
            }
        }
        switch ($community->community_type_id):
            case CommunityType::COMMUNITY_TYPE_CLOSED:
                $testo = AmosCommunity::t('amoscommunity', 'Community ristretta ai partecipanti');
                if($isSubChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Sottocanale ristretto ai partecipanti');
                }elseif($isChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Canale ristretto ai partecipanti');
                }
                $array_info = [
                    'classType' => 'closed',
                    'textCommunityType' => $testo,
                    'iconCommunityType' => 'mdi mdi-eye-off',
                ];
                break;
            case CommunityType::COMMUNITY_TYPE_OPEN:
                $testo = AmosCommunity::t('amoscommunity', 'Community aperta');
                if($isSubChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Sottocanale aperto');
                }elseif($isChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Canale aperto');
                }
                $array_info = [
                    'classType' => 'open',
                    'textCommunityType' => $testo,
                    'iconCommunityType' => 'mdi mdi-lock-open-outline',
                ];
                break;
            case CommunityType::COMMUNITY_TYPE_PRIVATE:
                $testo = AmosCommunity::t('amoscommunity', 'Community riservata ai partecipanti');
                if($isSubChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Sottocanale riservato ai partecipanti');
                }elseif($isChannel){
                    $testo = AmosCommunity::t('amoscommunity', 'Canale riservato ai partecipanti');
                }
                $array_info = [
                    'classType' => 'private',
                    'textCommunityType' => $testo,
                    'iconCommunityType' => 'mdi mdi-lock',
                ];
                break;
            default:
                $array_info = [
                    'classType' => '',
                    'textCommunityType' => '',
                    'iconCommunityType' => 'am am-accounts-alt',
                ];
        endswitch;

        return $array_info;
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isLoggedCommunityManager()
    {
        $cwhModule = \Yii::$app->getModule('cwh');
        if (isset($cwhModule)) {
            $scope = $cwhModule->getCwhScope();
            if (!empty($scope) && isset($scope['community'])) {
                $model = Community::findOne($scope['community']);
                return CommunityUtil::hasRole($model);
            }
        }
        return false;
    }

    /**
     *
     * @return boolean
     */
    public static function isLoggedCommunityParticipant()
    {
        $cwhModule = \Yii::$app->getModule('cwh');
        if (isset($cwhModule) && !empty($cwhModule->getCwhScope())) {
            $scope = $cwhModule->getCwhScope();
            if (isset($scope['community'])) {
                $model = Community::findOne($scope['community']);
                return CommunityUtil::hasRole($model,
                        [CommunityUserMm::ROLE_COMMUNITY_MANAGER, CommunityUserMm::ROLE_READER, CommunityUserMm::ROLE_AUTHOR,
                        CommunityUserMm::ROLE_EDITOR, CommunityUserMm::ROLE_PARTICIPANT]);
            }
        }

        return false;
    }

    /**
     * Get Object of community_user_mm if is not guest user
     * @return CommunityUserMm
     */
    public static function getMemberCommunityLogged($id)
    {
        $communityUser = null;
        if (!\Yii::$app->user->isGuest) {
            $communityUser = CommunityUserMm::find()
                ->andWhere(['community_id' => $id])
                ->andWhere(['user_id' => \Yii::$app->user->id])
                ->andWhere(['!=', 'status', CommunityUserMm::STATUS_GUEST])
                ->one();
        }
        return $communityUser;
    }

    /**
     *
     * @param type $model
     * @param string|array $role
     * @return boolean
     */
    public static function hasRole($model, $role = null)
    {
        if (empty($role)) {
            $role = CommunityUserMm::ROLE_COMMUNITY_MANAGER;
        }
        $communityUserMm = CommunityUserMm::find()
            ->andWhere(['user_id' => \Yii::$app->user->id])
            ->andWhere(['community_id' => $model->id])
            ->andWhere(['role' => $role])
            ->one();
        if (!empty($communityUserMm)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param integer $communityId
     * @return string or null
     */
    public static function getRole($communityId)
    {
        $communityUserMm = CommunityUserMm::find()
            ->andWhere(['user_id' => \Yii::$app->user->id])
            ->andWhere(['community_id' => $communityId])
            ->one();
        if (!empty($communityUserMm)) {
            return $communityUserMm->role;
        }
        return null;
    }

    /**
     * @param Community $community
     * @return bool
     */
    public static function canDeleteCommunity($community)
    {
        if (
            is_null($community) ||
            !is_object($community) ||
            (!($community instanceof Community)) ||
            $community->isNewRecord ||
            ($community->status != Community::COMMUNITY_WORKFLOW_STATUS_DRAFT) ||
            !self::loggedUserIsCommunityManager($community->id)
        ) {
            return false;
        }

        /** @var AmosCommunity $communityModule */
        $communityModule = \Yii::$app->getModule(AmosCommunity::getModuleName());

        // Check if is enabled the delete of the community subcommunities. If not, checks if there's subcommunities in the community.
        if (!$communityModule->deleteCommunityWithSubcommunities && (count($community->subcommunities))) {
            return false;
        }

        // Check if is enabled the delete of the community contents. If not, checks if there's contents in the community.
        if (!$communityModule->deleteCommunityWithContents) {
            $cwhModule = \Yii::$app->getModule('cwh');
            if (!is_null($cwhModule)) {
                /** @var \open20\amos\cwh\AmosCwh $cwhModule */
                $communityContents = \open20\amos\cwh\utility\CwhUtil::getNetworkContents(Community::getCwhConfigId(),
                        $community->id);
                if (count($communityContents) > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This method checks if the logged user is a community manager for the community in param.
     * @param int $communityId
     * @param int $userId
     * @return bool
     */
    public static function userIsCommunityManager($communityId, $userId)
    {
        $communityIds = self::getCommunitiesManagedByUserId($userId, true, false, true);
        return (in_array($communityId, $communityIds));
    }

    /**
     * This method checks if the logged user is a community manager for the community in param.
     * @param int $communityId
     * @return bool
     */
    public static function loggedUserIsCommunityManager($communityId)
    {
        return self::userIsCommunityManager($communityId, \Yii::$app->user->id);
    }

    /**
     * This method checks if the logged user is a participant for the community in param.
     * @param int $communityId
     * @param int $userId
     * @return bool
     * @throws CommunityException
     */
    public static function userIsParticipant($communityId, $userId)
    {
        $communityIds = self::getCommunitiesParticipateByUserId($userId, true, false, true);
        return (in_array($communityId, $communityIds));
    }

    /**
     * This method checks if the logged user is a participant for the community in param.
     * @param int $communityId
     * @return bool
     * @throws CommunityException
     */
    public static function loggedUserIsParticipant($communityId)
    {
        return self::userIsParticipant($communityId, \Yii::$app->user->id);
    }

    /**
     * @param int $communityId
     * @param int $userId
     * @return bool
     */
    public static function userIsSignedUp($communityId, $userId)
    {
        $query       = new Query();
        $query->from(CommunityUserMm::tableName());
        $query->andWhere([CommunityUserMm::tableName().'.community_id' => $communityId,
            CommunityUserMm::tableName().'.user_id' => $userId,
            CommunityUserMm::tableName().'.deleted_at' => null]);
        $partecipant = $query->all();
        return (!is_null($partecipant)) && count($partecipant);
    }

    /**
     * Returns an array with community types ready to be rendered in a select.
     * @return array
     */
    public static function getCommunityTypeReadyForSelect()
    {
        $module = \Yii::$app->getModule('community');
        $query = CommunityType::find();
        if(!empty($module->enableCommunityTypes)){
            $query->andWhere(['community_types.id' => $module->enableCommunityTypes]);
        }
        return self::translateArrayValues(ArrayHelper::map($query->asArray()->all(), 'id', 'name'));
    }

    /**
     * This method checks if the user is member for the community in param.
     * @param int $communityId
     * @return bool
     * @throws CommunityException
     */
    public static function userIsCommunityMember($communityId, $userId)
    {
        $count = CommunityUserMm::find()
                ->andWhere(['user_id' => $userId])
                ->andWhere(['community_id' => $communityId])
                ->andWhere(['not in', 'status', [CommunityUserMm::STATUS_REJECTED, CommunityUserMm::STATUS_GUEST]])->count();
        return ($count > 0);
    }

    /**
     * This method check if the user is membre active for the community
     * @param $communityId
     * @param $userId
     * @return bool|int
     * @throws \yii\base\InvalidConfigException
     */
    public static function userIsCommunityMemberActive($communityId, $userId = null)
    {
        if(\Yii::$app->user->isGuest && empty($userId)){
            return 0;
        }
        if(empty($userId)){
            $userId = \Yii::$app->user->id;
        }
        $count = CommunityUserMm::find()
                ->andWhere(['user_id' => $userId])
                ->andWhere(['community_id' => $communityId])
                ->andWhere(['status' => CommunityUserMm::STATUS_ACTIVE])->count();
        return ($count > 0);
    }

    /**
     *
     * @param type $tablenameContent
     * @return type
     */
    public static function addCwhForCommunity($tablenameContent, $idcommunity)
    {

        $querycwhpubb = "SELECT content_id
			FROM `cwh_pubblicazioni` cp
                        JOIN `cwh_config_contents` ccc 
                        ON cp.`cwh_config_contents_id` = ccc.`id`
			AND ccc.`tablename` like '".$tablenameContent."' WHERE cp.`id` in (
                            SELECT a.`cwh_pubblicazioni_id` 
                            FROM `cwh_pubblicazioni_cwh_nodi_editori_mm` a 
                            JOIN `cwh_config` b 
                            on a.`cwh_config_id` = b.`id` and b.`tablename` like '".Community::tableName()."' 
                            where a.`cwh_network_id` = ".$idcommunity.")";

        $paramsId = \Yii::$app->getDb()->createCommand($querycwhpubb)->queryAll();
        $ids      = [];
        foreach ($paramsId as $param) {
            $ids[] = $param['content_id'];
        }
        return $ids;
    }

    /**
     * @param int $idcommunity
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function communityNews($idcommunity)
    {

        $paramsId = self::addCwhForCommunity(News::tableName(), $idcommunity);

        $news = News::find()
                ->andWhere([
                    'status' => News::NEWS_WORKFLOW_STATUS_VALIDATO,
                ])->andWhere(['in', 'id', $paramsId])->limit(3)->all();

        return $news;
    }

    /**
     * This method auto add all the users with module configured roles as community manager for the specified community.
     * You can specify the manager status (default to already active).
     * @param Community|CommunityContextInterface $community
     * @param string $managerStatus
     * @param AmosCommunity|null $communityModule
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws CommunityException
     */
    public static function autoAddCommunityManagersToCommunity($community,
                                                               $managerStatus = CommunityUserMm::STATUS_ACTIVE,
                                                               $communityModule = null)
    {
        if ($community->isDeleted()) {
            return true;
        }

        if (is_null($communityModule)) {
            $communityModule = AmosCommunity::instance();
        }

        $allOk = true;

        if (!is_null($communityModule) && is_array($communityModule->autoCommunityManagerRoles) && !empty($communityModule->autoCommunityManagerRoles)) {
            $userIdsToAddAsManager = self::getUserIdsToAddAsManager($communityModule->autoCommunityManagerRoles);
            $managerRole           = self::getManagerRoleToAutoAddAsManager($community);
            if (strlen($managerRole) > 0) {
                $alreadyManagerUserIds = self::getAlreadyManagerForACommunityUserIds($community->id, $managerRole);
                $userIdsToAddAsManager = array_diff($userIdsToAddAsManager, $alreadyManagerUserIds);
                foreach ($userIdsToAddAsManager as $userId) {
                    $ok = $communityModule->createCommunityUser($community->id, $managerStatus, $managerRole, $userId);
                    if (!$ok) {
                        $allOk = false;
                        \Yii::getLogger()->log(AmosCommunity::t('amoscommunity', '#error_auto_add_community_manager',
                                [
                                'userId' => $userId,
                                'communityId' => $community->id
                            ]), Logger::LEVEL_ERROR);
                    }
                }
            }
        }

        return $allOk;
    }

    /**
     * @param string[] $autoCommunityManagerRoles
     * @return array
     */
    protected static function getUserIdsToAddAsManager($autoCommunityManagerRoles)
    {
        $userIdsToAddAsManager = [];
        foreach ($autoCommunityManagerRoles as $platformRole) {
            if (is_string($platformRole)) {
                $roleUserIds           = \Yii::$app->authManager->getUserIdsByRole($platformRole);
                $userIdsToAddAsManager = array_merge($userIdsToAddAsManager, $roleUserIds);
            }
        }
        $userIdsToAddAsManager = array_unique($userIdsToAddAsManager);
        return $userIdsToAddAsManager;
    }

    /**
     * @param Community|CommunityContextInterface $community
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getManagerRoleToAutoAddAsManager($community)
    {
        $managerRole = '';
        if ($community->context == Community::className()) {
            $managerRole = $community->getManagerRole();
        } else {
            $contextObject = \Yii::createObject($community->context);
            if ($contextObject instanceof CommunityContextInterface) {
                $managerRole = $contextObject->getManagerRole();
            }
        }
        return $managerRole;
    }

    /**
     * @param int $communityId
     * @param string $managerRole
     * @return array
     */
    public static function getAlreadyManagerForACommunityUserIds($communityId, $managerRole)
    {
        $query                 = new Query();
        $query->select(['user_id'])
            ->from(CommunityUserMm::tableName())
            ->andWhere([
                'deleted_at' => null,
                'role' => $managerRole,
                'community_id' => $communityId,
            ])
            ->andWhere(['<>', 'status', CommunityUserMm::STATUS_REJECTED]);
        $alreadyManagerUserIds = $query->column();
        return $alreadyManagerUserIds;
    }

    /**
     * Recupera la community (o solo il suo id) legata al documento il cui id e'
     * passato al metodo
     * @param int $idDoc Id del docuemnto di cui recuperare la community
     * @param bool $onlyId Se true verra' restituito solo l'id della community;
     * altrimenti verra' restituito il model della community
     * @return Community|bool Restituisce il model della community legata al
     * documento o false nel caso non sia presente o non sia possibile
     * recuperare tale informazione
     * @throws CommunityException Se non viene passato al metodo l'id del
     * documento di cui recuperare la community o non e' presente il model Amos
     * Documenti
     */
    public static function getCommunityByIdDocumento($idDoc, $onlyId = false)
    {
        if (empty($idDoc)) {
            throw new CommunityException(AmosCommunity::t('amoscommunity',
                'getCommunityByIdFile: idFile non presente o non valido'));
        }
        if (!\Yii::$app->hasModule('documenti')) {
            throw new CommunityException(AmosCommunity::t('amoscommunity',
                'getCommunityByIdFile: Amos Documenti non è presente in'
                . 'piattaforma'));
        }
        $cwhModule = \Yii::$app->getModule('cwh');
        if (empty($cwhModule)) {
            return false;
        }
        $modelConfig = \open20\amos\cwh\models\CwhConfig::findOne(
            ['tablename' => Community::tableName()]);
        if (empty($modelConfig)){
            return false;
        }
        $modelConfigContent = \open20\amos\cwh\models\base\CwhConfigContents
            ::findOne(['tablename' => \open20\amos\documenti\models\Documenti::tableName()]);
        if (empty($modelConfigContent)){
            return false;
        }
        $modelPubblicazioni = \open20\amos\cwh\models\CwhPubblicazioni::find()
            ->andWhere(['cwh_config_contents_id' => $modelConfigContent->id])
            ->andWhere(['content_id' => $idDoc])
            ->one();
        if (empty($modelPubblicazioni)) {
            return false;
        }
        $modelNodiValidatoriMm = \open20\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm::find()
            ->andWhere(['cwh_pubblicazioni_id' => $modelPubblicazioni->id])
            ->andWhere(['cwh_config_id' => $modelConfig->id])
            ->one();
        if (empty($modelNodiValidatoriMm)) {
            return false;
        }
        if (!empty($onlyId)) {
            return $modelNodiValidatoriMm->cwh_network_id;
        }
        return Community::findOne($modelNodiValidatoriMm->cwh_network_id);
    }



    /**
     * This method get the susbribtion status of the logged user for a given community
     *
     * @param Community|CommunityContextInterface $community
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws CommunityException
     */


    public static function getUserCommunitySubscriptionStatus($community){
        $iscritto = '';
        $isOpenCommunity    = false;
        $isClosedCommunity  = false;
        $isPrivateCommunity = false;
        $isWaitingToSigned  = false;
        $isInvited          = false;
        switch($community->community_type_id){
            case CommunityType::COMMUNITY_TYPE_OPEN: $isOpenCommunity = true; break;
            case CommunityType::COMMUNITY_TYPE_CLOSED: $isClosedCommunity = true; break;
            case CommunityType::COMMUNITY_TYPE_PRIVATE: $isPrivateCommunity = true; break;
        }

        if (!\Yii::$app->user->isGuest) {
            $loggedUserId  = \Yii::$app->getUser()->getId();
            $userCommunity = CommunityUtil::getMemberCommunityLogged($community->id);
            $userProfile   = User::findOne($loggedUserId)->getProfile();
            if (!empty($userProfile) && $userProfile->validato_almeno_una_volta && !is_null($userCommunity)) {
                if (in_array($userCommunity->status, [CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER, CommunityUserMm::STATUS_WAITING_OK_USER])) {
                    $isWaitingToSigned = true;
                } else {
                    $isSigned = true;
                }
            } else {
                $isSigned = false;
            }
            $isCreatorCommunity = ($community->created_by == \Yii::$app->user->id) && !\Yii::$app->user->can("ADMIN");
            if ($isSigned){
                if (!$isCreatorCommunity) {
                    $iscritto = AmosCommunity::t('amoscommunity', 'Sei gia iscritto');
                }
            }else{
                if ($userProfile && $userProfile->validato_almeno_una_volta) {
                    if ($isPrivateCommunity){
                        if ($isWaitingToSigned){
                            $iscritto = AmosCommunity::t('amoscommunity', 'Sei stato invitato');
                        }elseif ($isInvited){
                            $iscritto = AmosCommunity::t('amoscommunity', 'Sei stato invitato nel canale');
                        }
                    }else{
                       if ($isWaitingToSigned || $isInvited){
                            $iscritto = AmosCommunity::t('amoscommunity', 'Sei stato invitato nel canale');
                        }
                    }
                }else{
                    $iscritto = AmosCommunity::t('amoscommunity', 'Devi essere prima validato');
                }
            }
        }

        return $iscritto;
    }


    public static function getCommunityScope(){
        $communityId = '';
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $communityId = $scope['community'];
            }
        }
        return $communityId;
    }


    public static function SubcommToChannelTrad($string){
        $moduleCommunity = \Yii::$app->getModule('community');
        $array_translation = [];
        if($moduleCommunity->enableNewCommunityHeader){
            $array_translation = [
                'Visualizza tutte le community' => AmosCommunity::t('amoscommunity', 'Visualizza tutti i canali'),
                'Visualizza le community create da me' => AmosCommunity::t('amoscommunity', 'Visualizza i canali creati da me'),
                'Create da me' => AmosCommunity::t('amoscommunity', 'Creati da me'),
                'Tutte' => AmosCommunity::t('amoscommunity', 'Tutti'),
                'Le mie community' => AmosCommunity::t('amoscommunity', 'I miei canali'),
                'Visualizza community da validare' => AmosCommunity::t('amoscommunity', 'Visualizza canali da validare'),
                'Amministra tutte le community' => AmosCommunity::t('amoscommunity', 'Amministra tutti i canali'),
                'Visita la community' => AmosCommunity::t('amoscommunity', 'Visita il canale'),
                'Tutte le sottocommunity' => AmosCommunity::t('amoscommunity', 'Tutti i canali'),
                'Visualizza la lista delle sottocommunity' => AmosCommunity::t('amoscommunity', 'Visualizza la lista dei canali'),
                'Community ristretta ai partecipanti' => AmosCommunity::t('amoscommunity', 'Canale ristretto ai partecipanti'),
                'Community aperta' => AmosCommunity::t('amoscommunity', 'Canale aperto'),
                '#new_subcommunity' => AmosCommunity::t('amoscommunity', 'Nuovo canale'),
                'Community riservata ai partecipanti' => AmosCommunity::t('amoscommunity', 'Canale riservato ai partecipanti'),
                'Visita il sottocanale' => AmosCommunity::t('amoscommunity', 'Visita il sottocanale'),
                'Canale della community' => AmosCommunity::t('amoscommunity', 'Canale della community'),
                'Inserisci descrizione del canale' => AmosCommunity::t('amoscommunity', 'Inserisci descrizione del canale'),
                'Nome canale' => AmosCommunity::t('amoscommunity', 'Nome canale'),
                'Subcommunities' => AmosCommunity::t('amoscommunity', 'Canali'),
                'Le mie sottocommunity' => AmosCommunity::t('amoscommunity', 'I miei canali'),
                'I miei canali' =>AmosCommunity::t('amoscommunity', 'I miei canali'),
                '#subcommunities_in' =>AmosCommunity::t('amoscommunity', 'Canali in '),
                '#subcommunity_mini_widget_new_form' => AmosCommunity::t('amoscommunity', 'Sarà possibile creare un canale solo dopo aver salvato'),
                "Iscriviti alla community per visualizzarne i contenuti" => AmosCommunity::t('amoscommunity', 'Iscriviti al canale per visualizzarne i contenuti'),
            ];
        }else{
            $array_translation = [
                'VIsita il sottocanale' =>AmosCommunity::t('amoscommunity', 'Visita la community'),
                'Inserisci descrizione del canale' =>AmosCommunity::t('amoscommunity', 'Insert the community description'),
                'Nome canale' =>AmosCommunity::t('amoscommunity', 'Nome canale'),
                'I miei canali' =>AmosCommunity::t('amoscommunity', 'Le mie community'),
            ];
        }
        return ($array_translation[$string] ? $array_translation[$string] : AmosCommunity::t('amoscommunity', $string));
    }

    public static function canUseMsTeamsFeatures(){
            $profile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
            $isConnected = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->count();

            if ($profile && $profile->enable_msgraph && $isConnected) {
                return true;
            }
        return false;
    }

    /**
     * @return string
     */
    public static function showIconSvgTeams(){
        $html = <<<HTML
  <svg xmlns="http://www.w3.org/2000/svg" width="69" height="65" viewBox="0 0 69 65" fill="none">
            <g clip-path="url(#clip0_2201_11182)">
                <path d="M48.1281 24.375H65.951C67.6348 24.375 68.9998 25.7573 68.9998 27.4625V43.9026C68.9998 50.1696 63.983 55.25 57.7945 55.25H57.7416C51.553 55.2509 46.5356 50.1712 46.5347 43.9042C46.5347 43.9037 46.5347 43.9032 46.5347 43.9026V25.9886C46.5347 25.0974 47.2481 24.375 48.1281 24.375Z" fill="#5059C9" />
                <path d="M60.1745 21.125C64.1626 21.125 67.3955 17.8511 67.3955 13.8125C67.3955 9.77392 64.1626 6.5 60.1745 6.5C56.1865 6.5 52.9536 9.77392 52.9536 13.8125C52.9536 17.8511 56.1865 21.125 60.1745 21.125Z" fill="#5059C9" />
                <path d="M37.7095 21.125C43.47 21.125 48.1398 16.396 48.1398 10.5625C48.1398 4.729 43.47 0 37.7095 0C31.9491 0 27.2793 4.729 27.2793 10.5625C27.2793 16.396 31.9491 21.125 37.7095 21.125Z" fill="#7B83EB" />
                <path d="M51.617 24.375H22.1973C20.5335 24.4167 19.2174 25.815 19.256 27.4999V46.2508C19.0236 56.3619 26.9229 64.7526 36.9071 65C46.8914 64.7526 54.7906 56.3619 54.5583 46.2508V27.4999C54.5969 25.815 53.2808 24.4167 51.617 24.375Z" fill="#7B83EB" />
                <path opacity="0.1" d="M38.5116 24.375V50.6513C38.5036 51.8562 37.7825 52.9387 36.6823 53.3975C36.3319 53.5476 35.9554 53.6249 35.575 53.625H20.6678C20.4592 53.0888 20.2667 52.5525 20.1062 52C19.5445 50.1354 19.2579 48.197 19.2557 46.2475V27.495C19.2171 25.8128 20.5311 24.4167 22.1922 24.375H38.5116Z" fill="black" />
                <path opacity="0.2" d="M36.9069 24.375V52.2762C36.9069 52.6614 36.8305 53.0427 36.6823 53.3975C36.2292 54.5117 35.1603 55.2419 33.9704 55.25H21.422C21.1492 54.7137 20.8925 54.1775 20.6678 53.625C20.4432 53.0725 20.2667 52.5525 20.1062 52C19.5445 50.1354 19.2579 48.197 19.2557 46.2475V27.495C19.2171 25.8128 20.5311 24.4167 22.1922 24.375H36.9069Z" fill="black" />
                <path opacity="0.2" d="M36.9069 24.375V49.0263C36.8947 50.6635 35.5871 51.9876 33.9704 52H20.1062C19.5445 50.1354 19.2579 48.197 19.2557 46.2475V27.495C19.2171 25.8128 20.5311 24.4167 22.1922 24.375H36.9069Z" fill="black" />
                <path opacity="0.2" d="M35.3022 24.375V49.0263C35.29 50.6635 33.9824 51.9876 32.3657 52H20.1062C19.5445 50.1354 19.2579 48.197 19.2557 46.2475V27.495C19.2171 25.8128 20.5311 24.4167 22.1922 24.375H35.3022Z" fill="black" />
                <path opacity="0.1" d="M38.5116 15.9738V21.0925C38.2388 21.1088 37.9821 21.125 37.7093 21.125C37.4365 21.125 37.1797 21.1088 36.9069 21.0925C36.3653 21.0561 35.8281 20.9691 35.3023 20.8325C32.0529 20.0533 29.3683 17.7455 28.0814 14.6251C27.8599 14.101 27.688 13.5569 27.5679 13.0001H35.5751C37.1943 13.0063 38.5055 14.334 38.5116 15.9738Z" fill="black" />
                <path opacity="0.2" d="M36.9071 17.5988V21.0925C36.3655 21.0561 35.8283 20.9691 35.3025 20.8326C32.053 20.0533 29.3685 17.7455 28.0815 14.6251H33.9706C35.5898 14.6313 36.901 15.959 36.9071 17.5988Z" fill="black" />
                <path opacity="0.2" d="M36.9071 17.5988V21.0925C36.3655 21.0561 35.8283 20.9691 35.3025 20.8326C32.053 20.0533 29.3685 17.7455 28.0815 14.6251H33.9706C35.5898 14.6313 36.901 15.959 36.9071 17.5988Z" fill="black" />
                <path opacity="0.2" d="M35.3025 17.5988V20.8325C32.053 20.0532 29.3685 17.7454 28.0815 14.625H32.366C33.9852 14.6312 35.2963 15.959 35.3025 17.5988Z" fill="black" />
                <path d="M2.94131 14.625H32.361C33.9854 14.625 35.3023 15.9586 35.3023 17.6036V47.3964C35.3023 49.0414 33.9854 50.375 32.361 50.375H2.94131C1.31686 50.375 0 49.0414 0 47.3964V17.6036C0 15.9586 1.31689 14.625 2.94131 14.625Z" fill="url(#paint0_linear_2201_11182)" />
                <path d="M25.3918 25.9642H19.5108V42.1817H15.7639V25.9642H9.91016V22.8182H25.3918V25.9642Z" fill="white" />
            </g>
            <defs>
                <linearGradient id="paint0_linear_2201_11182" x1="6.13273" y1="12.2976" x2="29.6078" y2="52.4462" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#5A62C3" />
                    <stop offset="0.5" stop-color="#4D55BD" />
                    <stop offset="1" stop-color="#3940AB" />
                </linearGradient>
                <clipPath id="clip0_2201_11182">
                    <rect width="69" height="65" fill="white" />
                </clipPath>
            </defs>
        </svg>
HTML;
return $html;
    }

    public static function mapDayOfTheWeek($dow)
    {
        $days = [
            0 => AmosCommunity::t('amoscommunity','Lunedì'),
            1 => AmosCommunity::t('amoscommunity','Martedì'),
            2 => AmosCommunity::t('amoscommunity','Mercoledì'),
            3 => AmosCommunity::t('amoscommunity','Giovedì'),
            4 => AmosCommunity::t('amoscommunity','Venerdì'),
            5 => AmosCommunity::t('amoscommunity','Sabato'),
            6 => AmosCommunity::t('amoscommunity','Domenica'),
        ];
        if (!empty($days[$dow])) {
            return $days[$dow];
        }
        return $dow;
    }

    public static function mapMonthNames($month)
    {
        $months = [
            1 => "Gennaio",
            2 => "Febbraio",
            3 => "Marzo",
            4 => "Aprile",
            5 => "Maggio",
            6 => "Giugno",
            7 => "Luglio",
            8 => "Agosto",
            9 => "Settembre",
            10 => "Ottobre",
            11 => "Novembre",
            12 => "Dicembre"
        ];
        if (!empty($months[$month])) {
            return $months[$month];
        }
        return $month;
    }
}