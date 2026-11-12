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

use open20\amos\core\views\AmosGridView;
use yii\helpers\Html;
use open20\amos\community\AmosCommunity;
use open20\amos\community\assets\AmosCommunityAsset;
use open20\amos\community\widgets\CommunityCardWidget;
use open20\amos\community\models\Community;

?>
<?php
AmosCommunityAsset::register($this);
$this->title = AmosCommunity::t('amoscommunity', "Sposta community '{name}'", ['name' => $model->title]);
$this->params['breadcrumbs'][] = $this->title;
$currentCommunity = $model;

$columns = [];

$columns['logo_id'] = [
    'label' => AmosCommunity::t('amoscommunity', 'Logo'),
    'format' => 'raw',
    'value' => function ($model) use ($enabledHierarchy) {
        $options = ['model' => $model];
        if ($enabledHierarchy) {
            $options['enableHierarchy'] = true;
        }
        return CommunityCardWidget::widget($options);
    }
];

$columns['name'] = [
    'attribute' => 'name',
    'label' => AmosCommunity::t('amoscommunity', '#community_name'),
    'format' => 'html',
];

if (!$fixedCommunityType) {
    $columns['communityType'] = [
        'attribute' => 'communityType',
        'label' => AmosCommunity::t('amoscommunity', '#community_type'),
        'format' => 'html',
        'value' => function ($model) {
            /** @var Community $model */
            if (!is_null($model->community_type_id)) {
                return AmosCommunity::t('amoscommunity', $model->communityType->name);
            } else {
                return '-';
            }
        }
    ];
}

if (!$bypassWorkflow) {
    $columns['status'] = [
        'attribute' => 'status',
        'label' => AmosCommunity::t('amoscommunity', '#community_status'),
        'value' => function ($model) {
            return $model->hasWorkflowStatus() ? AmosCommunity::t('amoscommunity', $model->getWorkflowStatus()->getLabel()) : '-';
        }
    ];
}

$actionColumns = [
    'class' => 'open20\amos\core\views\grid\ActionColumn',
    'template' => '{move}',
    'buttons' => [
        'move' => function ($url, $model) use ($currentCommunity) {
            if ($model->id != $currentCommunity->id) {
                return Html::a(AmosCommunity::t('amoscommunity', 'Seleziona'), ['/community/community/move', 'id' => $currentCommunity->id, 'toId' => $model->id], [
                    'title' => AmosCommunity::t('amoscommunity', 'Sposta in questa community'),
                    'class' => 'btn btn-primary',
                    'data-confirm' => AmosCommunity::t('amoscommunity', "Vuoi spostare la tua community sotto la community <strong>{name}</strong>?", [
                            'name' => $model->title]
                    )
                ]);
            }
            return '';
        }
    ]
];

if (Yii::$app->controller->id == 'subcommunities') {
    $actionColumns['viewOptions'] = [
        'class' => 'btn btn-tools-secondary',
        'url' => ['/community/community/view'],
        'defaultUrlIdParam' => true
    ];

    $actionColumns['updateOptions'] = [
        'class' => 'btn btn-tools-secondary',
        'url' => ['/community/community/update'],
        'defaultUrlIdParam' => true
    ];

    $actionColumns['deleteOptions'] = [
        'class' => 'btn btn-danger-inverse',
        'url' => ['/community/community/delete'],
        'defaultUrlIdParam' => true
    ];
}

$columns[] = $actionColumns;

?>

<div class="container-change-view">
    <div class="btn-tools-container flexbox">
        <div class="tools-right">
            <div class="btn-group"><span class="btn btn-secondary show-hide-element am am-search" title="Cerca" data-toggle-element="form-search"> </span></div>
        </div>
    </div>
</div>
<div class="community-index">
    <?= $this->render('_search', [
        'model' => $modelSearch,
        'originAction' => '/community/community/move?id='.$model->id,
        'cancelAction' => '/community/community/move?id='.$model->id
    ]); ?>

    <?= AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns
    ]) ?>
</div>
