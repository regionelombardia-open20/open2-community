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
use open20\amos\community\assets\AmosCommunityAsset;
use open20\amos\community\widgets\graphics\WidgetGraphicsMyCommunities;
use open20\amos\core\forms\WidgetGraphicsActions;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\views\DataProviderView;
use open20\amos\dashboard\assets\DashboardFullsizeAsset;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

DashboardFullsizeAsset::register($this);
AmosCommunityAsset::register($this);

/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 * @var bool $linkToSubcommunities
 */
$moduleCommunity = Yii::$app->getModule(AmosCommunity::getModuleName());
?>

<?php
$modelLabel      = 'user';

if (!\Yii::$app->user->isGuest && \Yii::$app->user->id != Yii::$app->params['platformConfigurations']['guestUserId']) {
	$titleSection = 'Partecipanti';
	$linkCta = '/amosadmin/user-profile/index';
	$labelCreate = 'Visualizza tutti';
    $titleCreate = 'Visualizza la lista dei partecipanti';
} else {
    $titleSection = 'Ultimi partecipanti';
}
?>

<div class="widget-graphic-cms-bi-less card-<?= $modelLabel ?> container">
    <div class="page-header">
        <?=
        $this->render(
            "@vendor/open20/amos-layout/src/views/layouts/fullsize/parts/bi-less-plugin-header",
            [
				'titleSection' => $titleSection,
				'titleCreate' => $titleCreate,
                'linkCta' => $linkCta,
                'labelCreate' => $labelCreate,
                'hideCreate' => true
            ]
        );
        ?>
    </div>

    <section>
        <?php
        $usersList = $dataProviderViewWidgetConf['dataProvider'];
        if (count($usersList->getModels()) == 0):
            ?>
            <div class="list-empty">
                <h3>
                    <?= AmosCommunity::t('amoscommunity', 'Nessun partecipante') ?>
                </h3>
            </div>
            <?php
        else:
            if ($searchButtons != null) :
                ?>
                <div class="search-buttons">
                    <?php
                    foreach ($searchButtons as $button) {
                        echo $button;
                    }
                    ?>	</div>
                <?php
            endif;
            Pjax::begin(['id' => $toRefreshSectionId, 'timeout' => 15000]);
            //$pagination = $usersList->getPagination();print_r("<pre>");print_r($pagination);die;
            echo DataProviderView::widget($dataProviderViewWidgetConf);

            Pjax::end();
        endif;
        ?>
    </section>
</div>