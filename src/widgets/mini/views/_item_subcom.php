<?php

use lispa\amos\community\AmosCommunity;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\module\BaseAmosModule;

/**
 * @var yii\web\View $this
 * @var $model \lispa\amos\community\models\CommunityUserMm
 */

$controller = Yii::$app->controller;
$isActionUpdate = ($controller->action->id == 'update');
$confirm = $isActionUpdate ? [
    'confirm' => BaseAmosModule::t('amoscore', '#confirm_exit_without_saving')
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
