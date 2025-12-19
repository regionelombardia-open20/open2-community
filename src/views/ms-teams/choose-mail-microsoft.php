<?php

use open20\amos\community\AmosCommunity;

/**
 * @var $model
 */

$this->title = AmosCommunity::t('amoscommunity', 'Connetti il profilo a Microsoft Teams');
?>

<?php
$form = \open20\amos\core\forms\ActiveForm::begin() ?>
<p><?= AmosCommunity::t('amoscommunity',"Scegli l'email del tuo account microsoft che vuoi collegare")?></p>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model,'email')->label('email')?>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <?= \open20\amos\core\helpers\Html::submitButton(AmosCommunity::t('amoscommunity',"Procedi"),[
                'class' => 'btn btn-primary',
                'title' => AmosCommunity::t('amoscommunity',"Procedi")
        ])?>
    </div>
</div>
<?php
\open20\amos\core\forms\ActiveForm::end();?>
