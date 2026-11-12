<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\widgets\graphics\views
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
use open20\amos\community\AmosCommunity; 
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;
use open20\amos\comments\AmosComments;
use open20\amos\comments\widgets\CommentsWidget;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\utilities\CurrentUser;

/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \open20\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 * @var bool $linkToSubcommunities
 */
//$moduleCommunity   = \Yii::$app->getModule(AmosCommunity::getModuleName());
//$communitiesModels = $communitiesList->getModels();
$toRefreshSectionId = 'container_comments';
$model = open20\amos\community\models\Community::findOne($widget->community);
?>
<?php if (!CurrentUser::isPlatformGuest()){ ?>
    <div class="comments-widget box-widget-column my-community container">
        <section>
            <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>
            <?php if (false): ?>
                <div class="list-items list-empty"><h3><?= AmosCommunity::t('amoscommunity', '#noCommunity') ?></h3></div>
            <?php endif; ?>
            <div class="list-items">        
                <div class="widget-listbox-option" role="option">
                    <article class="wrap-item-box">
                        <?php
                        $commentsModule = \Yii::$app->getModule(AmosComments::getModuleName());
                        if (!is_null($commentsModule)) {
                            echo CommentsWidget::widget([
                                'model' => $model,
                                'useDesign' => true,
                                'noAttach' => true,                           
                                'pageSize' => $widget->numberToView,
                                'moderator' => CommunityUtil::isManagerUser($model, 'id'),
                            ]);
                        }
                        ?>
                    </article>
                </div>
            </div>
            <?php Pjax::end(); ?>
        </section>
    </div>
<?php } ?>