<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\layout\views\layouts\parts
 * @category   CategoryName
 */

/**
 * @var $model Community
 */

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityType;
use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\helpers\Html;
use open20\amos\layout\assets\BaseAsset;
use open20\amos\core\user\User;
use open20\amos\community\widgets\CommunityHeaderWidget;
use open20\amos\dashboard\models\AmosWidgets;
use open20\amos\community\models\CommunityAmosWidgetsMm;

$asset = BaseAsset::register($this);
open20\amos\community\assets\AmosCommunityAsset::register($this);

$jsReadMore = <<< JS

$("#moreTextJs .changeContentJs > .actionChangeContentJs").click(function(){
    $("#moreTextJs .changeContentJs").toggle();
    $('html, body').animate({scrollTop: $('#moreTextJs').offset().top - 120},1000);
});
JS;
$this->registerJs($jsReadMore);


$communityModule = Yii::$app->getModule('community');

$widgetId = AmosWidgets::find()->select('id')
    ->andWhere(['classname' => 'open20\amos\admin\widgets\icons\WidgetIconUserProfile'])
    ->andWhere(['module' => 'admin'])
    ->andWhere(['status' => 1])->one();

$checkChannelCustomization = CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $model->id])->andWhere(['amos_widgets_id' => $widgetId->id])->count();


$is_children = 0;
if ($model->parent_id) {
    $model = Community::getCommunityRoot($model);
    $is_children = 1;
}

$isOpenCommunity = $model->community_type_id == CommunityType::COMMUNITY_TYPE_OPEN;
$classCommunityType = $model->getColorClassCommunityType();
$status = null;
$viewUrl = '/community/community/view?id=' . $model->id;
if (!\Yii::$app->user->isGuest) {
    $viewUrl = '/community/join/open-join?id=' . $model->id;
    $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
}

$fixedCommunityType = !is_null($communityModule->communityType);
$status = null;
if (!\Yii::$app->user->isGuest) {
    $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
}
$unsubscribeClass = !($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) ? 'not-subscribed' : '';

