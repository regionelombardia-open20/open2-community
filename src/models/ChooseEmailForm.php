<?php

namespace open20\amos\community\models;

use yii\base\Model;

class ChooseEmailForm extends Model
{
 public $email;
 public function rules()
 {
    return [
        ['email', 'email'],
        ['email', 'required'],
    ];
 }
}