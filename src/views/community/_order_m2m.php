<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\views\community
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