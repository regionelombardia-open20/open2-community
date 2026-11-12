<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\widgets\graphics
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
