<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models
 * @category   CategoryName
 */

namespace lispa\amos\community\models;

use yii\base\Model;


/**
 * Class LoginForm
 * @package lispa\amos\admin\models
 */
class RegisterForm extends Model
{
    public $nome;
    public $cognome;
    public $email;
    public $role;

    /**
     * @var string $captcha
     */
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'cognome', 'email'], 'required'],
            [['role'], 'safe']
        ];
    }
}
