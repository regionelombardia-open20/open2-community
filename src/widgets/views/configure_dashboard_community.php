<?php

use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\AmosGridView;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\dashboard\AmosDashboard;
use yii\helpers\Html;

/* * @var \lispa\amos\dashboard\models\AmosUserDashboards $currentDashboard * */
/* * @var \lispa\amos\dashboard\models\AmosWidgets $widgetIconSelectable * */
/* * @var \lispa\amos\dashboard\models\AmosWidgets $widgetGraphicSelectable * */
/* * @var array $widgetSelected * */

/* * @var \yii\web\View $this * */
AmosIcons::map($this);
$this->params['widgetSelected'] = $widgetSelected;
?>
<div class="dashboard-default-index">

    <div class="col-xs-12">
        <h2><?= AmosDashboard::tHtml('amosdashboard', 'Plugins'); ?></h2>
    </div>

    <div class="plugin-list dashboard-content">
        <?= \lispa\amos\core\views\ListView::widget( [
                'dataProvider' => $providerIcon,
                'itemOptions' => ['class' => 'item'],
                'itemView' => '@vendor/lispa/amos-community/src/views/configure-dashboard/_icon',
            ]
        )?>

        <div class="col-xs-12">
            <h2><?= AmosDashboard::tHtml('amosdashboard', 'Widget'); ?></h2>
        </div>

        <?=
        AmosGridView::widget([
            'dataProvider' => $providerGraphic,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'module',
                    'label' => 'Plugin',
                ],
                [
                    'class' => 'lispa\amos\core\views\grid\CheckboxColumn',
                    'name' => 'amosWidgetsIds[]',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return [
                            'id' => \yii\helpers\StringHelper::basename($model['classname']),
                            'value' => $model['id'],
                            'checked' => in_array($model['id'], $this->params['widgetSelected'])
                        ];
                    }
                ],
                [
                    'label' => 'Icona',
                    'contentOptions' => ['class' => 'icona'],
                    'format' => 'html',
                    'value' => function ($model) {
                        $backgrounColor = 'color-border-mediumBase';
                        return '<p class="'.$backgrounColor.'">'.AmosIcons::show('view-web').'</p>';
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




