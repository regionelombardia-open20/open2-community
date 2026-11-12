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
use open20\amos\community\models\Community;
use open20\amos\core\widget\WidgetGraphic;
use open20\amos\community\models\search\BookmarksSearch;
use open20\amos\community\utilities\CommunityUtil;

/**
 * Class WidgetGraphicsComments
 * @package open20\amos\community\widgets\graphics
 */
class WidgetGraphicsLinks extends WidgetGraphic
{
    private $community;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setDefaultValues();
        $this->setLabel(AmosCommunity::t('amoscommunity', 'Bookmark'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Aggiungi e condividi dei link'));
        $communityId = CommunityUtil::getCommunityScope();
        if($communityId){
            $this->community = Community::findOne($communityId);
        }
        $this->setClassCustom('square-widget-graphic');

        #$this->setDefaultValues();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $viewPath     = '@vendor/open20/amos-community/src/widgets/graphics/views/';
        $viewToRender = $viewPath.'links';

        if($_GET){
            $this->setCommunity(Community::findOne($_GET));
        }
        $communityLinks = $this->community->getSomeLinks();

        $isMemberActive = CommunityUtil::userIsCommunityMemberActive($this->community->id);

        $modelSearch = new BookmarksSearch();
        $dataProvider = $modelSearch->search([]);
        $dataProvider->query->limit(5);
        return $this->render(
            $viewToRender,
            [
                'model' => $this->community,
                'data' => $communityLinks,
                'dataP' => $dataProvider,
                'isMemberActive' => $isMemberActive,
                'limit' => $dataProvider->query->limit
            ]
        ) . "<div class='placeholder-widget'>".$this->view->render('@vendor/open20/amos-community/src/widgets/graphics/views/_placeholder_widget')."</div>";
    }

    /**
     *
     * @return integer
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     *
     * @param integer $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;
    }

    public function setDefaultValues()
    {
        $this->setCode('COMMENTSCOMMUNITY_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'CLinks'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of links'));
        if (empty($this->getCommunity())) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (!empty($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope['community'])) {
                    $this->setCommunity($scope['community']);
                }
            }
        }
        $moduleCommunity = \Yii::$app->getModule(AmosCommunity::getModuleName());
    }

    public function isVisible()
    {
        return true;
    }
}