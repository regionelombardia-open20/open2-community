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


use open20\amos\core\user\User;
use open20\amos\community\AmosCommunity;
use open20\amos\core\models\ModelsClassname;
use open20\amos\core\helpers\Html;
use open20\amos\core\module\BaseAmosModule;

$moduleNotify = Yii::$app->getModule('notify');
$notificationConfOpt = $moduleNotify->createModel('NotificationsConfOpt');
$widgetConfData = $notificationConfOpt::emailFrequencyValueAndLabels();


$modelClassname = ModelsClassname::find()->andWhere(['module' => 'community'])->one();
$notificationConfNetworkModel = $moduleNotify->createModel('NotificationconfNetwork');
$notificationNetwork = $notificationConfNetworkModel::find()->select('email,id')
    ->andWhere(['models_classname_id' => $modelClassname->id])
    ->andWhere(['record_id' => $community->id])
    ->andWhere(['user_id' => $user->id])->one();


$notificationConf = $moduleNotify->createModel('NotificationConf');
$notificationUser = $notificationConf::find()->andWhere(['user_id' => $user->id])->one();

?>

<div class="dropdown wrapper-dropdown-notify">
    <button type="button" class="btn btn-notify dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?= AmosCommunity::t('amoscommunity', 'Menu imposta notifiche per {community}', ['community' => $community->name]) ?>">
        <span class="mdi mdi-bell-<?= $widgetConfData[$notificationNetwork->email] == 'Mai' || $notificationUser->notifications_enabled == 0 ? 'off' : 'ring' ?>-outline"></span><span class="label-btn"><?= !$community->parent_id ? AmosCommunity::t('amoscommunity', 'Iscritto') : ''  ?></span> <small class="icon-expand mdi mdi-chevron-down"></small>
    </button>
    <div class="dropdown-menu dropdown-menu-right dropdown-notify">
        <div class="dropdown-body">
            <div class="dropdown-title">
                <?= AmosCommunity::t('amoscommunity', 'Imposta notifiche') ?>
            </div>
            <span class="text-secondary">
                <?= AmosCommunity::t('amoscommunity', 'Con quale frequenza desideri ricevere aggiornamenti da questa community?') ?>
            </span>
        </div>
        <?php if ($notificationUser->notifications_enabled == 1) { ?>
            <ul class="list-menu-dropdown dropdown-list-notify">

                <?php foreach ($widgetConfData as $key => $value) { ?>
                    <li class="<?= $key == $notificationNetwork->email ? 'bold' : '' ?>"> <?= Html::a($value, ['/community/community/change-notification', 'value' => $key, 'notification_network' => $notificationNetwork->id]); ?>
                        <?= $key == $notificationNetwork->email ? '<span class="mdi mdi-check-circle-outline mdi-24px"></span>' : '' ?></li>
                    <li role="separator" class="divider"></li>
                <?php } ?>

            </ul>
        <?php } else { ?>
            <div class="dropdown-body">
                <div class="panel panel-warning">
                    <div class="panel-body bold">
                       
                        <?= AmosCommunity::t('amoscommunity', 'Le notifiche di piattaforma sono disabilitate') ?><br>
                         <?= Html::a('Imposta notifiche', ['/amosadmin/user-profile/update', 'id' => $user->userProfile->id,'#' =>'tab-notifications'], ['title' => 'Imposta notifiche']); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="dropdown-footer">
            <?= $this->render('parts/_user_subscribe_unsubscribe_buttons', ['community' => $community, 'user' => User::findOne(\Yii::$app->user->id)]) ?>
        </div>
    </div>
</div>