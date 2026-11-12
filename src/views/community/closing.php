<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\views\community
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
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\core\icons\AmosIcons;

/** @var $model \open20\amos\community\models\Community */
/** @var $published bool - If the community is in published status */
/** @var $message string */

$this->title = $model;

?>
<div class="closing progress-container">

    <h2 class="part"><?= AmosCommunity::tHtml('amoscommunity', 'Closing') ?></h2>
    <div class="row m-b-30">
        <div class="col-xs-12">
            <div class="pull-left">
                <div class="like-widget-img color-primary m-t-15">
                    <?= AmosIcons::show('group', [], 'dash'); ?>
                </div>
            </div>
            <div class="text-wrapper">
                <?php if ($published): ?>
                    <h3><?= AmosCommunity::tHtml('amoscommunity', "#community_success") ?></h3>
                <?php else: ?>
                    <h3><?= $message ?></h3>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php if ($published): ?>
    <?= WizardPrevAndContinueButtonWidget::widget([
        'model' => $model,
        'continueLabel' => AmosCommunity::tHtml('amoscommunity', "#manage_invite_participants"),
        'finishUrl' => Yii::$app->urlManager->createUrl(['community/community/update', 'id' => $model->id, 'tabActive' => 'tab-participants']),
        'previousUrl' => Yii::$app->session->get(AmosCommunity::beginCreateNewSessionKey()),
        'previousLabel' => AmosCommunity::tHtml('amoscommunity', "#back_to_communities")
    ]) ?>
<?php else: ?>
    <?= WizardPrevAndContinueButtonWidget::widget([
        'model' => $model,
        'viewPreviousBtn' => false,
        'continueLabel' => AmosCommunity::tHtml('amoscommunity', '#back_to_communities'),
        'finishUrl' => Yii::$app->session->get(AmosCommunity::beginCreateNewSessionKey())
    ]) ?>
<?php endif; ?>
