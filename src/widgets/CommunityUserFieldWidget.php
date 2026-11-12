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

/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 10/06/2019
 * Time: 15:52
 */

namespace open20\amos\community\widgets;


use open20\amos\admin\models\UserProfile;
use open20\amos\community\utilities\CommunityUserFieldUtility;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class CommunityUserFieldWidget extends Widget
{
    public $dynamicModel;
    public $form;
    public $community;
    public $isView = false;
    public $user_id;

    public function init()
    {
        parent::init();
        if(($this->isView == false && (empty($this->dynamicModel) || empty($this->form)))){
            throw new InvalidConfigException("The params 'dynamicModel' and  'form' are mandatory");
        }
        if($this->isView && (empty($this->user_id) )){
            throw new InvalidConfigException("The param 'user_id' is Mandatory");

        }
//        $this->loadDynamicModel();
    }

    public function run()
    {
        if($this->isView) {
            $model = UserProfile::find()->andWhere(['user_id' => $this->user_id])->one();
            return $this->render('community_user_fields_view', ['model' => $model]);
        }else {
            return $this->render('community_user_fields', ['dynamicModel' => $this->dynamicModel, 'form' => $this->form]);
        }

    }

//    public function loadDynamicModel(){
//        $this->dynamicModel =  CommunityUserFieldUtility::loadDynamicFields($this->community->id, $this->user_id);
//    }

}