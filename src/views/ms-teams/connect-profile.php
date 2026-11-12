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
