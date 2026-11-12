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

use open20\amos\core\helpers\Html;
use open20\amos\core\forms\ActiveForm;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use yii\helpers\Url;
use open20\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;
use yii\redactor\widgets\Redactor;
use yii\helpers\Inflector;
use open20\amos\community\models\Bookmarks;
use open20\amos\community\AmosCommunity;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\Bookmarks $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="bookmarks-form col-xs-12 nop">

    <?php
    $form = ActiveForm::begin([
        'options' => [
            'id' => 'bookmarks_' . ((isset($fid)) ? $fid : 0),
            'data-fid' => (isset($fid)) ? $fid : 0,
            'data-field' => ((isset($dataField)) ? $dataField : ''),
            'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
            'class' => ((isset($class)) ? $class : '')
        ]
    ]);
    ?>

    <?= \open20\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget::widget([
        'form' => $form,
        'model' => $model,
        'workflowId' => Bookmarks::BOOKMARKS_WORKFLOW,
        'classDivIcon' => '',
        'classDivMessage' => 'message',
        'viewWidgetOnNewRecord' => false
    ]); ?>

    <div class="row">
        <div class="col-xs-12 m-t-20">


            <div class="content-form-statisticframe"><!-- titolo string -->
                <div class="col-md-8"><?= $form->field($model, 'titolo')->textInput(['maxlength' => true]) ?></div>
                <!-- link string -->
                <div class="col-md-8"><?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?></div>
                <!-- id community -->
                <?= $form->field($model, 'community_id')->hiddenInput(['value' => $community->id])->label(false); ?>
                <div class="col-md-4">
                    <?php
                    if (\Yii::$app->getUser()->can('BOOKMARKS__CREATE')) {
                        $append = ' canInsert';
                    } else {
                        $append = NULL;
                    } ?>
                </div>

                <!-- TAG -->
                <?php
                $moduleTag = \Yii::$app->getModule('tag');
                $moduleCwh = \Yii::$app->getModule('cwh');
                if (isset($moduleTag) && isset($moduleCwh) && in_array(get_class($model), $moduleTag->modelsEnabled) && $moduleTag->behaviors) { ?>
                    <div class="col-md-12">
                        <div class="receiver-section m-b-10">
                            <div class="tag-section-bookmarks">
                                <div class="">
                                    <?= \open20\amos\cwh\widgets\DestinatariPlusTagWidget::widget([
                                        'model' => $model,
                                        'moduleCwh' => $moduleCwh,
                                        'scope' => $moduleCwh->getCwhScope(),
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-md-12 m-b-30">
                    <?= RequiredFieldsTipWidget::widget(); ?>
                </div>

                <div class="row">
                    <?php
                    echo \open20\amos\workflow\widgets\WorkflowTransitionButtonsWidget::widget([
                        'form' => $form,
                        'model' => $model,
                        'workflowId' => Bookmarks::BOOKMARKS_WORKFLOW,
                        'viewWidgetOnNewRecord' => true,
                        //'closeSaveButtonWidget' => CloseSaveButtonWidget::widget($config),
                        'closeButton' => Html::a(
                            \Yii::t('amoscore', 'Annulla'),
                            ['bookmarks/index', 'id' => $community->id],
                            ['class' => 'btn btn-secondary']
                        ),
                        'initialStatusName' => "BOZZA",
                        'initialStatus' => Bookmarks::BOOKMARKS_STATUS_DRAFT,
                        'statusToRender' => [
                            Bookmarks::BOOKMARKS_STATUS_DRAFT => 'Modifica in corso'
                        ],
                        'hideSaveDraftStatus' => [
                            //1 => CollaborationProposals::STATUS_PUBLISHED
                            //2 => Bookmarks::BOOKMARKS_STATUS_TOVALIDATE
                        ],
                        'additionalButtons' => [],
                        'draftButtons' => [
                            'default' => [
                                'button' => Html::submitButton(
                                    \Yii::t('amoscore', 'Salva'),
                                    ['class' => 'btn btn-workflow']
                                ),
                                'description' => $model->isNewRecord ? \Yii::t('amoscommunity', 'Potrai pubblicare il bookmark in seguito') : \Yii::t('amoscommunity', 'Salva e mantieni lo stato attuale'),
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
