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
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace open20\amos\community\widgets;

use open20\amos\community\AmosCommunity;
use yii\base\Widget;

/**
 * Description of ManageUserCommunityInfo
 *
 */
class ManageUserCommunityInfo extends Widget
{
    public $community;
    public $user;
    public $button;


    public function init()
    {
        parent::init();
        if (is_null($this->community) || is_null($this->user)) {
            throw new \Exception(AmosCommunity::t('amoscommunity', 'Missing model'));
        }
    }

    public function run()
    {
        $community = $this->community;
        $user = $this->user;
        if($this->button){
            return $this->render('parts/_user_subscribe_unsubscribe_buttons', ['community' => $community, 'user' => $user]);
        }
        return $this->render('manage_user_community_info', ['community' => $community, 'user' => $user]);


    }
}
