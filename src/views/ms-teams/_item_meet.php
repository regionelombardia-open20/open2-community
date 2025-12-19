<?php

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