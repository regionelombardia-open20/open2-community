<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\views\community
 * @category   CategoryName
 */

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\search\CommunitySearch $model
 * @var yii\widgets\ActiveForm $form
 */

?>
<div class="community-order element-to-toggle" data-toggle-element="form-order">
    
    <?= Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView')); ?>

    <div class="col-xs-12">
        <p class="h3"><?= AmosCommunity::tHtml('amoscommunity', 'Order by') ?></p>
    </div>

    <div class="col-sm-6 col-lg-4">
        <?php
        $labels = $model->getOrderAttributesLabels();      
        foreach ($labels as $key => $label){
            $labels[$key] = AmosCommunity::t('amoscommunity', $label);
        }

        ?>
        <?= Html::label($model->getAttributeLabel('orderAttribute'), 'orderAttribute') ?>
        <?= Select2::widget([
                'name' => 'orderAttribute',
                'value' => Yii::$app->request->post('orderAttribute',null),
                'data' => $labels,              
                'options' => [
                    'multiple' => false,  
                    'placeholder' => AmosCommunity::t('amoscommunity', 'Select') . '...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],         
            ])
        ?>
   
    </div>
    <div class="col-sm-6 col-lg-4">
        <?= Html::label($model->getAttributeLabel('orderType'), 'orderType') ?>
        <?= Select2::widget([
                'name' => 'orderType',
                'value' => Yii::$app->request->post('orderType',null),
                'data' => [
                    SORT_ASC => AmosCommunity::t('amoscommunity', 'Ascendent'),
                    SORT_DESC => AmosCommunity::t('amoscommunity', 'Descendent'),
                ],
                'language' => 'it',
                'options' => ['multiple' => false,                
                ],
                'pluginOptions' => [
                    'allowClear' => false
                ],         
            ])
        ?>
        
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(AmosCommunity::tHtml('amoscommunity', 'Cancel'), Url::current(),
                ['class' => 'btn btn-secondary', 'title' => AmosCommunity::t('amoscommunity', 'Cancel order')]) ?>
            <?= Html::submitButton(AmosCommunity::tHtml('amoscommunity', 'Order'), ['class' => 'btn btn-outline-tertiary', 'title' => AmosCommunity::t('amoscommunity', 'Start order')]) ?>
        </div>
    </div>

    <div class="clearfix"></div>
</div>