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
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\search\CommunitySearch;
?>


<?php

if($is_channel){
	$modelSearch = new CommunitySearch(['subcommunityMode' => true,'fixedId' => $model->id]);
}else{
	if($model->parent_id){
		$modelSearch = new CommunitySearch(['subcommunityMode' => true,'fixedId' => $model->parent_id]);
	}else{
		$modelSearch = new CommunitySearch(['subcommunityMode' => true,'fixedId' => $model->id]);
	}
}
$dataProvider = $modelSearch->searchAll(Yii::$app->request->getQueryParams());


$subcommunitiesQuery = $dataProvider->query->leftJoin('community_user_mm cu', 'cu.community_id = community.id AND cu.user_id = :userId', [':userId' => \Yii::$app->user->id])
    ->orderBy([new \yii\db\Expression('ISNULL(cu.id) ASC, community.created_at DESC')])
->andWhere(['parent_id' => $model->id])
->limit(5)->all();



foreach ($subcommunitiesQuery as $subCom) {

    $isOpenCommunity = false;
    $isClosedCommunity = false;
    $isPrivateCommunity = false;
    $isWaitingToSigned = false;
    $isInvited = false;

    $array_info = CommunityUtil::getCommunityTypeConfigs($subCom);
    $iscritto = CommunityUtil::getUserCommunitySubscriptionStatus($subCom);

    if (!\Yii::$app->user->isGuest) {
        $viewUrl = '/community/join/open-join?id=' . $subCom->id;
        if ($array_info['classType'] != 'open') {
            if ($isWaitingToSigned) {
                $viewUrl = '/community/community/view?id=' . $subCom->id;
            }
        }
    } else {
        $viewUrl = '/community/community/view?id=' . $subCom->id;
    }
?>

    <li class="item-list-channel">
        <div class="img-channel-list">
            <?php
            $url = '/img/img_default.jpg';
            if (!is_null($subCom->communityLogo)) {
                $url = $subCom->communityLogo->getWebUrl('square_small', false, true);
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
            <a href="<?= $viewUrl ?>" title="<?= $subCom->name ?>" class="link-list-channel">
                <?= $subCom->name ?>
            </a>
            <div class="label-partecipanti text-secondary">
                <span><?= $subCom->getCommunityUserMms()->count() ?> <?= AmosCommunity::t('amoscommunity', 'Partecipanti') ?></span>
            </div>
        </div>
        <div class="status-iscrizione-channel text-secondary">
            <!-- TO DO da fare if se hai un canale con invito in sospeso -->
            <?= $iscritto ?>
        </div>

    </li>
<?php

} ?>