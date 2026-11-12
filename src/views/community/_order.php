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
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\search\CommunitySearch $model
 * @var yii\widgets\ActiveForm $form
 */

?>
<div class="community-order element-to-toggle" data-toggle-element="form-order">

    <?php $form = ActiveForm::begin([
        'action' => (isset($originAction) ? [$originAction] : ['index']),
        'method' => 'get',
    ]);
    echo Html::hiddenInput("enableOrder", "1");
    echo Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView'));
    ?>

    <div class="col-xs-12">
        <p class="h3"><?= AmosCommunity::tHtml('amoscommunity', 'Order by') ?></p>
    </div>

    <div class="col-sm-6 col-lg-4">
        <?php
        $labels = $model->getOrderAttributesLabels();
        foreach ($labels as $key => $label){
            $labels[$key] = AmosCommunity::t('amoscommunity', $label);
        }?>
        <?= $form->field($model, 'orderAttribute')->dropDownList($labels) ?>
    </div>
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'orderType')->dropDownList(
            [
                SORT_ASC => AmosCommunity::t('amoscommunity', 'Ascendent'),
                SORT_DESC => AmosCommunity::t('amoscommunity', 'Descendent'),
            ]
        )
        ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(AmosCommunity::tHtml('amoscommunity', 'Cancel'), [Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')],
                ['class' => 'btn btn-secondary', 'title' => AmosCommunity::t('amoscommunity', 'Cancel order')]) ?>
            <?= Html::submitButton(AmosCommunity::tHtml('amoscommunity', 'Order'), ['class' => 'btn btn-navigation-primary', 'title' => AmosCommunity::t('amoscommunity', 'Start order')]) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>
</div>