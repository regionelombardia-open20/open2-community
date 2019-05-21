<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\widgets\icons\WidgetIconAccademyDocument;

/**
 * @var $this \yii\web\View
 * @var $model \lispa\amos\community\models\Community
 */
// title not active in layout view_network
//$this->title = AmosCommunity::t('amoscommunity', 'Welcome to the community!');
//if (!is_null($model->parent_id)) {
//    $this->title = AmosCommunity::t('amoscommunity', '#welcome_to_subcommunity');
//}
?>

<div class="actions-dashboard-container community-dashboard-container">

    <ul id="widgets-icon" class="bk-sortableIcon plugin-list p-t-25" role="menu">

        <?php if ($model->hide_participants == 0 && $model->showParticipantWidget()) { ?>
            <li class="item-widget col-custom" data-code="lispa\amos\admin\widgets\icons\WidgetIconUserProfile">
                <a href="/community/community/participants?communityId=<?= $model->id ?>"
                   title=<?= AmosCommunity::t('amoscommunity', "#platform_user_list") ?> role="menuitem"
                   class="sortableOpt1">
                    <span class="badge"></span>
                    <span class="color-primary bk-backgroundIcon color-darkGrey">
                        <span class="dash dash-users"> </span>
                        <span class="icon-dashboard-name pluginName">
                            <?= AmosCommunity::t('amoscommunity', 'Participants') ?>
                        </span>
                    </span>
                </a>
            </li>
        <?php }
        if (\Yii::$app->getModule('community')->showSubcommunitiesWidget === true && $model->showSubCommunityWidget()) {
            $widgetSubcommunities = Yii::createObject($model->getPluginWidgetClassname());
            echo $widgetSubcommunities::widget();
        }
        if ($model->context == 'lispa\amos\projectmanagement\models\Projects') {
            /** @var \lispa\amos\core\record\Record $contentObject */
            $contentObject = Yii::createObject(lispa\amos\projectmanagement\models\Projects::className());
            $widgetClassname = $contentObject->getPluginWidgetClassname();
            $widget = Yii::createObject($widgetClassname);
            echo $widget::widget();
        }?>




        <?php
            echo \lispa\amos\dashboard\widgets\SubDashboardWidget::widget([
                'model' => $model,
                'widgets_type' => 'ICON',
            ]);
        ?>
    </ul>
    <div class="clearfix"></div>


    <?php
        echo \lispa\amos\dashboard\widgets\SubDashboardWidget::widget([
            'model' => $model,
            'widgets_type' => 'GRAPHIC',
        ]);
    ?>

</div>
