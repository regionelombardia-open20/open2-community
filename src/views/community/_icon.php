<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\widgets\JoinCommunityWidget;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\notificationmanager\forms\NewsWidget;

/**
 * @var \lispa\amos\community\models\Community $model
 */

$communityModule = Yii::$app->getModule('community');
$fixedCommunityType = !is_null($communityModule->communityType);
$bypassWorkflow = $communityModule->bypassWorkflow;
if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && $model->id == 2750) {
    $viewUrl = '/community/join?id=' . $model->id;
}else{
    $viewUrl = '/community/community/view?id=' . $model->id;
}


?>

<div class="card-container community-card-container col-xs-12 nop">
    <div class="col-xs-12 nop icon-header">
        <?= ContextMenuWidget::widget([
            'model' => $model,
            'actionModify' => "/community/community/update?id=" . $model->id,
            'actionDelete' => "/community/community/delete?id=" . $model->id,
            'mainDivClasses' => '',
            'optionsDelete' => ['class' => 'delete-community-btn']
        ]);
        ?>
        <?= $newsWidget = NewsWidget::widget([
            'model' => $model,
            'css_class' => 'badge'
        ]) ?>
        <div class="community-image">
            <?php
            $url = '/img/img_default.jpg';
            if (!is_null($model->communityLogo)) {
                $url = $model->communityLogo->getUrl('square_medium', false, true);
            }
            $logo = Html::img($url, [
                'class' => 'img-responsive',
                'alt' => $model->getAttributeLabel('communityLogo')
            ]);
            ?>
            <?= Html::a($logo, $viewUrl, ['title' => $model->name]); ?>
        </div>
    </div>
    <div class="col-xs-12 nop icon-body">
        <h3 class="title">
            <?= Html::a($model->name, $viewUrl, ['title' => AmosCommunity::t('amoscommunity','#icon_title_link') . ' ' . $model->name]); ?>
        </h3>
    </div>
    <div class="col-xs-12 nop icon-footer">
        <?php
        $accessType = '';

        if (!$fixedCommunityType) {
            $accessType = AmosCommunity::t('amoscommunity', 'Access type: ') . AmosCommunity::t('amoscommunity', $model->getCommunityTypeName());
        }

        $content = '';
        $content .= Html::tag('p', $accessType);

        if (!empty($accessType)) {
            echo Html::tag('div', AmosIcons::show('info-circle', [], 'dash'), [
                    'class' => 'amos-tooltip pull-left',
                    'data-toggle' => 'tooltip',
                    'data-html' => 'true',
                    'title' => $content]
            );
        }
        ?>
        <?= JoinCommunityWidget::widget(['model' => $model, 'divClassBtnContainer' => 'pull-right']) ?>
    </div>
</div>
