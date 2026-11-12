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

use yii\helpers\Html;
use \open20\amos\core\icons\AmosIcons;
use open20\amos\community\AmosCommunity;

/**
 * @var $meet \open20\amos\community\models\clients\msmodels\MsCalendarEvent
 */

$startDateTime = $meet->getObjStartDateTime();
$endDateTime = $meet->getObjEndDateTime();


?>
<div class="item-task item-ms-teams">
    <div class="title-item">
        <span class="mdi mdi-calendar mdi-24px"></span>
        <a class='link-meet' href="#" data-key="<?= $hashedId ?>"><?= $meet->subject ?></a>
    </div>
    <div class="item-body">
        <div class="label-mst-meeting"><?= AmosCommunity::t('amoscommunity', "Microsoft Teams Meeting") ?></div>
        <div class="date-item">
            <span class="mdi mdi-calendar-text-outline"></span> <?= $startDateTime->format('d/m/Y') ?> | <?= $startDateTime->format('H:i') ?> - <?= $endDateTime->format('H:i') ?>
        </div>
    </div>
</div>