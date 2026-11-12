<?php

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
