<?php

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;

/**
 * @var $redeemUrl
 * @var $msInvitation \open20\amos\community\models\clients\msmodels\MsInvitation
 * @var $displayName
 * @var $email
 */
$this->title = AmosCommunity::t('amoscommunity', 'Connetti il profilo a Microsoft Teams');
?>
<h5> <?= AmosCommunity::t('amoscommunity', 'Dati utente Microsoft') ?></h5>
<div class="row">
    <div class="col-md-12">
        <strong><?= AmosCommunity::t('amoscommunity', 'Nome utente') . ': ' ?></strong><?= $displayName ?><br>
        <strong><?= AmosCommunity::t('amoscommunity', 'email') . ': ' ?></strong><?= $email ?><br>
        <?php AmosCommunity::t('amoscommunity', 'Per connettere il tuo account microsoft clicca su connetti e dai il tuo consenso per l\'utilizzo dei tuoi dati.') ?>
    </div>
</div>
<div class="row m-t-20">
    <div class="col-md-12">
        <?= Html::a("Connetti", $redeemUrl, ['class' => 'btn btn-primary']) ?>
    </div>
</div>
