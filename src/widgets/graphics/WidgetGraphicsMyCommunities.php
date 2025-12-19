<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\widgets\graphics
 * @category   CategoryName
 */

namespace open20\amos\community\widgets\graphics;

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\search\CommunitySearch;
use open20\amos\core\widget\WidgetGraphic;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\models\Community;

/**
 * Class WidgetGraphicsMyCommunities
 * @package open20\amos\community\widgets\graphics
 */
class WidgetGraphicsMyCommunities extends WidgetGraphic
{
    private $community;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setCode('MY_COMMUNITIES_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'My communities'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of my communities'));
        $this->setClassCustom('square-widget-graphic');
        $communityId = CommunityUtil::getCommunityScope();
        if($communityId){
            $this->community = Community::findOne($communityId);
        }
    }
    
    /**
     * @return string
     */
    public function getHtml()
    {
        $moduleCommunity = \Yii::$app->getModule('community');
        $search = new CommunitySearch();
        $search->setNotifier(new NotifyWidgetDoNothing());
        
        $viewPath = '@vendor/open20/amos-community/src/widgets/graphics/views/';
        $viewToRender = $viewPath . 'my_communities';
        $numberToView = 3;
        $communitiesList = $search->searchMyCommunities($_GET, $numberToView);
        if($moduleCommunity->enableNewCommunityHeader){
            $viewToRender = $viewPath . 'my_communities_new';
            $communitiesList = $search->searchCommunitiesOfInterest($_GET, 5);
			if($this->community){
                $communitiesList->query->andWhere(['parent_id' => $this->community->id]);
            }
        }
        
        if (is_null(\Yii::$app->getModule('layout'))) {
            $viewToRender .= '_old';
        }
        
        /** show subcommunities if you are inside a community and if you have anabled show subcommunities */
        
        $showSubscommunities = $moduleCommunity->showSubcommunities;
        $linkToSubcommunities = false;
        $titleSection =  AmosCommunity::t('amoscommunity', 'Le mie community');
        /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
        if ($showSubscommunities) {
            $moduleCwh = \Yii::$app->getModule('cwh');
            $titleSection = AmosCommunity::t('amoscommunity', 'Le mie sottocomunity');
            if (!is_null($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope['community'])) {
                    $search->subcommunityMode = true;
                    $linkToSubcommunities = true;
                }
            }
        }
        
        
        
        return $this->render(
            $viewToRender,
            [
                'communitiesList' => $communitiesList,
                'widget' => $this,
                'toRefreshSectionId' => 'widgetGraphicMyCommunities',
                'linkToSubcommunities' => $linkToSubcommunities,
                'titleSection' => $titleSection,
                'model' => $this->community,
            ]
        ) . "<div class='placeholder-widget'>".$this->view->render('@vendor/open20/amos-community/src/widgets/graphics/views/_placeholder_widget')."</div>";
    }
}
