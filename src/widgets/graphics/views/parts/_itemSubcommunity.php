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

use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;

/**
 * @var $model \open20\amos\community\models\CommunityUserMm
 */
?>
<?php
$isOpenCommunity = false;
$isClosedCommunity = false;
$isPrivateCommunity = false;
$isWaitingToSigned = false;
$isInvited = false;

$array_info = CommunityUtil::getCommunityTypeConfigs($model);
$iscritto = CommunityUtil::getUserCommunitySubscriptionStatus($model);

if (!\Yii::$app->user->isGuest) {
    $viewUrl = '/community/join/open-join?id=' . $model->id;
    if ($array_info['classType'] != 'open') {
        if ($isWaitingToSigned) {
            $viewUrl = '/community/community/view?id=' . $model->id;
        }
    }
} else {
    $viewUrl = '/community/community/view?id=' . $model->id;
}
?>

<div class="img-channel-list">
    <?php
    $url = '/img/img_default.jpg';
    if (!is_null($model->communityLogo)) {
        $url = $model->communityLogo->getWebUrl('square_small', false, true);
    }
    echo Html::img(
        $url,
        [
            'class' => 'img-responsive shimmer-image',
            'alt' => ''
        ]
    );
    ?>
    <span class="icon-community-status bg-community-<?= $array_info['classType'] ?>" data-toggle="tooltip" title="<?= $array_info['textCommunityType'] ?>">
        <span class="icon icon-white icon-sm <?= $array_info['iconCommunityType'] ?>"></span>
    </span>
</div>
<div class="title-channel-list">
    <a href="<?= $viewUrl ?>" data-pjax='0' aria-label="<?= AmosCommunity::t('amoscommunity', 'Accedi al canale {nome}',['nome' => $model->name]) ?>" title="<?= $model->name ?>" class="link-list-channel">
        <?= $model->name ?>
    </a>
    <div class="label-partecipanti text-secondary">
        <span><?= $model->getCommunityUserMms()->count() ?> <?= AmosCommunity::t('amoscommunity', 'Partecipanti') ?></span>
    </div>
</div>
<div class="status-iscrizione-channel text-secondary">
    <!-- TO DO da fare if se hai un canale con invito in sospeso -->
    <?= $iscritto ?>
</div>