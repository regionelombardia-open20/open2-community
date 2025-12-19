<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\widgets\graphics\views
 * @category   CategoryName
 */

use open20\amos\community\AmosCommunity;
use open20\amos\community\assets\AmosCommunityAsset;
use open20\amos\community\models\Community;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;

AmosCommunityAsset::register($this);

/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \open20\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 * @var bool $linkToSubcommunities
 */

$moduleCommunity = \Yii::$app->getModule(AmosCommunity::getModuleName());
$communitiesModels = $communitiesList->getModels();
$textReadAll = AmosCommunity::t('amoscommunity', 'Tutti canali');
$linkReadAll = '/community/subcommunities/index?id=' . $model->id;
?>

<div class="widget-graphic-cms-bi-less">
    <div class="box-new-widget my-community">
        <div class="page-header header-new-widget">
            <div class="bi-plugin-header">
                <div class="flexbox title-heading-plugin">
                    <div class="title-new-widget m-r-10">
                        <?= AmosCommunity::t('amoscommunity', 'Ultimi canali creati') ?>
                    </div>

                    <a href="<?= $linkReadAll ?>" aria-label=" <?= AmosCommunity::t('amoscommunity', 'Visualizza la lista di tutti i canali') ?>" class="link-all-discussioni text-uppercase align-items-center small">
                        <span><?= $textReadAll ?></span>
                        <span class="icon mdi mdi-arrow-right-circle-outline"></span>
                    </a>
                </div>
                <?php if ($model->isCommunityManager() && ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED)) { ?>

                    <div class="cta-wrapper">
                        <div class="flexbox manage-cta-container">
                            <a class="btn btn-xs btn-primary" aria-label="<?= AmosCommunity::t('amoscommunity', 'Crea un nuovo canale per {titolo}',['titolo' => $model->name])?>" href="<?= '/community/community/create?parentId=' . $model->id; ?>" title="<?= AmosCommunity::t('amoscommunity', 'Nuovo') ?>">
                                <span class="am am-plus-circle-o"></span><span class=""><?= AmosCommunity::t('amoscommunity', 'Nuovo') ?></span>
                            </a>
                        </div>
                    </div>
                <?php
                } ?>

            </div>
        </div>

        <div class="widget-body">
            <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>
            <div role="listbox">

                <div class="widget-channel-list">
                    <?= \open20\amos\core\views\ListView::widget(
                        [
                            'dataProvider' => $communitiesList,
                            'itemOptions' => ['class' => 'item-list-channel'],
                            'itemView' => '@vendor/open20/amos-community/src/widgets/graphics/views/parts/_itemSubcommunity',
                        ]
                    ) ?>
                </div>

            </div>
            <?php Pjax::end(); ?>

        </div>
    </div>

</div>