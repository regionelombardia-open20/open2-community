<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\utilities
 * @category   CategoryName
 */

namespace lispa\amos\community\utilities;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\exceptions\CommunityException;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\cwh\models\CwhAuthAssignment;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class CommunityUtil
 * @package lispa\amos\community\utilities
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
            'community_id' => $model->id,
            'user_id' => \Yii::$app->getUser()->getId(),
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
                        $ok = $communityManager->save(false);
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
    public static function getCommunitiesByUserId($userId, $onlyIds = false, $returnQuery = false, $ignoreValidatedOnce = false)
    {
        if (!is_numeric($userId) || ($userId <= 0)) {
            throw new CommunityException(AmosCommunity::t('amoscommunity', 'getCommunitiesByUserId: userId is not a number or is not positive'));
        }
        /** @var ActiveQuery $query */
        $query = Community::find();
        $query->innerJoinWith('communityUserMms')
            ->andWhere([CommunityUserMm::tableName() . '.user_id' => $userId])
            ->andWhere([CommunityUserMm::tableName() . '.status' => CommunityUserMm::STATUS_ACTIVE])
            ->andWhere([Community::tableName() . '.context' => Community::className()]);
        if (!$ignoreValidatedOnce) {
            $query->andWhere([Community::tableName() . '.validated_once' => 1]);
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
    public static function getCommunitiesByRoleAndUserId($userId, $role, $onlyIds = false, $returnQuery = false, $ignoreValidatedOnce = false)
    {
        /** @var ActiveQuery $query */
        $query = self::getCommunitiesByUserId($userId, false, true, $ignoreValidatedOnce);
        $query->andWhere([CommunityUserMm::tableName() . '.role' => $role]);
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
    public static function getCommunitiesManagedByUserId($userId, $onlyIds = false, $returnQuery = false, $ignoreValidatedOnce = false)
    {
        return self::getCommunitiesByRoleAndUserId($userId, CommunityUserMm::ROLE_COMMUNITY_MANAGER, $onlyIds, $returnQuery, $ignoreValidatedOnce);
    }
    
    /**
     * @param int $userId
     * @param bool $onlyIds
     * @param bool $returnQuery IF true return the ActiveQuery object
     * @param bool $ignoreValidatedOnce
     * @return Community[]|int[]|ActiveQuery
     * @throws CommunityException
     */
    public static function getCommunitiesParticipateByUserId($userId, $onlyIds = false, $returnQuery = false, $ignoreValidatedOnce = false)
    {
        return self::getCommunitiesByRoleAndUserId($userId, CommunityUserMm::ROLE_PARTICIPANT, $onlyIds, $returnQuery, $ignoreValidatedOnce);
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
        
        $permissionCreateName = $cwhModule->permissionPrefix . "_CREATE_" . $classname;
        $cwhAuthTable = CwhAuthAssignment::tableName();
        /** @var ActiveQuery $parentCommunitiesQuery */
        $parentCommunitiesQuery = Community::find()
            ->innerJoin($cwhAuthTable, $cwhAuthTable . '.cwh_config_id = ' . Community::getCwhConfigId() . ' AND cwh_network_id = community.id')
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
        $contexts = $managerQuery->column();
        return $contexts;
    }
    
    /**
     * This method return a string array of community managers of all community contexts.
     * @return array
     */
    public static function getAllCommunityManagerRoles()
    {
        $contexts = self::getAllCommunityContexts();
        $communityManagerRoles = [];
        foreach ($contexts as $context) {
            $context = '\\' . $context;
            /** @var \lispa\amos\community\models\CommunityContextInterface $model */
            $model = new $context();
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
        $communityIds = [$community->id];
        
        if (!empty($subCommunities)) {
            foreach ($subCommunities as $subCommunity) {
                $communityIds[] = $subCommunity->id;
            }
            foreach ($subCommunities as $subCommunity) {
                $communityIds = ArrayHelper::merge($communityIds, self::getCommunityAndSubcommunitiesIdsRecursive($subCommunity));
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
        $query = CommunityUserMm::find()->andWhere(0);
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $community = Community::findOne($scope['community']);
                $query = $community->getCommunityUserMms();
            }
        }
        return $query;
    }
    
    public static function isLoggedCommunityManager()
    {
        $cwhModule = \Yii::$app->getModule('cwh');
        if (isset($cwhModule) && !empty($cwhModule->getCwhScope())) {
            $scope = $cwhModule->getCwhScope();
            if (isset($scope['community'])) {
                $model = Community::findOne($scope['community']);
                return CommunityUtil::hasRole($model);
            }
        }
        
        return false;
    }
    
    /**
     * @param $user_id
     * @param $model Community
     * @return bool
     */
    public static function hasRole($model)
    {
        $communityUserMm = CommunityUserMm::find()
            ->andWhere(['user_id' => \Yii::$app->user->id])
            ->andWhere(['community_id' => $model->id])
            ->andWhere(['role' =>  $model->getManagerRole()])
            ->one();
        if (!empty($communityUserMm)) {
            return true;
        }
        return false;
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
                /** @var \lispa\amos\cwh\AmosCwh $cwhModule */
                $communityContents = \lispa\amos\cwh\utility\CwhUtil::getNetworkContents(Community::getCwhConfigId(), $community->id);
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
        $query = new Query();
        $query->from(CommunityUserMm::tableName());
        $query->andWhere([CommunityUserMm::tableName() . '.community_id' => $communityId,
            CommunityUserMm::tableName() . '.user_id' => $userId,
            CommunityUserMm::tableName() . '.deleted_at' => null]);
        $partecipant = $query->all();
        return (!is_null($partecipant)) && count($partecipant);
    }
    
    /**
     * Returns an array with community types ready to be rendered in a select.
     * @return array
     */
    public static function getCommunityTypeReadyForSelect()
    {
        return self::translateArrayValues(ArrayHelper::map(CommunityType::find()->asArray()->all(), 'id', 'name'));
    }
}
