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
 * Date: 14/06/2019
 * Time: 16:40
 */

namespace open20\amos\community\validators;


use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityUserFieldVal;
use yii\validators\Validator;

class UniqueUserValCommunityValidator extends Validator
{

    public $community_id;

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws \yii\base\InvalidConfigException
     */
    function validateAttribute($model, $attribute)
    {

       $value = $model->$attribute;
       $fieldValCount = CommunityUserFieldVal::find()
           ->innerJoin('community_user_field', 'community_user_field_val.user_field_id = community_user_field.id')
           ->andWhere(['value' => $value])
           ->andWhere(['!=', 'user_id', \Yii::$app->user->id])
           ->andWhere(['community_id' => $this->community_id])
           ->count();
       if($fieldValCount > 0){
           $this->addError($model, $attribute, AmosCommunity::t('amoscommunity', 'Questo campo deve essere univoco'));
           return false;
       }
       return true;
    }




}
