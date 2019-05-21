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
\lispa\amos\dashboard\assets\DashboardFullsizeAsset::register($this);
use \lispa\amos\core\icons\AmosIcons;

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
    <nav>
        <div class="container-custom">
            <div class="wrap-plugins row">
                <div id="widgets-icon" class="widgets-icon col-xs-12" role="menu">
                    <?php if ($model->hide_participants == 0) { ?>
                        <div class="square-box" data-code="lispa\amos\admin\widgets\icons\WidgetIconUserProfile">
                            <div class="square-content item-widget plugin-partecipants">
                                <a class="dashboard-menu-item" href="/community/community/participants?communityId=<?= $model->id ?>"
                                   title=<?= AmosCommunity::t('amoscommunity', "#platform_user_list") ?> role="menuitem"
                                   class="sortableOpt1">
                                    <span class="badge"></span>
                                    <span class="">
                                        <?=AmosIcons::show('user', [], AmosIcons::IC)?>
                                        <span class="icon-dashboard-name pluginName">
                                            <?= AmosCommunity::tHtml('amoscommunity', 'Participants') ?>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    if ((!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == false) && \Yii::$app->getModule('community')->showSubcommunitiesWidget
                        === true && (!in_array($model->id, [2751, 2754, 2750, 2772]))) {
                        $widgetSubcommunities = Yii::createObject($model->getPluginWidgetClassname());
                        echo $widgetSubcommunities::widget();
                    }
                    if (empty(\Yii::$app->params['isPoi']) && \Yii::$app->getModule('community')->showSubcommunitiesWidget === true) {
                        $widgetSubcommunities = Yii::createObject($model->getPluginWidgetClassname());
                        echo $widgetSubcommunities::widget();
                    }
                    if ($model->context == 'lispa\amos\projectmanagement\models\Projects') {
                        /** @var \lispa\amos\core\record\Record $contentObject */
                        $contentObject = Yii::createObject(lispa\amos\projectmanagement\models\Projects::className());
                        $widgetClassname = $contentObject->getPluginWidgetClassname();
                        $widget = Yii::createObject($widgetClassname);
                        echo $widget::widget();
                    }

                    // Community accademy POI
                    if (isset(Yii::$app->params['isPoi']) && (Yii::$app->params['isPoi'] === true) && ($model->id == 2761)) {
                        if (!empty(Yii::$app->getModule('chat')) && (Yii::$app->getModule('chat')->assistanceUserId != Yii::$app->user->id)) {
                            /** @var \lispa\amos\core\record\Record $contentObject */
                            $widgetClassname = \lispa\amos\chat\widgets\icons\WidgetIconChatAssistance::className();
                            $widget = Yii::createObject($widgetClassname);
                            echo $widget::widget();
                        }
                        /** @var \lispa\amos\core\record\Record $contentObject */
                        $widgetClassname = WidgetIconAccademyDocument::className();
                        $widget = Yii::createObject($widgetClassname);
                        echo $widget::widget();
                    }

                    if (isset(Yii::$app->params['isPoi']) && (Yii::$app->params['isPoi'] === true) && ($model->id == 2795|| $model->id == 2794 || $model->id == 2793 )) {
                        /** @var \lispa\amos\core\record\Record $contentObject */
                        $utility = new \lispa\amos\community\utilities\CommunityUtil();
                        $community = \lispa\amos\community\models\Community::findOne(\Yii::$app->request->get('id'));
                        $userMm = \lispa\amos\community\models\CommunityUserMm::findOne([
                            'community_id' => \Yii::$app->request->get('id'),
                            'user_id' => \Yii::$app->getUser()->getId(),
                            'role' => \lispa\amos\community\models\Community::ROLE_COMMUNITY_MANAGER
                        ]);

                        if(!empty($userMm)){
                            $widgetClassname1 = lispa\amos\sondaggi\widgets\icons\WidgetIconSondaggi::className();
                            $widget1 = Yii::createObject($widgetClassname1);
                        }else {
                            $widgetClassname1 = lispa\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi::className();
                            $widget1 = Yii::createObject($widgetClassname1);
                        }

                        $widgetClassname4 = \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard::className();
                        $widget4 = Yii::createObject($widgetClassname4);
                        $widgetClassname5 = lispa\amos\news\widgets\icons\WidgetIconNewsDashboard::className();
                        $widget5 = Yii::createObject($widgetClassname5);
                        $widgetClassname6 = \lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard::className();
                        $widget6 = Yii::createObject($widgetClassname6);
                        echo $widget1::widget();
                        echo $widget4::widget();
                        echo $widget5::widget();
                        echo $widget6::widget();
                    }

                    if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id,
                            [2750, 2761, 2769, 2772, 2795, 2794, 2793])) {

                    } else {
                        echo \lispa\amos\dashboard\widgets\SubDashboardWidget::widget([
                            'model' => $model,
                            'widgets_type' => 'ICON',
                        ]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="community-description">
        <div class="container-custom">
            <?=$model->description?>
        </div>
    </div>

    <?php
    if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id, [2761])) {
        \lispa\amos\dashboard\assets\SubDashboardAsset::register(\Yii::$app->getView());
        $widgetClassname1 = \lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews::className();
        $widget1 = Yii::createObject($widgetClassname1);
        echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">' .
            '<div class="graphics-dashboard-container">' .
            '<div id="widgets-graphic" class="widgets-graphic-sortable">' .
            '<div class="grid">' .
            '<div class="grid-sizer"></div>';
        echo '<div data-code="' . $widget1::classname() . '" data-module-name>' . $widget1::widget() . '</div>';
        echo '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    } else if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id, [2750])) {
        \lispa\amos\dashboard\assets\SubDashboardAsset::register(\Yii::$app->getView());

        $allWidget = '';

        echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">' .
            '<div class="graphics-dashboard-container">' .
            '<div id="widgets-graphic" class="widgets-graphic-sortable">' .
            '<div class="grid">' .
            '<div class="grid-sizer"></div>';

        $widgetClassname1 = \openinnovation\landing\widgets\graphics\WidgetGraphicDownloadTicket::className();
        $widget1 = Yii::createObject($widgetClassname1);
        $widgetClassname2 = \lispa\amos\chat\widgets\graphics\WidgetGraphicChatAssistance::className();
        $widget2 = Yii::createObject($widgetClassname2);
        $widgetClassname3 = \lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews::className();
        $widget3 = Yii::createObject($widgetClassname3);
        $widgetClassname4 = \lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti::className();
        $widget4 = Yii::createObject($widgetClassname4);

        if($widget1->isVisible()){
            $allWidget .= '<div data-code="' . $widget1::classname() . '" data-module-name>' . $widget1::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget3::classname() . '" data-module-name>' . $widget3::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget2::classname() . '" data-module-name>' . $widget2::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget4::classname() . '" data-module-name>' . $widget4::widget() . '</div>';
        } else {
            $allWidget .= '<div data-code="' . $widget2::classname() . '" data-module-name>' . $widget2::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget4::classname() . '" data-module-name>' . $widget4::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget3::classname() . '" data-module-name>' . $widget3::widget() . '</div>';
        }

        echo $allWidget;

        echo '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }
    else if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id, [2769])) {
        \lispa\amos\dashboard\assets\SubDashboardAsset::register(\Yii::$app->getView());
        $widgetClassname1 = \lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews::className();
        $widget1 = Yii::createObject($widgetClassname1);
        $widgetClassname2 = \lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti::className();
        $widget2 = Yii::createObject($widgetClassname2);
        echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">' .
            '<div class="graphics-dashboard-container">' .
            '<div id="widgets-graphic" class="widgets-graphic-sortable">' .
            '<div class="grid">' .
            '<div class="grid-sizer"></div>';
        echo '<div data-code="' . $widget1::classname() . '" data-module-name>' . $widget1::widget() . '</div>';
        echo '<div data-code="' . $widget2::classname() . '" data-module-name>' . $widget2::widget() . '</div>';
        echo '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }
    else if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id, [2772])) {
        \lispa\amos\dashboard\assets\SubDashboardAsset::register(\Yii::$app->getView());
        $widgetClassname1 = \openinnovation\landing\widgets\graphics\WidgetGraphicCountdown::className();
        $widget1 = Yii::createObject($widgetClassname1);
        $widgetClassname2 = \openinnovation\landing\widgets\graphics\WidgetGraphicLiveChat::className();
        $widget2 = Yii::createObject($widgetClassname2);
        echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">' .
            '<div class="graphics-dashboard-container">' .
            '<div id="widgets-graphic" class="widgets-graphic-sortable">' .
            '<div class="grid">' .
            '<div class="grid-sizer"></div>';
        echo '<div data-code="' . $widget1::classname() . '" data-module-name>' . $widget1::widget() . '</div>';
        echo '<div data-code="' . $widget2::classname() . '" data-module-name>' . $widget2::widget(['url' => '/community/join/live-chat']) . '</div>';
        echo '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }
    else if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id, [ 2795, 2794, 2793])) {
        \lispa\amos\dashboard\assets\SubDashboardAsset::register(\Yii::$app->getView());
        $allWidget = '';

        echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">' .
            '<div class="graphics-dashboard-container">' .
            '<div id="widgets-graphic" class="widgets-graphic-sortable">' .
            '<div class="grid">' .
            '<div class="grid-sizer"></div>';


        $widgetClassname2 = \lispa\amos\chat\widgets\graphics\WidgetGraphicChatAssistance::className();
        $widget2 = Yii::createObject($widgetClassname2);
        $widgetClassname3 = \lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews::className();
        $widget3 = Yii::createObject($widgetClassname3);
        $widgetClassname4 = lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni::className();
        $widget4 = Yii::createObject($widgetClassname4);
        $widgetClassname5 = lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti::className();
        $widget5 = Yii::createObject($widgetClassname5);


            $allWidget .= '<div data-code="' . $widget2::classname() . '" data-module-name>' . $widget2::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget3::classname() . '" data-module-name>' . $widget3::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget4::classname() . '" data-module-name>' . $widget4::widget() . '</div>';
            $allWidget .= '<div data-code="' . $widget5::classname() . '" data-module-name>' . $widget5::widget() . '</div>';


        echo $allWidget;

        echo '</div>' .
            '</div>' .
            '</div>' .
            '</div>';
    }
    else {
        echo \lispa\amos\dashboard\widgets\SubDashboardFullsizeWidget::widget([
            'model' => $model,
            'widgets_type' => 'GRAPHIC',
        ]);
    }
    ?>

</div>