$communityLogo = $model->communityLogo;
if($isOrganizzazioniCommunity) {
    $organizzazione = \open20\amos\organizzazioni\models\Profilo::findOne(['community_id' => $model->id]);
    $communityLogo = $organizzazione->logoOrganization;
}
?>
<?php if (isset($model)) { ?>
    <?php $fixedCommunityType = (!is_null($communityModule->communityType)); ?>
    <div class="community-scope-wrapper community-scope-large bg-<?= $classCommunityType ?>">
        <div class="container">
            <div class="scope-header-container <?= $unsubscribeClass ?>">
                <div class="row">
                    <div class="col-md-9  left-scope <?= !($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) ? 'col-sm-7' : 'col-sm-8 col-xs-9' ?>">
                        <?php
                        if($isOrganizzazioniCommunity) :
                            ?>
                            <div>
                                <a href="/site/to-menu-url?url=/it/organizzazioni/profilo/index" class="link-all flexbox align-items-center" title="Visualizza la lista delle organizzazioni">
                                    <span class="am am-arrow-left"></span>
                                    <span><?= AmosCommunity::t('amosorganizzazioni', 'Tutte le organizzazioni') ?></span>
                                </a>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div class="d-flex">
                            <div class="image-community">
                                <?php $url = '/img/img_default.jpg';
                                if (!is_null($communityLogo)) {
                                    $url = $communityLogo->getWebUrl('item_community', false, true);
                                }
                                echo Html::img(
                                    $url,
                                    [
                                        'class' => 'img-responsive shimmer-image',
                                        'alt' => ''
                                    ]
                                ); ?>
                                <?php if ($model->enable_msgraph_teams) { ?>
                                    <div class="icon-ms-teams">
                                        <span data-toggle="tooltip" title="Community collegata a Microsoft Teams" class="mdi mdi-microsoft-teams"></span>
                                    </div>
                                <?php } ?>
                            </div>


                            <div class="content-text-header">
                                <div class="title-community-header">
                                    <div class="title-community">
                                        <?php if ($is_children) { ?>
                                            <a href="<?= '/community/join/open-join?id=' . $model->id; ?>" title="<?= $model->name ?>">
                                                <h1 class="h3"><?= $model->name ?></h1>
                                            </a>
                                        <?php } else { ?>
                                            <h1 class="h3"><?= $model->name ?></h1>
                                        <?php } ?>
                                    </div>
                                    <a class="link-modal-community-info" data-toggle="modal" data-target="#modal-community-info" href="javascript:void(0)" title="<?= AmosCommunity::t('amoscommunity', 'Informazioni community') ?>">
                                        <sup class="mdi mdi-information-outline mdi-18px"></sup>
                                    </a>
                                </div>

                                <?php if (!$fixedCommunityType) { ?>
                                    <?php $array_info = CommunityUtil::getCommunityTypeConfigs($model); ?>
                                    <div class="chip chip-community chip-<?= $classCommunityType ?>">
                                        <span class="chip-icon <?= $array_info['iconCommunityType'] ?>"></span><span class="chip-label"><?= $array_info['textCommunityType'] ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-3  right-scope <?= !($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) ? 'col-sm-5' : 'col-sm-4 col-xs-3' ?>">
                        <div class="actions-scope">
                            <div class="actions-scope-top">

                                <?php if (!($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED)) {
                                    echo $this->render('_user_subscribe_unsubscribe_buttons', ['community' => $model, 'user' => User::findOne(\Yii::$app->user->id)]);
                                } else {
                                    echo \open20\amos\community\widgets\ManageUserCommunityInfo::widget(['community' => $model, 'user' => User::findOne(\Yii::$app->user->id)]);
                                }
                                ?>
                                <div>
                                    <?php
                                    if(!$isOrganizzazioniCommunity) {
                                        echo ContextMenuWidget::widget([
                                            'title' => AmosCommunity::t('amoscommunity', 'Menu contestuale di {community}', ['community' => $model->name]),
                                            'model' => $model,
                                            'actionModify' => "/community/community/update?id=" . $model->id,
                                            'actionDelete' => "/community/community/delete?id=" . $model->id,
                                            // 'layout' => '@vendor/open20/amos-layout/src/views/widgets/context_menu_widget_network_scope.php'
                                        ]);
                                    } else {
                                        echo ContextMenuWidget::widget([
                                            'title' => AmosCommunity::t('amoscommunity', 'Menu contestuale di {community}', ['community' => $model->name]),
                                            'model' => $model,
                                            'actionModify' => "/organizzazioni/profilo/update?id=" . $organizzazione->id,
                                            'actionDelete' => "/organizzazioni/profilo/delete?id=" . $organizzazione->id,
                                            // 'layout' => '@vendor/open20/amos-layout/src/views/widgets/context_menu_widget_network_scope.php'
                                        ]);
                                    }
                                    ?></div>
                            </div>

                            <?php if ($communityModule->showSubcommunities && !$isOrganizzazioniCommunity) { ?>
                                <div class="actions-scope-bottom">
                                    <!-- ----------  SEZIONE CANALI   -----------     -->

                                    <div class="dropdown wrapper-dropdown-channel">
                                        <button type="button" class="btn btn-channel dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?= AmosCommunity::t('amoscommunity', 'Menu canali di {community}', ['community' => $model->name]) ?>">
                                            <span class="material-icons-two-tone icon-channel icon-primary">
                                                group_work
                                            </span>
                                            <span class="label-btn-channel"><?= AmosCommunity::t('amoscommunity', 'Canali') ?></span><small class="icon-expand mdi mdi-chevron-down"></small>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-channel">
                                            <div class="dropdown-header">
                                                <div class="dropdown-header-left">
                                                    <div class="dropdown-title">
                                                        <?= AmosCommunity::t('amoscommunity', 'Canali') ?>
                                                    </div>
                                                    <a class="link-allChannel text-uppercase align-items-center" href="<?= '/community/subcommunities/index?id=' . $model->id; ?>" title="<?= AmosCommunity::t('amoscommunity', 'Tutti i canali') ?>">
                                                        <span class=""><?= AmosCommunity::t('amoscommunity', 'Tutti i canali') ?></span>
                                                        <span class="icon mdi mdi-arrow-right-circle-outline"></span>
                                                    </a>
                                                    <div class="more-info">
                                                        <span><?= AmosCommunity::t('amoscommunity', 'Canali della Community "{community}"', ['community' => $model->name]) ?></span>
                                                    </div>
                                                </div>
                                                <div class="dropdown-header-right">
                                                    <?php if ($model->isCommunityManager() && ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED) && !($communityModule->disableSubChannel && $is_children)) { ?>
                                                        <a class="btn btn-xs btn-primary-outline" href="<?= '/community/community/create?parentId=' . $model->id; ?>" title="<?= AmosCommunity::t('amoscommunity', 'Nuovo') ?>">
                                                            <span class="am am-plus-circle-o"></span><span class=""><?= AmosCommunity::t('amoscommunity', 'Nuovo') ?></span>
                                                        </a>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                            <ul class="list-channel-dropdown">
                                                <?php if ($model->getSubcommunities()->count() > 0) { ?>
                                                    <?php echo $this->render('_select_item_community_channels', ['model' => $model, 'communityModule' => $communityModule, 'is_channel' => false]); ?>
                                                <?php } else { ?>
                                                    <li class="no-item-list-channel lead m-b-0">
                                                        <?= AmosCommunity::t('amoscommunity', 'Nessun canale presente') ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>

                                        </div>
                                    </div>

                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="network-scope-wrapper scope-community-wrapper scope-small">
        <p class="link-all m-b-0 flexbox align-items-center p-b-0">
            <span class="ic ic-community m-r-5"></span>

            <a href="/community/join/open-join?id=<?= $model->id ?>" class="" title="<?= AmosCommunity::t('amoscommunity', 'Vai alla community principale') . ':' . ' ' . $model->name ?>">
                <?= $model->name ?>
            </a>
        </p>
    </div>
<?php } ?>


<!-- MODAL INFO GENERAL COMMUNITY -->
<div id="modal-community-info" class="modal modal-community" tabindex="-1" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Description">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span class="mdi mdi-close"></span>
                </button>
                <div class="modal-title h6"><?= AmosCommunity::t('amoscommunity', 'Descrizione community') ?></div>
            </div>
            <div class="modal-body">
                <div class="h3"><?= $model->name ?></div>
                <?php echo $this->render('_modal_item_community', ['model' => $model]); ?>
            </div>

        </div>
    </div>
</div>