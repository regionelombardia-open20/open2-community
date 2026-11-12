<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\basic\template
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


use yii\helpers\Html;
use open20\amos\core\forms\ActiveForm;
use open20\amos\admin\AmosAdmin;

//use kartik\password\PasswordInput;
use open20\amos\core\forms\PasswordInput;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \open20\amos\core\forms\FirstAccessForm */
/* @var $user open20\amos\core\user\ */

$this->title = \open20\amos\community\AmosCommunity::t('amoscommunity', "Crea account microsoft e connettiti");
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 nop">
        <p>
            <?= \open20\amos\community\AmosCommunity::t('amoscommunity', "Il tuo account microsoft di dominio con l'email <strong>{email}</strong> non è presente, se vuoi creare l'account scegli le tue nuove credenziali e procedi",['email' => $user->email]) ?>
            <br><br>
            <?= \open20\amos\community\AmosCommunity::t('amoscommunity', "Scegli la password che userai per accedere al tuo account microsoft") ?>
            <br><strong><?= \open20\amos\community\AmosCommunity::t('amoscommunity', "Email account: ") ?></strong> <?= $user->email ?>
        </p>
    </div>
</div>
<div>
    <?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['autocomplete' => 'off'],
    ])
    ?>
    <div class="row">
        <div class="col-xs-12 nop">
            <?=
            $form->field($model, 'password')->widget(PasswordInput::classname(), [
                'language' => 'it',
                'options' => [
                    'autocomplete' => 'off'
                ],
                'pluginOptions' => [
                    'showMeter' => true,
                    'toggleMask' => true,
                    'language' => 'it'
                ]]);
            ?>
            <?= $form->field($model, 'ripetiPassword')->passwordInput([
                'autocomplete' => 'off'
            ]) ?>
            <?= $form->field($model, 'token')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'username')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-xs-12 footer-link nop">
            <?= Html::a(\open20\amos\community\AmosCommunity::t('amoscommunity', 'Torna a scelta email'),['/community/ms-teams/choose-ms-teams','urlRedirect' => $urlRedirect], ['class' => 'btn btn-secondary', 'title' => AmosAdmin::t('amosadmin', '#text_button_login')]) ?>

            <?= Html::submitButton(\open20\amos\community\AmosCommunity::t('amoscommunity', 'Connetti'), ['class' => 'btn btn-primary pull-right', 'name' => 'first-access-button', 'title' => AmosAdmin::t('amosadmin', '#text_button_login')]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
