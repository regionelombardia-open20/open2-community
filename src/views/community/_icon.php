<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\views\community
 * @category   CategoryName
 */

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\models\CommunityType;
use open20\amos\notificationmanager\forms\NewsWidget;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\user\User;
use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\community\models\Community;
use open20\design\assets\ShimmerDesignAsset;

ShimmerDesignAsset::register($this);


/**
 * @var \open20\amos\community\models\Community $model
 */
$communityModule = Yii::$app->getModule('community');
$fixedCommunityType = !is_null($communityModule->communityType);
$hideCommunityType = $communityModule->hideCommunityTypeInCommunityIconView;
$bypassWorkflow = $communityModule->forceWorkflow($model);
$isGuest = \Yii::$app->user->isGuest;
$isOpenCommunity = false;
$isClosedCommunity = false;
$isPrivateCommunity = false;
$isWaitingToSigned = false;
$isChannel = !empty($model->parent_id);
$isSubchannel = false;
$communityRoot = null;
$classCommunityType = $model->getColorClassCommunityType();
$isEnabledMsTeams = $communityModule && $communityModule->enableMicrosoftTeams && $model->enable_msgraph_teams;

$message = AmosCommunity::t('amoscommunity', 'Visita la community');
if ($isChannel) {
    $communityRoot = Community::getCommunityRoot($model);
    $message = CommunityUtil::SubcommToChannelTrad('Visita la community');
    $parent = Community::findOne($model->parent_id);
    if($parent->parent_id){
        $isSubchannel = true;
        $message = CommunityUtil::SubcommToChannelTrad('Visita il sottocanale');
    }
}


$urlRedirect = null;
if (!$isGuest) {
    $loggedUserId = Yii::$app->getUser()->getId();
    $userCommunity = CommunityUserMm::findOne(['community_id' => $model->id, 'user_id' => $loggedUserId]);
    $userProfile = User::findOne($loggedUserId)->getProfile();
    if (!empty($communityModule) && $communityModule->enableAutoLinkLanding == true && !empty($model->redirect_url)) {
        $urlRedirect = $model->redirect_url;
    }

    $viewUrl = '/community/join/open-join?id=' . $model->id;
    if (!empty($loggedUserId)) {
        $userCommunity = CommunityUtil::getMemberCommunityLogged($model->id);
        $userProfile = User::findOne($loggedUserId)->getProfile();
    }

    if (!empty($userProfile) && $userProfile->validato_almeno_una_volta && !is_null($userCommunity)) {
        if (in_array($userCommunity->status, [CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER, CommunityUserMm::STATUS_WAITING_OK_USER])) {
            $isWaitingToSigned = true;
        } else {
            $isSigned = true;
        }
    } else {
        $isSigned = false;
    }

    if ($model->community_type_id == CommunityType::COMMUNITY_TYPE_OPEN) {
        $isOpenCommunity = true;
    } else if ($model->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED) {
        $isClosedCommunity = true;
    } else if ($model->community_type_id == CommunityType::COMMUNITY_TYPE_PRIVATE) {
        $isPrivateCommunity = true;
    }

    if (!$isOpenCommunity) {
        if ($isWaitingToSigned) {
            $viewUrl = '/community/community/view?id=' . $model->id;
        }
    }
} else {
    $viewUrl = '/community/community/view?id=' . $model->id;
}
?>

