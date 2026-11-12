<?php

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\helpers\Html;
use open20\amos\community\AmosCommunity;
use open20\amos\community\utilities\CommunityUtil;
use yii\helpers\Url;


/**
 * @var $fixedCommunityType
 * @var $model \open20\amos\community\models\Community
 */
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
$isInDashboardCommunity =  \Yii::$app->controller->action->id == 'open-join' && \Yii::$app->controller->id == 'join';
?>
<?php if ($model->isSubChannel()) { ?>
    <div class="channel-father">
        <?php
        $parentCommunity = $model->parent;
        $urlParent = '/img/img_default.jpg';
        if (!is_null($parentCommunity->communityLogo)) {
            $urlParent = $parentCommunity->communityLogo->getWebUrl('item_community', false, true);
        }
        echo Html::a(Html::img($urlParent, [
            'alt' => 'Community Logo',
            'class' => 'community-logo'
        ]), [
            '/community/join/index', 'id' => $model->parent_id
        ], [
            'title' => AmosCommunity::t('amoscommunity', 'Vai al Padre'),
            'data-toggle' => 'tooltip'
        ]);

        echo Html::a('<span class= "mdi mdi-arrow-left"></span>', [
            '/community/join/index', 'id' => $model->parent_id
        ], [
            'title' => AmosCommunity::t('amoscommunity', 'Vai al Padre'),
            'class' => "icon-father",
            'data-toggle' => "tooltip"
        ]);
        ?>
    </div>
<?php }
?>
<?php if (!empty($model->parent_id)) { ?>
    <div class="current-channel chevron-border">

        <div class="content-channel-menu">

            <div class="nav-item-plugin">
                <div class="img-channel-current">
                    <?php
                    $url = '/img/img_default.jpg';
                    if (!is_null($model->communityLogo)) {
                        $url = $model->communityLogo->getWebUrl('item_community', false, true);
                    }
                    echo Html::img(
                        $url,
                        [
                            'class' => 'img-channel-current',
                            'alt' => $model->getAttributeLabel('communityLogo')
                        ]
                    );
                    ?>
                    <?php if (!$fixedCommunityType) { ?>
                        <?php $array_info = CommunityUtil::getCommunityTypeConfigs($model); ?>
                        <span class="icon-community-status bg-community-<?= $array_info['classType'] ?>" data-toggle="tooltip" title="<?= $array_info['textCommunityType'] ?>">
                            <span class="icon icon-white icon-sm <?= $array_info['iconCommunityType'] ?>"></span>
                        </span>
                    <?php } ?>
                </div>

                <?php if ($isInDashboardCommunity) { ?>
                    <span class="label-menu"><?= $model->name ?></span>

                <?php } else { ?>
                    <?= Html::a($model->name, [
                        '/community/join/index', 'id' => $model->id
                    ], [
                        'title' => $model->name,
                        'class' => 'label-menu'
                    ]); ?>
                <?php } ?>

            </div>
        </div>

        <div class="actions-scope d-flex align-items-center m-l-5">
            <a class="text-link" data-toggle="modal" data-target="#modal-channel-info" href="#" title="<?= AmosCommunity::t('amoscommunity', 'Informazioni canale') ?>">
                <span class="mdi mdi-information-outline mdi-18px"></span>
            </a>

            <?php
            echo ContextMenuWidget::widget([
                'title' => AmosCommunity::t('amoscommunity', 'Menu contestuale di "{community}"', ['community' => $model->name]),
                'model' => $model,
                'actionModify' => "/community/community/update?id=" . $model->id,
                'actionDelete' => "/community/community/delete?id=" . $model->id,
               // 'layout' => '@vendor/open20/amos-layout/src/views/widgets/context_menu_widget_network_scope.php'
            ])
            ?>

        </div>


    </div>
<?php } else { ?>
    <div class="current-community">
        <?php if ($isInDashboardCommunity) { ?>
            <div class="nav-item-plugin active">
                <span class="material-icons-two-tone icon-primary">
                    dashboard
                </span>
                <span class="label-menu"><?= AmosCommunity::t('amoscommunity', 'Generale') ?> </span>
            </div>

        <?php } else { ?>
            <?php
            $urlDashboard = Url::to(['/community/join/index', 'id' => $model->id]);
            ?>
            <a href="<?= $urlDashboard ?>" title="<?=AmosCommunity::t('amoscommunity', 'Generale')?>" class="nav-item-plugin">
                <span class="material-icons-two-tone icon-primary">
                    dashboard
                </span>
                <span class="label-menu"><?= AmosCommunity::t('amoscommunity', 'Generale') ?> </span>
            </a>

        <?php } ?>
    </div>
<?php } ?>