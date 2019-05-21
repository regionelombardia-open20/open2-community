<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\widgets\graphics\views
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\widgets\JoinCommunityWidget;
use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;
use lispa\amos\community\assets\AmosCommunityAsset;
use lispa\amos\core\icons\AmosIcons;

\lispa\amos\dashboard\assets\DashboardFullsizeAsset::register($this);


/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 */

$moduleDocumenti = \Yii::$app->getModule(AmosCommunity::getModuleName());

?>
<div class="box-widget-header">
    <?php if(isset($moduleCommunity) && !$moduleCommunity->hideWidgetGraphicsActions) { ?>
        <?= WidgetGraphicsActions::widget([
            'widget' => $widget,
            'tClassName' => AmosCommunity::className(),
            'actionRoute' => ['/community/community-wizard/introduction'],
            'toRefreshSectionId' => $toRefreshSectionId,
            'permissionCreate' => 'COMMUNITY_CREATE'
        ]); ?>
    <?php } ?>
    
    <div class="box-widget-wrapper">
        <h2 class="box-widget-title">
            <?= AmosIcons::show('community', ['class' => 'am-2'], AmosIcons::IC)?>
            <?= AmosCommunity::tHtml('amoscommunity', 'My communities') ?>
        </h2>
    </div>

    <?php
    if (count($communitiesList->getModels()) == 0) {
        $textReadAll = AmosCommunity::t('amoscommunity', '#addCommunity');
        $linkReadAll = ['/community/community-wizard/introduction'];

    } else {
        if ($linkToSubcommunities) {
            $textReadAll = AmosCommunity::t('amoscommunity', '#showAll');
            $linkReadAll = ['/community/subcommunities/my-communities'];
        } else {
            $textReadAll = AmosCommunity::t('amoscommunity', '#showAll') . AmosIcons::show('chevron-right');
            $linkReadAll = ['/community/community/my-communities'];
        }
    }?>
    <div class="read-all"><?= Html::a($textReadAll,$linkReadAll,['class' => '']); ?></div>
</div>
<div class="box-widget box-widget-column my-community">
    <section>
        <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>

        <?php if (count($communitiesList->getModels()) == 0): ?>
            <div class="list-items list-empty"><h3><?= AmosCommunity::t('amoscommunity', '#noCommunity') ?></h3></div>
        <?php endif; ?>
            <div class="list-items">
                <?php
                foreach ($communitiesList->getModels() as $community):
                    /** @var Community $community */
                    ?>
                    <div class="widget-listbox-option" role="option">
                        <article class="wrap-item-box">
                            <div>
                                <div class="container-img">
                                    <?= \lispa\amos\community\widgets\CommunityCardWidget::widget([
                                        'model' => $community,
                                        'imgStyleDisableHorizontalFix' => true,
                                    ]) ?>
                                </div>
                            </div>
                            <div class="container-text">
<!--                                <div class="box-widget-info-top">-->
<!--                                    <p>< ?= Yii::$app->getFormatter()->asDatetime($community->created_at); ?></p>-->
<!--                                </div>-->
                                <h2 class="box-widget-subtitle">
                                    <?php
                                    $decode_name = strip_tags($community->name);
                                    $decoded_name = '';
                                    if (strlen($decode_name) > 150) {
                                        $stringCut = substr($decode_name, 0, 150);
                                        $decoded_name = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                    } else {
                                        $decoded_name =  $decode_name;
                                    }
                                    ?>

                                    <?= Html::a($decoded_name, ['/community/community/view', 'id' => $community->id]) ?>
                                </h2>
                                <p class="box-widget-text">
                                    <?php
                                    $decode_description = strip_tags($community->description);
                                    if (strlen($decode_description) > 300) {
                                        $stringCut = substr($decode_description, 0, 300);
                                        echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                    } else {
                                        echo $decode_description;
                                    }
                                    ?>
                                </p>
                                <div class="box-widget-info-bottom">
                                    <span><?= $community->getCommunityUsers()->count() ?></span>
                                    <span><?= AmosCommunity::t('amoscommunity', 'Participants') ?></span>
                                </div>
                            </div>

                            <div class="footer-listbox">
                                <?= JoinCommunityWidget::widget(['model' => $community ]) ?>
                            </div>
                        </article>
                    </div>
                <?php
                endforeach;
                ?>
            </div>
        <?php Pjax::end(); ?>
    </section>
</div>