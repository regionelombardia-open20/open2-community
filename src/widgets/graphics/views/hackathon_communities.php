<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\widgets\graphics\views
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
use open20\amos\community\models\Community;
use open20\amos\core\forms\WidgetGraphicsActions;
use open20\amos\core\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;
use open20\amos\community\assets\AmosCommunityAsset;
$assetCommunity = AmosCommunityAsset::register($this);
/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \open20\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 */

$moduleDocumenti = \Yii::$app->getModule(AmosCommunity::getModuleName());

?>
<div class="grid-item">
    <div class="box-widget hackathon-widget">
        <div class="box-widget-toolbar row nom">
            <h2 class="box-widget-title col-xs-10 nop"><?= AmosCommunity::t('amoscommunity', 'Hackathon') ?></h2>
            <?php if(isset($moduleCommunity) && !$moduleCommunity->hideWidgetGraphicsActions) { ?>
                <?= WidgetGraphicsActions::widget([
                    'widget' => $widget,
                    'tClassName' => AmosCommunity::className(),
                    'actionRoute' => ['/community/community-wizard/introduction'],
                    'toRefreshSectionId' => $toRefreshSectionId,
                    'permissionCreate' => 'COMMUNITY_CREATE'
                ]); ?>
            <?php } ?>
        </div>
        <section>
            <?= Html::img($assetCommunity->baseUrl . '/images/hackwidget.jpg') ?>
            <?= Html::a('Scopri di più', $url, ['class' => 'btn btn btn-navigation-secondary']); ?>
        </section>
    </div>
</div>