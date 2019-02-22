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
use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;
use lispa\amos\community\assets\AmosCommunityAsset;
AmosCommunityAsset::register($this);
/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 */

$moduleDocumenti = \Yii::$app->getModule(AmosCommunity::getModuleName());

?>
<div class="grid-item grid-item--height2">
<div class="box-widget my-community">
    <div class="box-widget-toolbar row nom">
        <h2 class="box-widget-title col-xs-10 nop"><?= AmosCommunity::t('amoscommunity', 'My communities') ?></h2>
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
        <!--<h2 class="sr-only"><?php //= AmosCommunity::t('amoscommunity', 'My communities') ?></h2>-->
        <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>
        <div role="listbox">
            <?php
            if (count($communitiesList->getModels()) == 0) {
                $textReadAll = AmosCommunity::t('amoscommunity', '#addCommunity');
                $linkReadAll = ['/community/community-wizard/introduction'];
                echo '<div class="list-items list-empty clearfixplus"><h2 class="box-widget-subtitle"></h2>' . AmosCommunity::t('amoscommunity', '#noCommunity') . '</div>';
            } else {
                if($linkToSubcommunities) {
                    $textReadAll = AmosCommunity::t('amoscommunity', 'View Community List');
                    $linkReadAll = ['/community/subcommunities/my-communities'];
                } else {
                    $textReadAll = AmosCommunity::t('amoscommunity', 'View Community List');
                    $linkReadAll = ['/community/community/my-communities'];
                } ?>
                <div class="list-items clearfixplus">
                    <?php
                    foreach ($communitiesList->getModels() as $community):
                        /** @var Community $community */
                        ?>
                        <div class="col-xs-12 widget-listbox-option" role="option">
                            <article class="col-xs-12 nop">
                                <div class="container-img">
                                    <?= \lispa\amos\community\widgets\CommunityCardWidget::widget(['model' => $community, 'imgStyleDisableHorizontalFix' => true]) ?>
                                </div>
                                <div class="container-text">
                                    <div class="col-xs-12 nop">
                                        <!-- <p>< ?= Yii::$app->getFormatter()->asDatetime($community->created_at); ?></p>-->
                                        <h2 class="box-widget-subtitle">
                                            <?php
                                            $decode_name = strip_tags($community->name);
                                            if (strlen($decode_name) > 60) {
                                                $stringCut = substr($decode_name, 0, 60);
                                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                            } else {
                                                echo $decode_name;
                                            }
                                            ?>
                                        </h2>
                                    </div>
                                    <div class="col-xs-12 box-widget-text nop nom">
                                        <p>
                                            <?php
                                            $decode_description = strip_tags($community->description);
                                            if (strlen($decode_description) > 60) {
                                                $stringCut = substr($decode_description, 0, 60);
                                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                            } else {
                                                echo $decode_description;
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </article>
                            <div class="footer-listbox col-xs-12 nop">
                                <p class="pull-left"><?= count($community->communityUsers) . " " . AmosCommunity::t('amoscommunity', 'MEMBERS') ?></p>
                                <span class="pull-right">
                                    <?= Html::a(AmosCommunity::t('amoscommunity', 'Sign in'),
                                        ['/community/join', 'id' => $community->id],
                                        ['class' => 'btn btn-navigation-primary', 'data-pjax' => 0]); ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    endforeach;
                    ?>
                </div>
                <?php
                }
            ?>
        </div>
        <?php Pjax::end(); ?>
    </section>
    <div class="read-all"><?= Html::a($textReadAll,$linkReadAll,['class' => '']); ?></div>
</div>
</div>