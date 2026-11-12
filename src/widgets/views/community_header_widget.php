<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
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
use open20\amos\community\utilities\CommunityUtil;

/**
 * @var $dynamicModel \yii\base\DynamicModel
 * @var $form \open20\amos\core\forms\ActiveForm
 * @var $model \open20\amos\community\models\Community
 */

//\open20\amos\layout\assets\SpinnerWaitAsset::register($this);
?>
<?php
$configs = $model->getMessageAlertMsTeams();

echo $this->render('parts/header_community', ['model' => $model, 'isOrganizzazioniCommunity' => $isOrganizzazioniCommunity]);
echo $this->render('parts/menu_community', ['model' => $model, 'isOrganizzazioniCommunity' => $isOrganizzazioniCommunity]);
?>
<!--    <div class="loading loading-community"></div>-->

<?php
$title = AmosCommunity::t('amoscommunity', 'Dashboard generale');
if ($model->parent_id) {
    $title = AmosCommunity::t('amoscommunity', 'Dashboard canale');
    $parent = Community::findOne($model->parent_id);
    if ($parent->parent_id) {
        $title = AmosCommunity::t('amoscommunity', 'Dashboard sottocanale');
    }
}

if (!\Yii::$app->user->isGuest) {
    $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
}

?>
    <div class="container">
        <?php echo $this->render('parts/_alert_ms_teams', $configs) ?>
    </div>

<?php
$isDashboard = \Yii::$app->controller->module->id == 'community' && \Yii::$app->controller->id == 'join' && \Yii::$app->controller->action->id == 'open-join';

if ($isDashboard && $model->community_type_id == \open20\amos\community\models\CommunityType::COMMUNITY_TYPE_OPEN && $status != \open20\amos\community\widgets\CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) { ?>
    <div class="container">
        <div class="alert alert-warning">
            <?=$model->parent_id ? CommunityUtil::SubcommToChannelTrad( "Iscriviti alla community per visualizzarne i contenuti") :  AmosCommunity::t('amoscommunity', 'Iscriviti alla community per visualizzarne i contenuti') ?>
        </div>
    </div>
<?php }

if ($isDashboard && $status == \open20\amos\community\widgets\CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) { ?>
    <div class="container">
        <h2 class="text-secondary m-t-20 h6"><?= strtoupper($title) ?></h2>
    </div>
<?php } ?>