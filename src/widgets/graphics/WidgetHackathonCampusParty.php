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

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace open20\amos\community\widgets\graphics;

use open20\amos\core\widget\WidgetGraphic;
use open20\amos\community\AmosCommunity;
use Yii;
use yii\helpers\Url;
use open20\amos\community\utilities\CommunityUtil;


class WidgetHackathonCampusParty extends WidgetGraphic
{
    const ID_COMMUNITY_PADRE = 2751;
    const ID_COMMUNITY = 2754;
    
    
    /**
     * 
     * @return boolean
     */
    public function isVisible()
    {
        $ret = false;
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) 
        {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) 
            {
                if($scope['community'] == WidgetHackathonCampusParty::ID_COMMUNITY_PADRE)
                {
                    if (\Yii::$app->getUser()->can($this->getWidgetPermission())) {
                        $ret = true;
                    } 
                }
            }
        }
        return $ret;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setCode('HACKATON_COMMUNITIES_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'Hackathon'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Consente all\'utente di accedre alla community Hackathon'));
    }

    /**
     * 
     * @return type
     */
    public function getHtml()
    {
        $viewToRender = 'hackathon_communities';
        if(CommunityUtil::userIsSignedUp(WidgetHackathonCampusParty::ID_COMMUNITY, \Yii::$app->user->id))
        {
            $url = Url::to(['/community/join', 'id' => WidgetHackathonCampusParty::ID_COMMUNITY]);
        }
        else
        {
            $url = Url::to(['/community/community/view', 'id' => WidgetHackathonCampusParty::ID_COMMUNITY]);
        }
        return $this->render($viewToRender, [
            'url' => $url,
            'widget' => $this,
        ]);
    }
}

