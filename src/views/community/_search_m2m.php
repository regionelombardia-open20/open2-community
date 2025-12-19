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
use open20\amos\community\models\Community;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\search\CommunitySearch $model
 * @var yii\widgets\ActiveForm $form
 */

$moduleTag = Yii::$app->getModule('tag');

/** @var AmosCommunity $moduleCommunity */
$moduleCommunity = Yii::$app->getModule(AmosCommunity::getModuleName());
/* $enableAutoOpenSearchPanel = !isset(\Yii::$app->params['enableAutoOpenSearchPanel']) || \Yii::$app->params['enableAutoOpenSearchPanel'] === true; */
$enableAutoOpenSearchPanel = Yii::$app->request->post('enableSearch',0);

?>
<div class="community-search element-to-toggle <?= $enableAutoOpenSearchPanel ? 'toggleIn' : '' ?>" data-toggle-element="form-search">

    <?= Html::hiddenInput("enableSearch", $enableAutoOpenSearchPanel); ?>
    <?= Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView')); ?>

    <div class="col-xs-12">
        <p class="h3"><?= AmosCommunity::tHtml('amoscommunity', 'Search by') ?></p>
    </div>

    <?php if (!$moduleCommunity->hideCommunityTypeSearchFilter): ?>
        <div class="col-sm-6 col-lg-4">
            <?= Html::label($model->getAttributeLabel('communityType'), 'community_type_id') ?>
            <?= Select2::widget([
                    'name' => Html::getInputName($model, 'community_type_id'),
                    'value' => Yii::$app->request->post('Community') ? Yii::$app->request->post('Community')['community_type_id'] ?? null : null,
                    'data' => CommunityUtil::getCommunityTypeReadyForSelect(),
                    'language' => 'it',
                    'options' => ['multiple' => false,
                        'id' => 'communityType' . $model->id,
                        'placeholder' => AmosCommunity::t('amoscommunity', 'Select') . '...',
                        'class' => 'dynamicCreation',
                        'data-model' => 'community-type',
                        'data-field' => 'id',
                        'data-module' => 'community',
                        'data-entity' => 'community-type',
                        'data-toggle' => 'tooltip',
                        'disabled' => false
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],         
                ])
            ?>
        </div>
    <?php endif; ?>

<?php if (isset($moduleTag) && in_array(Community::className(), $moduleTag->modelsEnabled) && $moduleCommunity->searchByTags && $moduleTag->behaviors): ?>
<div class="col-xs-12">
<?php
          $params = \Yii::$app->request->getQueryParams();
          /*echo \open20\amos\tag\widgets\TagWidget::widget([
            'model' => $model,
            'attribute' => 'tagValues',
            'form' => $form,
            'isSearch' => true,
            'form_values' => isset($params[$model->formName()]['tagValues']) ? $params[$model->formName()]['tagValues'] : []
            ]);*/
          ?>
      </div>
<?php endif; ?>

<div class="col-xs-12">
  <div class="pull-right">
  <?= Html::a(AmosCommunity::tHtml('amoscommunity', 'Cancel'), Url::current(),
    ['class' => 'btn btn-secondary', 'title' => AmosCommunity::t('amoscommunity', 'Cancel search')])
  ?>
    <?= Html::submitButton(AmosCommunity::tHtml('amoscommunity', 'Filtra'), ['class' => 'btn btn-outline-tertiary', 'title' => AmosCommunity::t('amoscommunity', 'Start search'), 'onclick'=>'
            $("input[name=\"genericSearch\"]").val("");
            $(".element-to-toggle[data-toggle-element=\"form-search\"]").find("input[name=\"enableSearch\"]").val(1);              
     ']) 
    ?>
  </div>
</div>

<div class="clearfix"></div>

</div>