<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\views
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
use open20\amos\community\models\Community;
use open20\amos\core\icons\AmosIcons;

$moduleCommunity = Yii::$app->getModule('community');
$bypassWorkflow = $moduleCommunity->forceWorkflow($model);
$fixedCommunityType = !is_null($moduleCommunity->communityType);

?>

<section class="section-data">
    <h2>
        <?= AmosIcons::show('account'); ?>
        <?= AmosCommunity::tHtml('amoscommunity', 'Base information') ?>
    </h2>
    <dl>
        <dt><?= $model->name; ?></dt>
    </dl>
    <?= $model->description; ?>
</section>

<?php if (!$fixedCommunityType || !$bypassWorkflow || !is_null($model->parent_id)): ?>
    <section class="section-data">
        <h2>
            <?= AmosIcons::show('info'); ?>
            <?= AmosCommunity::tHtml('amoscommunity', 'Additional information') ?>
        </h2>
        <?php if (!$fixedCommunityType): ?>
            <dl>
                <dt><?= $model->getAttributeLabel('communityType'); ?></dt>
                <dd><?= isset($model->communityType) ?  AmosCommunity::t('amoscommunity', $model->communityType->name ) : '-' ?></dd>
            </dl>
        <?php endif; ?>
        <?php if (!$bypassWorkflow): ?>
        <dl>
            <dt><?= $model->getAttributeLabel('status'); ?></dt>
            <dd><?= $model->hasWorkflowStatus() ? AmosCommunity::t('amoscommunity', $model->getWorkflowStatus()->getLabel()) : '-' ?></dd>
        </dl>
        <?php endif; ?>
        <?php if($model->parent_id != null): ?>
            <dl>
                <dt><?= AmosCommunity::t('amoscommunity', "Created under the scope of community/organization:") ?></dt>
                <dd><?= Community::findOne($model->parent_id)->name ?></dd>
            </dl>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="section-data">
    <h2>
        <?= AmosIcons::show('info'); ?>
        <?= AmosCommunity::tHtml('amoscommunity', 'Updates') ?>
    </h2>
    <p>
        <?php
        $creatorName = '';
        if(!is_null($model->createdByUser)) {
            /** @var \open20\amos\admin\models\UserProfile $createUserProfile */
            $createUserProfile = $model->createdByUser->getProfile();
            if (!is_null($createUserProfile)) {
                $creatorName = $createUserProfile->getNomeCognome();
            }
        }
        ?>
        <label><?= $model->getAttributeLabel('created_by') ?></label>
        <span><?= $creatorName ?></span>

        <label><?= AmosCommunity::tHtml('amoscommunity', 'at') ?></label>
        <span><?= Yii::$app->getFormatter()->asDatetime($model->created_at) ?></span>
    </p>
    <p>
        <?php
        $updatedByName = '';
        /** @var \open20\amos\admin\models\UserProfile $updateUserProfile */
        $updateUserProfile = $model->updatedByUser->getProfile();
        if (!is_null($updateUserProfile)) {
            $updatedByName = $updateUserProfile->getNomeCognome();
        }
        ?>
        <label><?= $model->getAttributeLabel('updated_by') ?></label>
        <span><?= $updatedByName ?></span>

        <label><?= AmosCommunity::tHtml('amoscommunity', 'at') ?></label>
        <span><?= Yii::$app->getFormatter()->asDatetime($model->updated_at) ?></span>
    </p>
</section>
