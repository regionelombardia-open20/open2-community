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

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;
use open20\amos\core\module\BaseAmosModule;

/**
 * @var yii\web\View $this
 * @var $model \open20\amos\community\models\CommunityUserMm
 */

$controller = Yii::$app->controller;
$isActionUpdate = ($controller->action->id == 'update');
$confirm = $isActionUpdate ? [
    'confirm' => AmosCommunity::t('amoscommunity','#confirm_exit_without_saving')
] : null;

?>
<div class="member-items">
    <p class="member-item-name">
        <?= Html::a('<strong>' . $model->name . '</strong>', ['/community/community/view', 'id' => $model->id], [
            'data' => $confirm
        ]) ?>
    </p>
    <p class="member-item-role">
        <?= AmosCommunity::t('amoscommunity', $model->communityType->name) ?>
    </p>
    <span class="member-item-status">
         <?= $model->workflowStatus->label ?>
    </span>
</div>
