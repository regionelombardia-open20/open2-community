<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\models\search
 * @category   CategoryName
 */

namespace lispa\amos\community\models\search;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\interfaces\CmsModelInterface;
use lispa\amos\core\interfaces\SearchModelInterface;
use lispa\amos\core\record\CmsField;
use lispa\amos\cwh\AmosCwh;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Class CommunitySearch
 * CommunitySearch represents the model behind the search form about `lispa\amos\community\models\Community`.
 * @package lispa\amos\community\models\search
 */
class CommunitySearch extends Community implements SearchModelInterface, CmsModelInterface
{
    private $container;
    
    /** @var bool|false $subcommunityMode - true if navigating child communities of a main community */
    public $subcommunityMode = false;
    
    public $isSearch = true;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by', 'community_type_id'], 'integer'],
            [['status', 'name', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['logo_id', 'cover_image_id'], 'number'],
        ];
    }
    
    /**
     * @param array $params
     * @return ActiveQuery $query
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();
        
        //check params to get orders value
        $this->setOrderVars($params);
        
        $moduleCommunity = Yii::$app->getModule('community');
        
        /** @var Community $className */
        $className = $moduleCommunity->modelMap['Community'];
        return $className::find()->distinct();
    }
    
    /**
     * @inheritdoc
     */
    public function searchFieldsLike()
    {
        return [
            'name',
            'description',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function searchFieldsGlobalSearch()
    {
        return [
            'name',
            'description',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getSearchQuery($query)
    {
        $communityModule = Yii::$app->getModule('community');
        if (!$communityModule->hideCommunityTypeSearchFilter) {
            $query->andFilterWhere(['community_type_id' => $this->community_type_id]);
        }
    }
    
    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchAdminAllCommunities($params, $limit = null)
    {
        return $this->search($params, 'admin-all', $limit);
    }
    
    /**
     * Search for communities whose the logged user belongs
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @param bool|false $onlyActiveStatus
     * @return ActiveDataProvider $dataProvider
     */
    public function searchMyCommunities($params, $limit = null, $onlyActiveStatus = false)
    {
        return $this->search($params, 'own-interest', $limit, $onlyActiveStatus);
    }
    
    /**
     * Search for the communities created by the logged user
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchCreatedByCommunities($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }
    
    /**
     * Search for the communities that the logged user has permission to validate
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchToValidateCommunities($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }
    
    /**
     * @param ActiveQuery $query
     */
    public function getVisibleNetworksQuery($query, $params = [], $onlyActiveStatus = true, $userId = null)
    {
        /** @var ActiveQuery $queryClosed */
        $queryClosed = $this->baseSearch($params);
        $queryClosed->innerJoin(CommunityUserMm::tableName(), 'community.id = ' . CommunityUserMm::tableName() . '.community_id'
            . ' AND ' . CommunityUserMm::tableName() . '.user_id = ' . $userId)
            ->andFilterWhere([
                'community.community_type_id' => CommunityType::COMMUNITY_TYPE_CLOSED
            ])->andWhere(CommunityUserMm::tableName() . '.deleted_at is null');
        $queryClosed->select('community.id');
        $queryNotClosed = $this->baseSearch($params);
        $queryNotClosed->leftJoin(CommunityUserMm::tableName(), 'community.parent_id = ' . CommunityUserMm::tableName() . '.community_id'
            . ' AND ' . CommunityUserMm::tableName() . '.user_id = ' . $userId)
            ->andWhere(CommunityUserMm::tableName() . '.deleted_at is null');
        $andWhere = ['or',
            [self::tableName() . '.parent_id' => null],
            ['and',
                ['not', [self::tableName() . '.parent_id' => null]],
                ['not', [CommunityUserMm::tableName() . '.community_id' => null]],
            ]
        ];
        if ($onlyActiveStatus) {
            $andWhere['or']['and'][] = [CommunityUserMm::tableName() . '.status' => CommunityUserMm::STATUS_ACTIVE];
        }
        $queryNotClosed->andWhere($andWhere);
        $queryNotClosed->andWhere([
            'community.community_type_id' => [CommunityType::COMMUNITY_TYPE_OPEN, CommunityType::COMMUNITY_TYPE_PRIVATE]
        ]);
        $queryNotClosed->select('community.id');
        $query->andWhere(['community.id' => $queryClosed])->orWhere(['community.id' => $queryNotClosed]);
    }
    
    /**
     * @param ActiveQuery $query
     */
    public function filterValidated($query)
    {
        $query->andWhere(['or',
                ['community.status' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED],
                ['and',
                    ['community.validated_once' => 1],
                    ['community.visible_on_edit' => 1]
                ]
            ]
        );
    }
    
    /**
     * @param ActiveQuery $query
     */
    public function filterByContext($query)
    {
        if (!empty($query)) {
            $query->andWhere(['community.context' => Community::className()]);
            $query->andWhere('community.deleted_at is null');
        }
        /** @var AmosCommunity $moduleCommunity */
        $moduleCommunity = Yii::$app->getModule('community');
        $showSubscommunities = $moduleCommunity->showSubcommunities;
        if ($this->subcommunityMode) {
            /** @var AmosCwh $moduleCwh */
            $moduleCwh = Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope[self::tableName()])) {
                    $communityId = $scope[self::tableName()];
                    //filter by communities chlid of the community ID in cwh scope
                    $query->andWhere(['community.parent_id' => $communityId]);
                    //and show subcommunities in the list anyway (we are in community dashboard)
                    $showSubscommunities = true;
                }
            }
        }
        if (!$showSubscommunities) {
            $query->andWhere(['community.parent_id' => null]);
        }
    }
    
    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchParticipants($params)
    {
        /** @var yii\db\ActiveQuery $query */
        $query = $this->getCommunityUserMms();
        $query->orderBy('user_profile.cognome ASC');
        $participantsDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $participantsDataProvider;
    }
    
    /***
     * CmsModelInterface
     */
    /**
     * Search method useful to retrieve news to show in frontend (with cms)
     *
     * @param $params
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function cmsSearch($params, $limit = null)
    {
        $dataProvider = $this->search($params, $limit, 'all');
        return $dataProvider;
    }
    
    /**
     * @inheritdoc
     */
    public function cmsViewFields()
    {
        $viewFields = [];
        array_push($viewFields, new CmsField("name", "TEXT", 'amoscommunity', $this->attributeLabels()["name"]));
        array_push($viewFields, new CmsField("description", "TEXT", 'amoscommunity', $this->attributeLabels()['description']));
        array_push($viewFields, new CmsField("communityLogo", "IMAGE", 'amoscommunity', $this->attributeLabels()['communityLogo']));
        return $viewFields;
    }
    
    /**
     * @inheritdoc
     */
    public function cmsSearchFields()
    {
        $searchFields = [];
        
        array_push($searchFields, new CmsField("name", "TEXT"));
        array_push($searchFields, new CmsField("description", "TEXT"));
        
        return $searchFields;
    }
    
    /**
     * @param int $id
     * @return boolean
     */
    public function cmsIsVisible($id)
    {
        $retValue = true;
        return $retValue;
    }
}
