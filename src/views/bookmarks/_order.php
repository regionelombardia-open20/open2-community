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

use \open20\amos\community\AmosCommunity;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\search\BookmarksSearch $model
 * @var yii\widgets\ActiveForm $form
 */
$currentView = (isset($currentView)) ? $currentView : Yii::$app->request->getQueryParam('currentView');
$community_id = Yii::$app->request->getQueryParam('id');
?>

<div class="news-order element-to-toggle" data-toggle-element="form-order">
    <div class="col-xs-12">
        <p class="h3"><?= amoscommunity::t('amoscommunity', 'Order by') ?>:</p>
    </div>

    <?php $form = ActiveForm::begin([
        'action' => ['index', 'id' => $community_id],
        'method' => 'get',
        'options' => [
            'class' => 'default-form2'
        ]
    ]);
    //    echo Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView'));
    echo Html::hiddenInput("currentView", $currentView);
    echo Html::hiddenInput("BookmarksSearch[titolo]", Yii::$app->request->getQueryParam('BookmarksSearch')['titolo']);
    ?>

    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'orderAttribute')->dropDownList(['bookmarks.data_pubblicazione' => 'Data di pubblicazione'])
            ->label(amoscommunity::t('amoscommunity', 'Order attribute')) ?>
    </div>
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'orderType')->dropDownList(
            [
                SORT_ASC => amoscommunity::t('amoscommunity', 'Ascending'),
                SORT_DESC => amoscommunity::t('amoscommunity', 'Descending'),
            ]
        )
        ->label(amoscommunity::t('amoscommunity', 'Order type'));
        ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(amoscommunity::t('amoscommunity', 'Cancel'), ['index', 'id' => $community_id],
                ['class'=>'btn btn-secondary']) ?>
            <?= Html::submitButton(amoscommunity::t('amoscommunity', 'Sort'), ['class' => 'btn btn-navigation-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>