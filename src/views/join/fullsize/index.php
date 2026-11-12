<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * @var $model \open20\amos\community\models\Community
 */
use open20\amos\community\AmosCommunity;
use open20\amos\community\widgets\icons\WidgetIconAccademyDocument;
use open20\amos\core\icons\AmosIcons;
use open20\amos\community\utilities\CommunityUtil;

\open20\amos\dashboard\assets\DashboardFullsizeAsset::register($this);
$this->params['checkedByDefault'] = false;
$this->title = \Yii::t('amoscommunity',"Community")." '".$model->name."'";
/**
 * @var $this \yii\web\View
 * @var $model \open20\amos\community\models\Community
 */
// title not active in layout view_network
//$this->title = AmosCommunity::t('amoscommunity', 'Welcome to the community!');
//if (!is_null($model->parent_id)) {
//    $this->title = AmosCommunity::t('amoscommunity', '#welcome_to_subcommunity');
//}

$module = AmosCommunity::instance();
$disableRender  = $module->enableNewCommunityHeader;
?>

<div class="actions-dashboard-container community-dashboard-container">
    <?php
    if (!$disableRender && (!$module->showIconsPluginOnlyAdmin || ($module->showIconsPluginOnlyAdmin && (Yii::$app->user->can('ADMIN') || CommunityUtil::isManagerUser($model, 'id'))))) {
        ?>
        <nav>
            <div class="container nop">
                <div class="wrap-plugins">
                    <div id="widgets-icon" class="widgets-icon" role="menu">
                        <?php if ($model->hide_participants == 0 && $model->showParticipantWidget()) { ?>
                            <?php
                            $urlDisplayParticipantsMm = '';
                            $showBox = true;
                            $titleParticipants = AmosCommunity::t('amoscommunity', "#platform_user_list");
                            $nameShowParticipants = AmosCommunity::tHtml('amoscommunity', 'Participants');
                            if ($model->context == 'open20\amos\events\models\Event') {
                                $urlDisplayParticipantsMm = "/events/event/participants?communityId={$model->id}";
                                if (method_exists(new \open20\amos\events\utility\EventsUtility(),
                                                'hasPrivilegesLoggedUser')) {
                                    $showBox = false;
                                    $event = \open20\amos\events\models\Event::findOne(['community_id' => $model->id]);
                                    if ($event) {
                                        $showBox = \open20\amos\events\utility\EventsUtility::hasPrivilegesLoggedUser($event);
                                    }
                                } else {
                                    $showBox = true;
                                }
                            } else if ($model->context == 'open20\amos\organizzazioni\models\Profilo') {
                                $titleParticipants = AmosCommunity::t('amoscommunity', "Collaboratori");
                                $nameShowParticipants = AmosCommunity::tHtml('amoscommunity', 'Collaboratori');
                                $paramsOrg = [];
                                $paramsOrg['showAdditionalAssociateButton'] = true;
                                $paramsOrg['viewEmail'] = true;
                                $paramsOrg['checkManagerRole'] = true;
                                $paramsOrg['addPermission'] = true;
                                $paramsOrg['manageAttributesPermission'] = true;
                                $paramsOrg['forceActionColumns'] = false;
                                $paramsOrg['viewM2MWidgetGenericSearch'] = true;
                                $paramsOrg['enableModal'] = true;
                                $paramsOrg['gridId'] = 'organizations-employees-grid';
                                $paramsOrg['organizationManagerRoleName'] = '';
                                $paramsOrg['redirectUrlAfterAssociate'];
                                $paramsOrg['filterForRole'] = '';
                                $paramsOrg['userStatusAssociate'] = true;
                                $paramsOrg['isUpdate'] = true;

                                $profiloModel = \open20\amos\organizzazioni\models\Profilo::find()->andWhere(['community_id' => $model->id])->one();
                                if (!empty($profiloModel)) {
                                    $urlDisplayParticipantsMm = \yii\helpers\Url::to(['/organizzazioni/profilo/organization-employees',
                                                'id' => $profiloModel->id,
                                                'classname' => $model->context,
                                                'params' => $paramsOrg,
                                    ]);
                                } else {
                                    $urlDisplayParticipantsMm = "/community/community/participants?communityId={$model->id}";
                                }
                                if (($showBox) && (!($model->context == 'open20\amos\moodle\models\MoodleCourse'))) :
                                    ?>

                                    <?php
                                    if (
                                            \Yii::$app->getModule('community')->showCommunitiesParticipantPluging == true ||
                                            (\Yii::$app->getModule('community')->showCommunitiesParticipantPluging == false && CommunityUtil::loggedUserIsCommunityManager($model->id))) :
                                        ?>
                                        <div class="square-box"
                                             data-code="open20\amos\admin\widgets\icons\WidgetIconUserProfile">
                                            <div class="square-content item-widget plugin-partecipants">
                                                <a class="dashboard-menu-item" href="<?= $urlDisplayParticipantsMm ?>"
                                                   title=<?= $titleParticipants ?> role="menuitem"
                                                   class="sortableOpt1">
                                                    <span class="">
                                                        <?=
                                                        AmosIcons::show('user', [], AmosIcons::IC)
                                                        ?>
                                                        <span class="icon-dashboard-name pluginName">
                                                            <?=
                                                            $nameShowParticipants
                                                            ?>
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php }else{
                                $urlDisplayParticipantsMm = "/community/community/participants?communityId={$model->id}";
                                ?>
                            		<div class="square-box"
                                             data-code="open20\amos\admin\widgets\icons\WidgetIconUserProfile">
                                            <div class="square-content item-widget plugin-partecipants">
                                                <a class="dashboard-menu-item" href="<?= $urlDisplayParticipantsMm ?>"
                                                   title=<?= $titleParticipants ?> role="menuitem"
                                                   class="sortableOpt1">
                                                    <span class="">
                                                        <?=
                                                        AmosIcons::show('user', [], AmosIcons::IC)
                                                        ?>
                                                        <span class="icon-dashboard-name pluginName">
                                                            <?=
                                                            $nameShowParticipants
                                                            ?>
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                            <?php } ?>

                            <?php
                            if ($module->showSubcommunitiesWidget === true && $model->showSubCommunityWidget()) {
                                $widgetSubcommunities = Yii::createObject($model->getPluginWidgetClassname());
                                echo $widgetSubcommunities::widget();
                            }
                            if ($model->context == 'open20\amos\projectmanagement\models\Projects') {
                                /** @var \open20\amos\core\record\Record $contentObject */
                                $contentObject = Yii::createObject(open20\amos\projectmanagement\models\Projects::className());
                                $widgetClassname = $contentObject->getPluginWidgetClassname();
                                $widget = Yii::createObject($widgetClassname);
                                echo $widget::widget();
                            }
                            ?>

                        <?php
                        echo \open20\amos\dashboard\widgets\SubDashboardWidget::widget([
                            'model' => $model,
                            'widgets_type' => 'ICON',
                            'disableRender' => $disableRender
                        ]);

                            // Chat widget
                            if (AmosCommunity::instance()->canChat($model) && !AmosCommunity::instance()->placesChatInLastPosition) {
                                echo \open20\amos\comments\widgets\CommentsWidget::widget([
                                    'model' => $model,
                                    'layoutInverted' => true,
                                    'chat' => true
                                ]);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </nav>
        <?php
    }
    ?>

    <?php
    $isSaved = $model->isSavedCommunityAmosDashoardMm();
    $attributeOrder = 'default_order';
    if($isSaved){
        $attributeOrder  = 'n_order';
    }
    echo \open20\amos\dashboard\widgets\SubDashboardFullsizeWidget::widget([
        'model' => $model,
        'widgets_type' => 'GRAPHIC',
        'orderAttribute' => $attributeOrder
    ]);

    // Links Widget
    if (!$module->enableConfigureCommunityDashboard && $module->canLinks($model)) {
        echo \open20\amos\community\widgets\graphics\WidgetGraphicsLinks::widget([
            'community' => $model,
        ]);
    }

    echo'<div class="container">';
    echo'<div class="widgets-icon">';
    if (AmosCommunity::instance()->canChat($model) && AmosCommunity::instance()->placesChatInLastPosition) {
        echo \open20\amos\comments\widgets\CommentsWidget::widget([
            'model' => $model,
            'layoutInverted' => true,
            'chat' => true
        ]);
    }
    echo '</div>';
    echo '</div>';
    ?>
</div>
