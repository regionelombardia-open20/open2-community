<?php

use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\views\AmosGridView;
use open20\amos\core\views\DataProviderView;
use open20\amos\dashboard\AmosDashboard;
use yii\helpers\Html;
use open20\amos\community\AmosCommunity;

/**
 * @var $model \open20\amos\community\models\Community
 * @var $providerGraphic \yii\data\ActiveDataProvider
 * @var $widgetSelected
 * @var $widgetGraphicSelectable \open20\amos\dashboard\models\AmosWidgets
 * @var $widgetIconSelectable
 * @var $currentDashboard
 * @var $this \yii\web\View
 *
 */

$js = <<<JS
    $('.move-up,.move-down').click(function(e){
        e.preventDefault();
        let row = $(this).parents('tr');
        if ($(this).is(".move-up")) {
            row.insertBefore(row.prev());
        } else {
            row.insertAfter(row.next());
        }
    })
JS;
$this->registerJs($js);


// AmosIcons::map($this);
$this->params['widgetSelected'] = $widgetSelected;
$this->params['checkedByDefault'] = true;


$max = \open20\amos\community\models\base\CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $model->id])->max("n_order");
$min = \open20\amos\community\models\base\CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $model->id])->min("n_order");
$community = $model;
if($model->isNewRecord || !$isSaved) {
    $max = $providerGraphic->totalCount;
    $min = $providerGraphic->totalCount == 0 ? 0 : 1;
}

if (!empty(\Yii::$app->params['bsVersion']) && \Yii::$app->params['bsVersion'] == '4.x') : ?>
    <div class="dashboard-default-index">
        <div class="my-4">
            <!--<h2>< ?= AmosDashboard::tHtml('amosdashboard', 'Plugins'); ?></h2>-->
            <?= Html::tag('h5', AmosDashboard::t('amosdashboard', 'Plugins'), ['class' => 'subtitle-form font-weight-bold']) ?>
        </div>

        <div class="plugin-list dashboard-content pb-5">
            <?= \open20\amos\core\views\ListView::widget(
                [
                    'dataProvider' => $providerIcon,
                    'itemOptions' => ['class' => 'border-bottom border-white'],
                    'itemView' => '@vendor/open20/amos-community/src/views/configure-dashboard/_listPlugin',
                ]
            ) ?>

            <div class="mt-4">
                <!--<h2>< ?= AmosDashboard::tHtml('amosdashboard', 'Widget'); ?></h2>-->
                <?= Html::tag('h5', AmosDashboard::t('amosdashboard', 'Widget'), ['class' => 'subtitle-form font-weight-bold']) ?>
            </div>

            <?=
                AmosGridView::widget([
                    'dataProvider' => $providerGraphic,
                    'summary' => false,
                    'columns' => [
                        [
                            'class' => 'open20\amos\core\views\grid\CheckboxColumn',
                            'name' => 'amosWidgetsIds[]',
                            'checkboxOptions' => function ($model, $key, $index, $column) {
   
                                return [
                                    'id' => \yii\helpers\StringHelper::basename($model['classname']),
                                    'value' => $model['id'],
                                    'checked' => empty($this->params['widgetSelected']) ? true : in_array($model['id'], $this->params['widgetSelected'])
                                ];
                            }
                        ],
                        [
                            'label' => 'Nome',
                            'format' => 'html',
                            'attribute' => 'classname',
                            'value' => function ($model) {
                                $object = \Yii::createObject($model['classname']);
                                return $object->getLabel();
                            }
                        ],
                        [
                            'label' => 'Descrizione',
                            'value' => function ($model) {
                                $object = \Yii::createObject($model['classname']);
                                return $object->getDescription();
                            }
                        ],
                    ]
                ]);
            ?>
        </div>
    </div>