<div class="community-container p-3 d-flex flex-column justify-content-center align-items-center mb-5 category<?= $model->communityType ?> <?= $classCommunityType ?> <?= ($isChannel ? 'community-channel' : '') ?>">
    <?php $buttonTransform = \yii\helpers\Html::a(AmosCommunity::t('amoscommunity', "Trasforma in community"), ['/community/community/transform-to-community-parent', 'id' => $model->id], [
        'title' => AmosCommunity::t('amoscommunity', "Trasforma in community"),
        'data-confirm' => AmosCommunity::t('amoscommunity', 'Sei sicuro di trasformare la sottocommunity in community?')
    ]) ?>
    <?php $buttonMove = \yii\helpers\Html::a(AmosCommunity::t('amoscommunity', "Sposta"), ['/community/community/move', 'id' => $model->id], [
        'title' => AmosCommunity::t('amoscommunity', "Sposta sotto un'altra community")
    ]) ?>
    <?php $additionalButtons = [];
    if (!$isGuest) {
        if (Yii::$app->getUser()->can('AMMINISTRATORE_COMMUNITY')) {
            $additionalButtons[] = $buttonMove;
            if (!empty($model->parent_id)) {
                $additionalButtons[] = $buttonTransform;
            }
        }
    }

    $newsWidget = NewsWidget::widget([
        'model' => $model,
        'css_class' => 'badge badge-pill'
    ])
    ?>
    <div class="image-container position-relative">

        <div class="community-image ">
            <?php
            $url = '/img/img_default.jpg';
            if (!is_null($model->communityLogo)) {
                $url = $model->communityLogo->getWebUrl('item_community', false, true);
            }
            $logo = Html::img(
                $url,
                [
                    'class' => 'img-responsive shimmer-image',
                    'alt' => $model->getAttributeLabel('communityLogo'). ' '.$model->title
                ]
            );

            if (!$isGuest) {
                if (!empty($urlRedirect)) {
                    echo Html::a($logo, $urlRedirect), ['title' => $model->name, 'class' => 'img-shimmer', 'target' => '_blank'];
                } else {
                    echo Html::a($logo, $viewUrl, ['title' => $model->name, 'class' => 'img-shimmer']);
                }
            } else {
                echo $logo;
            }
            ?>

            <?php if ($isChannel && $communityRoot) {
                $urlCommunityRoot = '/img/img_default.jpg';
                if (!is_null($communityRoot->communityLogo)) {
                    $urlCommunityRoot = $communityRoot->communityLogo->getWebUrl('item_community', false, true);
                }
                $logo = Html::img(
                    $urlCommunityRoot,
                    [
                        'class' => 'img-responsive shimmer-image',
                        'alt' => $communityRoot->getAttributeLabel('communityLogo')
                    ]
                );
            } ?>

        </div>
        <?= $newsWidget ?>
        <?=
        ContextMenuWidget::widget([
            'model' => $model,
            'actionModify' => "/community/community/update?id=" . $model->id,
            'actionDelete' => "/community/community/delete?id=" . $model->id,
            'mainDivClasses' => '',
            'optionsDelete' => ['class' => 'delete-community-btn'],
            'labelDeleteConfirm' => AmosCommunity::t('amoscommunity', 'delete_confirm'),
            'additionalButtons' => $additionalButtons
        ]);
        ?>
        <?php if ($isEnabledMsTeams) { ?>
            <a class="icon-ms-teams" href="javascript:void(0)" data-toggle="tooltip" title="Community collegata a Microsoft Teams">
                <span class="mdi mdi-microsoft-teams"></span>
            </a>
        <?php } ?>
        <?php if ($isChannel) { ?>
            <a class="icon-channel" href="javascript:void(0)" data-toggle="tooltip" title="<?=CommunityUtil::SubcommToChannelTrad('Canale della community').": ".$communityRoot->name?>" >
                <span class="material-icons-two-tone icon-primary">
                    group_work
                </span>
            </a>
        <?php } ?>

        <?php if (!$fixedCommunityType) : ?>
            <?php
            switch ($model->community_type_id):
                case CommunityType::COMMUNITY_TYPE_CLOSED:
                    $textCommunityType = AmosCommunity::t('amoscommunity', 'Community ristretta - è visibile solamente agli iscritti');
                    if ($isChannel) {
                        $textCommunityType = CommunityUtil::SubcommToChannelTrad('Community ristretta ai partecipanti');
                    }
                    
                    $iconCommunityType = 'mdi mdi-eye-off';
                    break;
                case CommunityType::COMMUNITY_TYPE_OPEN:
                    $textCommunityType = AmosCommunity::t('amoscommunity', 'Community aperta - è possibile iscriversi liberamente');
                    if ($isChannel) {
                        $textCommunityType = CommunityUtil::SubcommToChannelTrad('Community aperta');
                    }

                    $iconCommunityType = 'mdi mdi-lock-open-outline';
                    break;
                case CommunityType::COMMUNITY_TYPE_PRIVATE:
                    $textCommunityType = AmosCommunity::t('amoscommunity', 'Community riservata ai partecipanti - è possibile fare richiesta di iscrizione');
                    if ($isChannel) {
                        $textCommunityType = CommunityUtil::SubcommToChannelTrad('Community riservata ai partecipanti');
                    }
                    $iconCommunityType = 'mdi mdi-lock';
                    break;
                default:
                    $iconCommunityType = 'am am-accounts-alt';
            endswitch;
            
            if($isSubchannel){
                $textCommunityType = AmosCommunity::t('amoscommunity', 'Sotto').strtolower($textCommunityType);
            }
            ?>
            <?php if (!empty($urlRedirect)) { ?>
                <a href="<?= $urlRedirect ?>" class="position-absolute category<?= $model->communityType ?> <?= $classCommunityType ?>" data-toggle="tooltip" title="<?= $textCommunityType ?>" target="_blank">
                    <span class="icon icon-white  <?= $iconCommunityType ?>"></span>
                </a>
            <?php } else { ?>
                <a href="<?= $viewUrl ?>" class="position-absolute category<?= $model->communityType ?> <?= $classCommunityType ?>" data-toggle="tooltip" title="<?= $textCommunityType ?>">
                    <span class="icon icon-white  <?= $iconCommunityType ?>"></span>
                </a>
            <?php } ?>
        <?php endif; ?>

    </div>
    <div class="community-title-container py-3">
        <h3 class="community-title title-two-line h6 font-weight-bold m-t-0 m-b-0 text-center overflow-hidden">
            <?php if (!$isGuest) : ?>
                <?php if ($urlRedirect) : ?>
                    <?=
                    Html::a(
                        $model->name,
                        $urlRedirect,
                        [
                            'title' => AmosCommunity::t('amoscommunity', '#icon_title_link') . ' ' . $model->name, 'class' => 'title-two-line link-list-title',
                            'target' => '_blank'
                        ]
                    );
                    ?>
                <?php else : ?>
                    <?=
                    Html::a(
                        $model->name,
                        $viewUrl,
                        [
                            'title' => AmosCommunity::t('amoscommunity', '#icon_title_link') . ' ' . $model->name, 'class' => 'title-two-line link-list-title',
                        ]
                    );
                    ?>
                <?php endif; ?>
            <?php else : ?>
                <?= $model->name ?>
            <?php endif; ?>
        </h3>
    </div>
    <?php
    if (!$hideCommunityType) :
    ?>
        <?php if (!$fixedCommunityType) : ?>
            <?php if (!$isGuest) : ?>
                <?php if ($isSigned) : ?>
                    <small class="d-flex align-items-end text-muted mt-4">
                        <?= AmosCommunity::t('amoscommunity', 'Iscritto il') . ' ' ?><?= \Yii::$app->formatter->asDate($userCommunity->created_at, 'php:d M Y') ?>
                    </small>
                    <div class="button-container d-flex justify-content-center border-top">
                        <a href="<?= $viewUrl ?>" class="btn btn-xs btn-link" title=" <?= $message . ' ' . $model->name ?>"><?= $message ?></a>
                    </div>
                <?php else : ?>
                    <?php if ($isOpenCommunity) : ?>
                        <div class="button-container d-flex justify-content-center border-top">
                            <a href="<?= $viewUrl ?>" class="btn btn-xs btn-primary" title=" <?= $message . ' ' . $model->name ?>"><?= $message ?></a>
                        </div>
                    <?php elseif ($isPrivateCommunity) : ?>
                        <?php if ($isWaitingToSigned) : ?>
                            <small class="text-muted text-center mt-4">
                                <?= AmosCommunity::t('amoscommunity', 'Richiesta di iscrizione inviata') ?>
                                <a href="javascript::void(0)" class="bi-form-field-tooltip-info m-l-5" data-toggle="tooltip" data-html="true" data-original-title="<?= AmosCommunity::t('amoscommunity', 'Sei in attesa che un community manager convalidi la richiesta per poter accedere alla community') ?>">
                                    <span class="am am-info-outline"></span>
                                    <span class="sr-only"><?= AmosCommunity::t('amoscommunity', 'Sei in attesa che un community manager convalidi la richiesta per poter accedere alla community') ?></span>
                                </a>
                            </small>
                        <?php endif ?>
                        <div class="button-container d-flex justify-content-center border-top">
                            <a href="<?= $viewUrl ?>" class="btn btn-xs btn-primary" title=" <?= $message . ' ' . $model->name ?>"><?= $message ?></a>
                        </div>
                    <?php else : ?>
                        <?php if ($isWaitingToSigned) : ?>
                            <div class="button-container d-flex justify-content-center border-top">
                                <a href="<?= $viewUrl ?>" class="btn btn-xs btn-warning" title="Clicca per accettare o rifiutare l'invito alla community <?= $model->name ?>"><?= AmosCommunity::t('amoscommunity', 'Rispondi all\'invito') ?></a>
                            </div>
                        <?php else : ?>
                            <div class="button-container d-flex justify-content-center border-top">
                                <a href="<?= $viewUrl ?>" class="btn btn-xs btn-primary" title=" <?= $message . ' ' . $model->name ?>"><?= $message ?></a>
                            </div>
                        <?php endif ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php
                $socialAuthModule = Yii::$app->getModule('socialauth');
                $labelSigninOrSignup = AmosCommunity::t('amoscommunity', 'Accedi o registrati');
                if ($socialAuthModule && ($socialAuthModule->enableRegister == false)) {
                    $labelSigninOrSignup = AmosCommunity::t('amoscommunity', 'Accedi');
                }
                $tooltipPreventCtaView = AmosCommunity::t(
                    'amoscommunity',
                    '{labelSigninOrSignup} alla piattaforma {platformName} per visitare la community',
                    ['labelSigninOrSignup' => $labelSigninOrSignup, 'platformName' => \Yii::$app->name]
                );
                ?>
                <div class="button-container w-100 d-flex justify-content-center border-top">
                    <a href="javascript::void(0)" class="btn btn-primary btn-xs mt-2 disabled disabled-with-pointer-events" data-toggle="tooltip" data-html="true" data-original-title="<?= $tooltipPreventCtaView ?>">
                        <?= $message ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>