<?php

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\user\User;
use open20\amos\community\widgets\CommunityHeaderWidget;
use open20\amos\dashboard\models\AmosWidgets;
use open20\amos\community\models\CommunityAmosWidgetsMm;

/**
 * @var $model Community
 */
$communityModule = \Yii::$app->getModule('community');
$communityRoot = Community::getCommunityRoot($model);
$isTeamEnabled = $communityRoot->enable_msgraph_teams;

$fixedCommunityType = (!is_null($communityModule->communityType));

$communityPartecipants = CommunityUtil::getCurrentCommunityMembers();

$userprofileGroup = [];
foreach ($communityPartecipants->all() as $cp) {
    $user = User::findOne($cp->user_id);
    $userprofileGroup[] = $user->getProfile();
}
$status = null;
if (!\Yii::$app->user->isGuest) {
    $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
}

$widgetId = AmosWidgets::find()->select('id')
                ->andWhere(['classname' => 'open20\amos\admin\widgets\icons\WidgetIconUserProfile'])
                ->andWhere(['module' => 'admin'])
                ->andWhere(['status' => 1])->one();

$checkChannelCustomization = CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $model->id])->andWhere(['amos_widgets_id' => $widgetId->id])->count();

$unsubscribeClass = !($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) ? 'not-subscribed' : '';

if( \Yii::$app->controller->id == 'community' && \Yii::$app->controller->action->id == 'view' && $model->parent_id && $status != CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED){
    return '';
}
    ?>
    <div class="navbar-community-wrapper affix-top">
        <div class="row-no">
            <div class="col-xs-12-no">
                <div class="container">
                    <div class="navbar-community">
                        <div class="navbar-community-left w-100">
                            <div class="navbar-menu-plugin root-community">
                                <div class="current-root">
                                    <?=
                                    $this->render('_current_channel', [
                                        'model' => $model,
                                        'fixedCommunityType' => $fixedCommunityType,
                                        'communityRoot' => $communityRoot
                                    ]);
                                    ?>
                                </div>
                            </div>

                            <div class="navbar-menu-plugin menu-links-plugin w-100 mr-auto">
                                <?= \open20\amos\community\widgets\CommunityMenuPluginsWidget::widget(['model' => $model]) ?>
                            </div>
                        </div>

                        <div class="navbar-community-right <?= $unsubscribeClass ?>">
                            <?php if ($checkChannelCustomization > 0) { ?>
                                <div class="community-list-partecipants" aria-label="<?= AmosCommunity::t('amoscommunity', 'Partecipanti di {community}', ['community' => $model->name]) ?>">
                                    <?php
                                    echo $this->render('_partecipants_list', [
                                        'numberExpose' => 5,
                                        'usersNumber' => $communityPartecipants->count(),
                                        'userProfiles' => $userprofileGroup,
                                        'community_id' => $model->id,
                                        'tooltipAdditionalInfoAvatarsGroup' => AmosCommunity::t(
                                                'amoscommunity',
                                                'Partecipante'
                                        ),
                                        'removeLink' => false
                                    ]);
                                    ?>
                                </div>
                            <?php } ?>
                            <!-- TENDINA SOTTOCANALI -->

                            <div class="actions-channel">
                                <?php if (!$isTeamEnabled && $model->parent_id && $communityModule->showSubcommunities && !$communityModule->disableSubChannel ) { ?>

                                    <div class="dropdown wrapper-dropdown-channel">
                                        <button type="button" class="btn btn-channel dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="<?= AmosCommunity::t('amoscommunity', 'Menu sottocanali di "{community}"', ['community' => $model->name]) ?>">
                                            <span class="material-icons-two-tone icon-channel icon-primary">
                                                group_work
                                            </span>
                                            <small class="icon-expand mdi mdi-chevron-down"></small>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-channel">
                                            <div class="dropdown-header">
                                                <div class="dropdown-header-left">
                                                    <div class="dropdown-title">
                                                        <?= AmosCommunity::t('amoscommunity', 'Sottocanali') ?>
                                                    </div>
                                                    <a class="link-allChannel text-uppercase align-items-center"
                                                       href="<?= '/community/subcommunities/index?id=' . $model->id; ?>"
                                                       title="<?= AmosCommunity::t('amoscommunity', 'Nuovo') ?>">
                                                        <span class=""><?= AmosCommunity::t('amoscommunity', 'Tutti i canali') ?></span>
                                                        <span class="icon mdi mdi-arrow-right-circle-outline"></span>
                                                    </a>
                                                    <div class="more-info">
                                                        <span><?= AmosCommunity::t('amoscommunity', 'Sottocanali del{tipo} "{community}"', ['community' => $model->name,'tipo' => $model->parent_id ? ' canale' : 'la community']) ?></span>
                                                    </div>
                                                </div>
                                                <div class="dropdown-header-right">
                                                    <?php if ($model->isCommunityManager() && ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED)) { ?>
                                                        <a class="btn btn-xs btn-primary-outline"
                                                           href="<?= '/community/community/create?parentId=' . $model->id; ?>"
                                                           title="<?= AmosCommunity::t('amoscommunity', 'Nuovo') ?>">
                                                            <span class="am am-plus-circle-o"></span><span
                                                                class=""><?= AmosCommunity::t('amoscommunity', 'Nuovo') ?></span>
                                                        </a>
                                                    <?php }
                                                    ?>
                                                </div>

                                            </div>
                                            <ul class="list-channel-dropdown">
                                                <?php if ($model->getSubcommunities()->count() > 0) { ?>
                                                    <?php echo $this->render('_select_item_community_channels', ['model' => $model, 'communityModule' => $communityModule, 'is_channel' => true]); ?>
                                                <?php } else { ?>
                                                    <li class="no-item-list-channel lead m-b-0">
                                                        <?= AmosCommunity::t('amoscommunity', 'Nessun canale presente') ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if ($model->parent_id) {
                                    ?>

                                    <div class="action-subscribe">
                                        <?php
                                        if (!($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED)) {
                                            echo $this->render('_user_subscribe_unsubscribe_buttons', ['community' => $model, 'user' => User::findOne(\Yii::$app->user->id), 'from_menu' => true]);
                                        } else {
                                            echo \open20\amos\community\widgets\ManageUserCommunityInfo::widget(['community' => $model, 'user' =>  User::findOne(\Yii::$app->user->id)]);
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php if ($model->parent_id) { ?>
        <div id="modal-channel-info" class="modal modal-channel" tabindex="-1" role="dialog" aria-labelledby="modal1Title"
             aria-describedby="modal1Description">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span class="mdi mdi-close"></span>
                        </button>
                        <div class="modal-title h6"><?= AmosCommunity::t('amoscommunity', 'Descrizione canale') ?></div>

                    </div>
                    <div class="modal-body">
                        <div class="h3"><?= $model->name ?></div>
                        <?php echo $this->render('_modal_item_community', ['model' => $model]); ?>
                    </div>

                </div>
            </div>
        </div>
        <?php
    }
?>