<?php else : ?>

    <div class="dashboard-default-index">

        <div class="mt-4">
            <!--<h2>< ?= AmosDashboard::tHtml('amosdashboard', 'Widget'); ?></h2>-->
            <?= Html::tag('h2', AmosDashboard::t('amosdashboard', 'Plugin'), ['class' => 'subtitle-form font-weight-bold']) ?>
        </div>

        <div class="plugin-list dashboard-content">
            <?= \open20\amos\core\views\ListView::widget(
                [
                    'dataProvider' => $providerIcon,
                    'itemOptions' => ['class' => 'col-md-4 flex-column-item'],
                    'itemView' => '@vendor/open20/amos-community/src/views/configure-dashboard/_icon',
                ]
            ) ?>

            <div class="">
                <!--<h2>< ?= AmosDashboard::tHtml('amosdashboard', 'Widget'); ?></h2>-->
                <?= Html::tag('h2', AmosDashboard::t('amosdashboard', 'Widget'), ['class' => 'subtitle-form']) ?>
                <p><?= AmosCommunity::t('amoscommunity',"Salva prima di ordinare i nuovi widget attivati")?></p>
            </div>


            <?=
                AmosGridView::widget([
                    'dataProvider' => $providerGraphic,
                    'summary' => false,
                    'columns' => [
                        //                [
                        //                    'attribute' => 'module',
                        //                    'label' => 'Plugin',
                        //                ],
                        [
                            'class' => 'open20\amos\core\views\grid\CheckboxColumn',
                            'name' => 'amosWidgetsIds[]',
                            'checkboxOptions' => function ($model, $key, $index, $column) {
                                return [
                                    'id' => \yii\helpers\StringHelper::basename($model['classname']),
                                    'value' => $model['id'],
                                    'checked' => empty($this->params['widgetSelected']) ? true : in_array($model['id'], $this->params['widgetSelected'])
                                ];
                            }
                        ],
                        [
                            'label' => 'Icona',
                            'contentOptions' => ['class' => 'icona'],
                            'format' => 'html',
                            'value' => function ($model) {
                                $backgrounColor = 'color-border-mediumBase';
                                return '<p class="' . $backgrounColor . '">' . AmosIcons::show('view-web') . '</p>';
                            }
                        ],
                        [
                            'label' => 'Nome',
                            'format' => 'html',
                            'attribute' => 'classname',
                            'value' => function ($model) {
                                $object = \Yii::createObject($model['classname']);
                                return $object->getLabel();
                            }
                        ],
                        [
                            'label' => 'Descrizione',
                            'value' => function ($model) {
                                $object = \Yii::createObject($model['classname']);
                                return $object->getDescription();
                            }
                        ],
                        [
                            'label' => 'Ordina',
                            'value' => function ($model, $key) use ($max, $min, $community, $isSaved) {
                                $buttons = '';
                                $widgetMm = \open20\amos\community\models\CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $community->id, 'amos_widgets_id' => $model->id])->one();
                                $nOrder = ($community->isNewRecord || !$isSaved) ? ($key+1) : $widgetMm->n_order;
                                if($widgetMm || $community->isNewRecord) {
//                                    if ($nOrder != $min) {
                                        $buttons .= Html::a(
                                            AmosIcons::show('long-arrow-up'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/order-slider',
                                                'id' => $model->id,
                                                'direction' => 'up',
                                                'urlRedirect' => urlencode('/events/event-dashboard/landing-manage-contents?id=' . $community->id),
                                                'useCrop' => true, 'cropRatio' => '2.2'
                                            ],
                                            [
                                                'class' => 'move-up btn btn-xs btn-icon',
                                                'data-toggle' => 'tooltip',
                                                'title' => AmosCommunity::t('amosevents', 'Sposta sopra')
                                            ]
                                        );
//                                    }
//                                    if ($nOrder != $max) {
                                        $buttons .= Html::a(
                                            AmosIcons::show('long-arrow-down'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/order-slider', 'id' => $model->id,
                                                 'direction' => 'down', 'urlRedirect' => urlencode('/events/event-dashboard/landing-manage-contents?id=' . $community->id)
                                            ],
                                            [
                                                'class' => 'move-down btn btn-xs btn-icon',
                                                'data-toggle' => 'tooltip',
                                                'title' => \open20\amos\community\AmosCommunity::t('amosevents', 'Sposta sotto')
                                            ]
                                        );
//                                    }
                                }
                                $input = Html::hiddenInput('orderWidgets[]', $model->id);
                                return $buttons.$input;
                            },
                            'format' => 'raw'
                        ],
                    ]
                ]);
            ?>
        </div>
    </div>

<?php endif; ?>