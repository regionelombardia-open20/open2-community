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

/**
 * Class WidgetGraphicsMyCommunities
 * @package open20\amos\community\widgets\graphics
 */
class WidgetGraphicsCommunitiesRecommended extends WidgetGraphic
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setCode('COMMUNITIES_RECOMMENDED_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'Communities Recommended'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of communities recommended'));
    }
    
    /**
     * @return string
     */
    public function getHtml()
    {
        $search = new CommunitySearch();
        $search->setNotifier(new NotifyWidgetDoNothing());
        
        $viewPath = '@vendor/open20/amos-community/src/widgets/graphics/views/';
        $viewToRender = $viewPath . 'communities_recommended';

        if(isset(\Yii::$app->params['WidgetGraphicsCommunitiesRecommended_limit']))
        {
            $numberToView = \Yii::$app->params['WidgetGraphicsCommunitiesRecommended_limit'];
        }else{
            $numberToView = 3;
        }
        if (is_null(\Yii::$app->getModule('layout'))) {
            $viewToRender .= '_old';
        }
        
        /** show subcommunities if you are inside a community and if you have anabled show subcommunities */
        $moduleCommunity = \Yii::$app->getModule('community');
        $showSubscommunities = $moduleCommunity->showSubcommunities;
        $linkToSubcommunities = false;
        /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
        if ($showSubscommunities) {
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope['community'])) {
                    $search->subcommunityMode = true;
                    $linkToSubcommunities = true;
                }
            }
        }
        
        $communitiesList = $search->searchCommunitiesRecommended($_GET, $numberToView);
       
        return $this->render(
            $viewToRender,
            [ 
                'communitiesList' => $communitiesList,
                'widget' => $this,
                'toRefreshSectionId' => 'widgetGraphicsCommunitiesRecommended',
                'linkToSubcommunities' => $linkToSubcommunities
            ]
        );
    }
}
