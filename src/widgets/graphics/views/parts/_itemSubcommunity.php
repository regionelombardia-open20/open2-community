<?php

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