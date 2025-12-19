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
use open20\amos\community\assets\AmosCommunityAsset;
use open20\amos\core\forms\ListTagsWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\StringUtils;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\community\widgets\CommunityHeaderWidget;

use yii\log\Logger;

/**
 * @var yii\web\View $this
 * @var \open20\amos\community\models\Community $model
 * @var string $tabActive
 */
$this->title = strip_tags($model);
$this->params['breadcrumbs'][] = ['label' => AmosCommunity::t('amoscommunity', 'Community'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$assetBundle = AmosCommunityAsset::register($this);
$idTabSheet = 'tab-registry';
$idTabContents = 'tab-contents';
$idTabParticipants = 'tab-participants';
$idTabTags = 'tab-tags';
$idTabProjects = 'tab-projects';
$idTabSubcommunities = 'tab-subcommunities';

$modelsEnabled = Yii::$app->getModule('cwh')->modelsEnabled;
/** @var AmosCommunity $moduleCommunity */
$moduleCommunity = Yii::$app->getModule('community');
$viewTabContents = $moduleCommunity->viewTabContents;

$customHideContentsModels = Yii::$app->getModule('hideContentsModels');
if (!empty($customHideContentsModels)) {
    $hideContentsModels = array_merge($moduleCommunity->hideContentsModels, $customHideContentsModels);
} else {
    $hideContentsModels = $moduleCommunity->hideContentsModels;
}

//check if a specific tab must be the active one
$tabSheetActive = isset($tabActive) ? ((strcmp($tabActive, $idTabSheet) == 0) ? true : false) : false;
$tabContentsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabContents) == 0) ? true : false) : false;
$tabParticipantsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabParticipants) == 0) ? true : false) : false;
$tabTagsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabTags) == 0) ? true : false) : false;
$tabProjectsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabProjects) == 0) ? true : false) : false;
$tabSubcommunitiesActive = isset($tabActive) ? ((strcmp($tabActive, $idTabSubcommunities) == 0) ? true : false) : false;

$url = $model->getAvatarUrl('original');

$isLoggedUserParticipant = $model->isNetworkUser();
$classCommunityType = $model->getColorClassCommunityType();

$isChannel = !empty($model->parent_id);

$img_class = '';
if ($model->parent_id) {
    $img_class = 'img-channel';
}

?>

<?php /*if ($model->status != Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED) { */ ?>
<!--
    <?/*= WorkflowTransitionStateDescriptorWidget::widget([
        'model' => $model,
        'workflowId' => Community::COMMUNITY_WORKFLOW,
        'classDivMessage' => 'message',
        'viewWidgetOnNewRecord' => true
    ]); */ ?>
--><?php /*} */ ?>


