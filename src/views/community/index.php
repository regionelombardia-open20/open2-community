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
use lispa\amos\community\assets\AmosCommunityAsset;
use lispa\amos\community\models\Community;
use lispa\amos\community\rbac\UpdateOwnNetworkCommunity;
use lispa\amos\community\rules\ValidateSubcommunitiesRule;
use lispa\amos\community\widgets\CommunityCardWidget;
use lispa\amos\community\widgets\JoinCommunityWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\DataProviderView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\community\models\search\CommunitySearch $model
 * @var \lispa\amos\dashboard\models\AmosUserDashboards $currentDashboard
 * @var string $currentView
 */

$communityModule = Yii::$app->getModule('community');
$fixedCommunityType = !is_null($communityModule->communityType);
$bypassWorkflow = $communityModule->bypassWorkflow;



$isDemo = (isset(\Yii::$app->params['isDemo']) && (\Yii::$app->params['isDemo'])) ? true : false;

$js = "
$('." . JoinCommunityWidget::btnJoinSelector() . "').on('click', function(event) {
    var communityId = $(this).data('community_id');
    if (communityId) {
        $.ajax({
            url: '" . Url::toRoute(['/community/community/increment-community-hits']) . "?id=' + communityId,
            type: 'get',
            success: function (response) {
                try {
                    var responseArray = $.parseJSON(response);
                    if (responseArray['success'] == 0) {
";
($isDemo) ? '' : $js .= "alert('" . AmosCommunity::t('amoscommunity', 'Hits increment failed') . "');";
$js .= "                return false;
                    }
                } catch (e) {
                    // not json
                    alert('Errore AJAX');
                }
            }
        });
    }
});

/* check OK button modal and show spinner */
$('body').on('click','.bootstrap-dialog-footer-buttons > .btn.btn-warning',function(){
    $('.loading').show();
});

";

$this->registerJs($js, View::POS_READY);
AmosCommunityAsset::register($this);
$columns = [];

$columns['logo_id'] = [
    'label' => AmosCommunity::t('amoscommunity', 'Logo'),
    'format' => 'raw',
    'value' => function ($model)use($enabledHierarchy) {
        $options = ['model' => $model];
        if($enabledHierarchy){
            $options['enableHierarchy'] = true;
        }
        return CommunityCardWidget::widget($options);
    }
];
$columns['name'] = 'name';
if (!$fixedCommunityType) {
    $columns['communityType'] = [
        'attribute' => 'communityType',
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
        'value' => function ($model) {
            return $model->hasWorkflowStatus() ? AmosCommunity::t('amoscommunity', $model->getWorkflowStatus()->getLabel()) : '-';
        }
    ];
}

$columns[] = [
    'class' => 'lispa\amos\core\views\grid\ActionColumn',
    'template' => '{publish}{reject}{joinCommunity}{view}{update}{delete}',
    'buttons' => [
        'publish' => function ($url, $model) {
            $createUrlParams = [
                '/community/community/publish',
                'id' => $model['id'],
                'redirectWizard' => false
            ];
            $btn = '';
            if ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE && (Yii::$app->getUser()->can('COMMUNITY_VALIDATOR') ||
                    Yii::$app->getUser()->can(ValidateSubcommunitiesRule::className(), ['model' => $model]))) {
                $btn = Html::a(AmosIcons::show('check-circle', ['class' => '']), Yii::$app->urlManager->createUrl($createUrlParams), ['title' => AmosCommunity::t('amoscommunity', 'Publish'),'class' => 'btn btn-tool-secondary']);
            }
            return $btn;
        },
        'reject' => function ($url, $model) {
            $createUrlParams = [
                '/community/community/reject',
                'id' => $model['id']
            ];
            $btn = '';
            if ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE && (Yii::$app->getUser()->can('COMMUNITY_VALIDATOR') ||
                    Yii::$app->getUser()->can(ValidateSubcommunitiesRule::className(), ['model' => $model]))) {
                $btn = Html::a(AmosIcons::show('minus-circle', ['class' => '']), Yii::$app->urlManager->createUrl($createUrlParams), ['title' => AmosCommunity::t('amoscommunity', 'Reject'), 'class' => 'btn btn-tool-secondary']);
            }
            return $btn;
        },
        'joinCommunity' => function ($url, $model) {
            if(\Yii::$app->user->can(UpdateOwnNetworkCommunity::className(), ['model' => $model])){
                return JoinCommunityWidget::widget(['model' => $model, 'isGridView' => true, 'useIcon' => true]);
            }
            return '';
        },
    ]
];

?>

<div class="community-index">
    <?= $this->render('_search', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id
    ]); ?>
    <?= $this->render('_order', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id
    ]); ?>
    
    <?php echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => $columns,
        ],
        'iconView' => [
            'itemView' => '_icon'
        ],
    ]); ?>

</div>

<div class="loading" id="loader" hidden></div>
