<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\admin\widgets\graphics\views
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

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\base\ConfigurationManager;
use open20\amos\admin\widgets\ConnectToUserWidget;
use open20\amos\admin\widgets\SendMessageToUserWidget;
use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\CurrentUser;
 

use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\admin\widgets\graphics\WidgetGraphicsUsers;
use open20\amos\organizzazioni\models\ProfiloUserMm;

/**
 * @var yii\web\View $this
 * @var \open20\amos\admin\models\UserProfile $model
 */

$model = $model->user->userProfile;
$userId = $model->user_id;
$appController = Yii::$app->controller;
$adminModule = AmosAdmin::instance();

$nomeCognome = '';
if ($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) {
    if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= $model->nome;
    }
    if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= ' ' . $model->cognome;
    }
}

$viewUrl = "/" . AmosAdmin::getModuleName() . "/user-profile/view?id=" . $model->id;

$prevalentPartnershipTruncated = '';
$prevalentPartnershipName = '';
if (!is_null($model->prevalentPartnership)) {
    $prevalentPartnershipTruncated = $model->prevalentPartnership;
    $prevalentPartnershipName = $model->prevalentPartnership->name;
}

?>

<div class="avatar-wrapper <?= ($tipoUtente == 'espisitore') ? 'avatar-organizzatore' : '' ?> avatar-extra-text mb-0">
    <?php if (($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_VIEW)) &&
        ($adminModule->confManager->isVisibleField('userProfileImage', ConfigurationManager::VIEW_TYPE_VIEW))
    ) : ?>
        <?php
        $url = $model->getAvatarUrl('card_users');
        Yii::$app->imageUtility->methodGetImageUrl = 'getAvatarUrl';
        $logoOptions = [
            'class' => Yii::$app->imageUtility->getRoundImage($model)['class'],
            //'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($model)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($model)['margin-top'] . "%;",
            'alt' => $model->getNomeCognome(),
        ];
        $options = [];
        if (strlen($nomeCognome) > 0) {
            $logoOptions['alt'] = AmosAdmin::t('amosadmin', '#icon_name_title_link') . ' ' . $model->getNomeCognome();
            $options['title'] = AmosAdmin::t('amosadmin', '#icon_name_title_link') . ' ' . $model->getNomeCognome();
            $options['class'] = 'avatar';
        }
        $logo = Html::img($url, $logoOptions);
        ?>
        <?= Html::a($logo, $viewUrl, $options); ?>
    <?php endif; ?>
    <?php if ($tipoUtente == 'espisitore') : ?>
        <a href="#" class="espositore-tooltip" data-toggle="tooltip" title="Espositore">
            <span class="icon icon-white icon-sm am am-pin-assistant"></span>
        </a>
    <?php endif; ?>
    <?= ContextMenuWidget::widget([
        'model' => $model,
        'actionModify' => '/' . AmosAdmin::getModuleName() . '/user-profile/update?id=' . $model->id,
        'disableDelete' => true
    ]) ?>
    <?= \open20\amos\notificationmanager\forms\NewsWidget::widget([
        'model' => $model,
        'css_class' => 'badge badge-left'
    ]); ?>
    <div class="ml-2 avatar-body">
        <p class="avatar-name font-weight-bold mb-0"><?= Html::a($model->getNomeCognome(), $viewUrl, ['title' => AmosAdmin::t('amosadmin', '#icon_name_title_link') . ' ' . $model->getNomeCognome(), 'data-gui' => 'icon-view-profiles']); ?></p>
        <?php
        if (
            ($adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_VIEW)) &&
            ($adminModule->confManager->isVisibleField('prevalent_partnership_id', ConfigurationManager::VIEW_TYPE_VIEW))
        ) : ?>
            <!-- "additionalInfo" -->
            <p class="avatar-info font-weight-normal mb-0">
                <?= (!empty($prevalentPartnershipTruncated)) ? AmosIcons::show('briefcase', [], 'dash') . $prevalentPartnershipTruncated : ''; ?>
            </p>
        <?php endif; ?>

        <?php if (Yii::$app->user->id != $model->user_id) : ?>
            <?php if (!CurrentUser::isPlatformGuest()) : ?>
                <?php if ($adminModule->enableUserContacts && !$adminModule->enableSendMessage) : ?>
                    <?= ConnectToUserWidget::widget(['model' => $model, 'divClassBtnContainer' => '']) ?>
                <?php endif; ?>
                <?php if (!$adminModule->enableUserContacts && $adminModule->enableSendMessage) : ?>
                    <?= SendMessageToUserWidget::widget(['model' => $model, 'divClassBtnContainer' => '']) ?>
                <?php endif; ?>
                <?php if ($adminModule->enableInviteUserToEvent) : ?>
                    <?php
                    /** @var \open20\amos\events\AmosEvents $eventsModule */
                    $eventsModule = Yii::$app->getModule('events');
                    ?>
                    <?php if (!is_null($eventsModule) && $eventsModule->hasMethod('getInviteUserToEventWidget')) : ?>
                        <?= $eventsModule->getInviteUserToEventWidget($model) ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>