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

use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityUserMm;

/**
 * @var $model \open20\amos\community\models\CommunityUserMm
 */
?>
<?php

$controller = Yii::$app->controller;
$isActionUpdate = ($controller->action->id == 'update');
$confirm = $isActionUpdate ? [
    'confirm' => AmosCommunity::t('amoscommunity','#confirm_exit_without_saving')
] : null;

$view_email_partecipants = false;
if (isset(Yii::$app->getModule('community')->view_email_partecipants)) {
    $view_email_partecipants = Yii::$app->getModule('community')->view_email_partecipants;
}

?>
<div class="member-items">
    <p class="member-item-name">
        <?php
        $adminModuleName = \open20\amos\admin\AmosAdmin::getModuleName();
        ?>
        <?= \open20\amos\core\helpers\Html::a('<strong>' . $model->user->userProfile->surnameName . '</strong>', ['/'.$adminModuleName.'/user-profile/view', 'id' => $model->user->userProfile->id ], [
            'data' => $confirm
        ])?>
    </p>

    <p class="member-item-role <?= $model->role ?>">
        <?= AmosCommunity::t('amoscommunity', $model->role) ?>
    </p>
    <span class="member-item-status <?= $model->status ?>">
        <?= AmosCommunity::t('amoscommunity', $model->status) ?>
    </span>
    <p>
        <?= ($view_email_partecipants && $checkCM) ? $model->user->email : '' ?>
    </p>
</div>

