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

namespace open20\amos\community\widgets;

use open20\amos\admin\models\UserProfile;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityMsgraphTokens;
use yii\base\Widget;

class MsTeamsConnectWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $moduleCommunity = \Yii::$app->getModule('community');
        $profile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id ])->one();
        if($profile && $profile->enable_msgraph && $moduleCommunity->enableMicrosoftTeams) {
            $isConnected = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->count();
            if (!$isConnected) {
                return $this->render('ms_teams_connect');
            }
        }
        return '';

    }


}