<div class="community-preview bg-<?= $classCommunityType ?>">
    <?php if (!$isChannel) { ?>
        <div class="bg-image-community" style="background:url(<?= $assetBundle->baseUrl ?>/images/bg-preview-community.png)"></div>
    <?php } ?>
    <div class="community-content-top">
        <div class="container">
            <?php if (!$isChannel) { ?>
            <a href="/site/to-menu-url?url=/it/community/community/index"
                       class="link-all flexbox align-items-center" title="Visualizza la lista delle community">
                        <span class="am am-arrow-left"></span>
                        <span><?= AmosCommunity::t('amoscommunity', 'Tutte le community') ?></span>
                    </a>
             <?php } ?>
            <div class="row">
                <div class="col-md-8">
                    <h1><?= $model->name ?></h1>
                    <div class="chip chip-community chip-<?= $classCommunityType ?>"><span class="chip-icon mdi mdi-lock-open-outline"></span><span class="chip-label"><?= CommunityUtil::getCommunityTypeConfigs($model)['textCommunityType']; ?></span></div>
                    <div class="community-abstract">
                        <?php
                        $desclen = 230;
                        ?>
                        <?php if (strlen($model->description) <= $desclen) : ?>
                            <?= $model->description ?>
                        <?php else : ?>
                            <div>
                                <?php
                                $moreContentTextLink  = AmosCommunity::t('amoslayout', 'espandi descrizione') . ' ' . AmosIcons::show("chevron-down");
                                $moreContentTitleLink = AmosCommunity::t('amoslayout', 'Leggi la descrizione completa');

                                $lessContentTextLink  = AmosCommunity::t('amoslayout', 'riduci descrizione') . ' ' . AmosIcons::show("chevron-up");
                                $lessContentTitleLink = AmosCommunity::t('amoslayout', 'Riduci testo');
                                ?>
                                <div>
                                    <?=
                                    strip_tags(StringUtils::truncateHTML($model->description, $desclen))
                                    ?>
                                    <a class="javascript:void(0)" href="#" data-toggle="modal" data-target="#modal-description-id" title="<?= $moreContentTitleLink ?>">Leggi tutto</a>
                                </div>


                                <div id="modal-description-id" class="modal fade modal-community-description" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <div class="modal-title h6">Descrizione completa</div>
                                            </div>
                                            <div class="modal-body">
                                                <?= $model->description ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="community-image">
                        <?php
                        $url        = '/img/img_default.jpg';
                        if (!is_null($model->communityLogo)) {
                            $url = $model->getAvatarUrl('medium', false, true);
                        }
                        $logo = Html::img(
                            $url,

                            [
                                'class' => 'img-responsive shimmer-image ' . $img_class,
                                'alt' => $model->getAttributeLabel('communityLogo'). ' '. $model->title,
                            ]
                        );
                        echo $logo;
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="community-content-bottom">
        <div class="container">
            <div class="box-tag-interesse">
                <strong class="text-label text-uppercase">Aree di interesse</strong>
                <?php if (!empty(\Yii::$app->getModule('tag'))) { ?>
                    <?= ListTagsWidget::widget([
                        'userProfile' => $model->id,
                        'className' => $model->className(),
                        'viewFilesCounter' => true,
                        'withTitle' => false,
                        'pageSize' => 4,
                        'idModal' => 'view-all-tags-preview',
                        'layout' => '@vendor/open20/amos-core/forms/views/widgets/widget_list_tags.php',
                    ]); ?>
                <?php } ?>
            </div>
        </div>

    </div>
    <div class="community-content-bottom">
        <div class="container">
            <div class="row d-flex flex-wrap align-items-center">
                <div class="col-md-5">
                    <div class="community-preview-info">

                        <div class="info-plugin">
                            <span class="material-icons-two-tone mdi-36px icon-primary">
                                people
                            </span> <span class="label-counter">
                                <strong><?= AmosCommunity::t('amoscommunity', 'Participants') ?>:</strong>
                            </span>
                            <span class="content-counter">
                                <?= $model->getCommunityUsers()->count() ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="text-right">
                        <?php if (!($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED)) {
                            echo \open20\amos\community\widgets\ManageUserCommunityInfo::widget(['community' => $model, 'user' => \Yii::$app->user->id, 'button' => true]);
                        } else {
                            echo \open20\amos\community\widgets\ManageUserCommunityInfo::widget(['community' => $model, 'user' => \Yii::$app->user->id]);
                        }
                        ?>

                    </div>
                </div>
            </div>

        </div>
    </div>



</div>




<div class="container">
    <?php if (Yii::$app->user->can('ADMIN') && Yii::$app->getModule('community') && Yii::$app->getModule('community')->enableUserJoinedReportDownload) { ?>
        <div class="community-description">
            <div class="container-custom">
                <div class="community-download-report col-xs-12 nop text-right">
                    <?= Html::a(AmosIcons::show('download') . ' ' . AmosCommunity::t('amoscommunity', '#download_user_joined_report'), ['user-joined-report-download', 'communityId' => $model->id], ['class' => 'btn btn-primary']); ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>