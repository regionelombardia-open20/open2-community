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

        <?php if ($model->hide_participants == 0) { ?>
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
        <?php } ?>

        <?php
        if ((!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == false) && \Yii::$app->getModule('community')->showSubcommunitiesWidget
            === true && ($model->id != 2751 && $model->id != 2754 && $model->id != 2750)) {
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

        if (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi'] == true && in_array($model->id,
                [2750, 2761, 2769])) {

        } else {
            echo \lispa\amos\dashboard\widgets\SubDashboardWidget::widget([
                'model' => $model,
                'widgets_type' => 'ICON',
            ]);
        }
        ?>
    </ul>
    <div class="clearfix"></div>

    <div class="m-t-30"></div>
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
    else {
        echo \lispa\amos\dashboard\widgets\SubDashboardWidget::widget([
            'model' => $model,
            'widgets_type' => 'GRAPHIC',
        ]);
    }
    ?>

</div>
