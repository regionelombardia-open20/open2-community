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
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\search\CommunitySearch $model
 * @var yii\widgets\ActiveForm $form
 */



?>
<div class="community-search element-to-toggle" data-toggle-element="form-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]);

    ?>

    <div class="col-xs-12">
        <p class="h3"><?= AmosCommunity::tHtml('amoscommunity', 'Search by') ?></p>
    </div>


    <div class="col-sm-6 col-lg-4">
        <div class="form-group">
        <label class="control-label"><?= AmosCommunity::tHtml('amoscommunity', 'Dominio') ?></label>
        <?= \yii\helpers\Html::textInput('dominio','',['class' => 'form-control'])?>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="form-group">
            <label class="control-label"><?= AmosCommunity::tHtml('amoscommunity', 'Sottoscrizione Id') ?></label>
            <?= \yii\helpers\Html::textInput('subId','',['class' => 'form-control'])?>
        </div>
    </div>

<div class="col-xs-12">
  <div class="pull-right">
  <?= Html::a(AmosCommunity::tHtml('amoscommunity', 'Cancel'), [isset($cancelAction) ? $cancelAction : Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')],
    ['class' => 'btn btn-secondary', 'title' => AmosCommunity::t('amoscommunity', 'Cancel search')])
  ?>
  <?= Html::submitButton(AmosCommunity::tHtml('amoscommunity', 'Search'), ['class' => 'btn btn-navigation-primary', 'title' => AmosCommunity::t('amoscommunity', 'Start search')]) ?>
  </div>
</div>

<div class="clearfix"></div>
<?php ActiveForm::end(); ?>
</div>