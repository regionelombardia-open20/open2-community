<?php

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\utilities\CommunityUtil;

/**
 * @var $dynamicModel \yii\base\DynamicModel
 * @var $form \open20\amos\core\forms\ActiveForm
 * @var $model \open20\amos\community\models\Community
 */

//\open20\amos\layout\assets\SpinnerWaitAsset::register($this);
?>
<?php
$configs = $model->getMessageAlertMsTeams();

echo $this->render('parts/header_community', ['model' => $model, 'isOrganizzazioniCommunity' => $isOrganizzazioniCommunity]);
echo $this->render('parts/menu_community', ['model' => $model, 'isOrganizzazioniCommunity' => $isOrganizzazioniCommunity]);
?>
<!--    <div class="loading loading-community"></div>-->

<?php
$title = AmosCommunity::t('amoscommunity', 'Dashboard generale');
if ($model->parent_id) {
    $title = AmosCommunity::t('amoscommunity', 'Dashboard canale');
    $parent = Community::findOne($model->parent_id);
    if ($parent->parent_id) {
        $title = AmosCommunity::t('amoscommunity', 'Dashboard sottocanale');
    }
}

if (!\Yii::$app->user->isGuest) {
    $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
}

?>
    <div class="container">
        <?php echo $this->render('parts/_alert_ms_teams', $configs) ?>
    </div>

<?php
$isDashboard = \Yii::$app->controller->module->id == 'community' && \Yii::$app->controller->id == 'join' && \Yii::$app->controller->action->id == 'open-join';

if ($isDashboard && $model->community_type_id == \open20\amos\community\models\CommunityType::COMMUNITY_TYPE_OPEN && $status != \open20\amos\community\widgets\CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) { ?>
    <div class="container">
        <div class="alert alert-warning">
            <?=$model->parent_id ? CommunityUtil::SubcommToChannelTrad( "Iscriviti alla community per visualizzarne i contenuti") :  AmosCommunity::t('amoscommunity', 'Iscriviti alla community per visualizzarne i contenuti') ?>
        </div>
    </div>
<?php }

if ($isDashboard && $status == \open20\amos\community\widgets\CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) { ?>
    <div class="container">
        <h2 class="text-secondary m-t-20 h6"><?= strtoupper($title) ?></h2>
    </div>
<?php } ?>