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
use open20\amos\community\AmosCommunity;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\CommunityUserField $model
 * @var yii\widgets\ActiveForm $form
 */


?>
<div class="community-user-field-form col-xs-12 nop">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'community-user-field_' . ((isset($fid)) ? $fid : 0),
            'data-fid' => (isset($fid)) ? $fid : 0,
            'data-field' => ((isset($dataField)) ? $dataField : ''),
            'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
            'class' => ((isset($class)) ? $class : '')
        ]
    ]);
    ?>
    <?php // $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

    <div class="row">
        <div class="col-xs-12">
            <?= Html::tag('h2', AmosCommunity::t('amoscommunity', '#settings_contents_title'), ['class' => 'subtitle-form']) ?>
            <div class="col-md-8 col xs-12">
                <?php if(!empty($model->community_id)) { ?>
                        <p><strong><?= AmosCommunity::t('amoscommunity', 'Community') ?></strong>: <?= $community->name?></p>
                        <?=  $form->field($model, 'community_id')->hiddenInput()->label(false);?>
               <?php  } else { ?>
                    <?= $form->field($model, 'community_id')->widget(Select::classname(), [
                        'data' => ArrayHelper::map(\open20\amos\community\models\Community::find()->asArray()->all(), 'id', 'name'),
                        'language' => substr(Yii::$app->language, 0, 2),
                        'options' => [
                            'placeholder' => 'Seleziona ...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        'pluginEvents' => [
                        ]
                    ])->label(AmosCommunity::t('amoscommunity', 'Community'))
                    ?>
                <?php  }  ?>
                <?= $form->field($model, 'user_field_type_id')->widget(Select::classname(), [
                'data' => ArrayHelper::map(\open20\amos\community\models\CommunityUserFieldType::find()->all(),'id','description'),
                'language' => substr(Yii::$app->language, 0, 2),
                'pluginOptions' => [
                    'allowClear' => true
                ],
                ])->label(AmosCommunity::t('amoscommunity', 'Tipo di campo'))
                ?><!-- name string -->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(AmosCommunity::t('amoscommunity', 'Attributo')) ?><!-- description text -->
                <?= $form->field($model, 'description')->textInput(['rows' => 5])->label(AmosCommunity::t('amoscommunity', 'Nome'));?>
                <?= $form->field($model, 'required')->checkbox();?>
                <?= $form->field($model, 'unique')->checkbox();?>
                <?= $form->field($model, 'validator_classname')
                    ->textInput(['maxlength' => true])
                    ->label(AmosCommunity::t('amoscommunity', 'Validatore')) ?><!-- description text -->

                <!--                --><?php //echo  $form->field($model, 'tooltip')->textarea(['rows' => 5]);
                ?><!-- required integer -->

                <?php
                if(!$model->isNewRecord && $model->user_field_type_id == 4){
                    $urlRedirect  = \Yii::$app->request->get('urlRedirect');
                    echo Html::a(AmosCommunity::t('amoscommunity', 'Aggiungi elemento'), [
                        '/community/community-user-field-default-val/create',
                        'id' => $model->id,
                        'urlRedirect' => $urlRedirect
                    ], [
                        'class' => 'btn btn-navigation-primary'
                    ]);

                    echo \open20\amos\core\views\AmosGridView::widget([
                        'dataProvider' => $dataProviderDefaultVals,
                        'columns' => [
                            'value',
                            [
                                'class' => \open20\amos\core\views\grid\ActionColumn::className(),
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model)use ($urlRedirect){
                                        $url = '/community/community-user-field-default-val/delete?id='.$model->id;
                                        return Html::a(AmosIcons::show('delete'), $url.'&urlRedirect='.$urlRedirect, [
                                            'class' => 'btn btn-danger-inverse'
                                        ]);
                                    },
                                ]

                            ]
                        ]
                    ]);
                }
                ?>
                <?= RequiredFieldsTipWidget::widget(); ?>

                <?= CloseSaveButtonWidget::widget(['model' => $model]); ?>

                <?php ActiveForm::end(); ?></div>
            <div class="col-md-4 col xs-12"></div>
        </div>
        <div class="clearfix"></div>

    </div>
</div>
