<?php
$this->title = \lispa\amos\community\AmosCommunity::t('amoscommunity', 'Participants');
$this->params['breadcrumbs'][] = ['label' => \lispa\amos\community\AmosCommunity::t('amoscommunity', 'Community'), 'url' => ['join', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

echo \lispa\amos\community\widgets\CommunityMembersWidget::widget([
    'model' => $model,
    'targetUrlParams' => [
        'viewM2MWidgetGenericSearch' => true
    ],
]);