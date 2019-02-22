<?php
use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\community\AmosCommunity;

/**
 * @var $model \lispa\amos\community\models\CommunityUserMm
 */
?>
<?php
$controller = Yii::$app->controller;
$isActionUpdate = ($controller->action->id == 'update');
$confirm = $isActionUpdate ? [
    'confirm' => \lispa\amos\core\module\BaseAmosModule::t('amoscore', '#confirm_exit_without_saving')
] : null;

?>
<div class="member-items">
    <p class="member-item-name">
        <?= \lispa\amos\core\helpers\Html::a('<strong>' . $model->user->userProfile->surnameName . '</strong>', ['/admin/user-profile/view', 'id' => $model->user->userProfile->id ], [
            'data' => $confirm
        ])?>
    </p>
    <p class="member-item-role">
        <?= $model->role ?>
    </p>
    <span class="member-item-status">
        <?= $model->status ?>
    </span>
</div>

