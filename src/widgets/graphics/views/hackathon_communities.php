<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\widgets\graphics\views
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\Pjax;
use lispa\amos\community\assets\AmosCommunityAsset;
$assetCommunity = AmosCommunityAsset::register($this);
/**
 * @var View $this
 * @var ActiveDataProvider $communitiesList
 * @var \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities $widget
 * @var string $toRefreshSectionId
 */

$moduleDocumenti = \Yii::$app->getModule(AmosCommunity::getModuleName());

?>
<div class="grid-item">
    <div class="box-widget hackathon-widget">
        <div class="box-widget-toolbar row nom">
            <h2 class="box-widget-title col-xs-10 nop"><?= AmosCommunity::t('amoscommunity', 'Hackathon') ?></h2>
            <?php if(isset($moduleCommunity) && !$moduleCommunity->hideWidgetGraphicsActions) { ?>
                <?= WidgetGraphicsActions::widget([
                    'widget' => $widget,
                    'tClassName' => AmosCommunity::className(),
                    'actionRoute' => ['/community/community-wizard/introduction'],
                    'toRefreshSectionId' => $toRefreshSectionId,
                    'permissionCreate' => 'COMMUNITY_CREATE'
                ]); ?>
            <?php } ?>
        </div>
        <section>
            <?= Html::img($assetCommunity->baseUrl . '/images/hackwidget.jpg') ?>
            <?= Html::a('Scopri di più', $url, ['class' => 'btn btn btn-navigation-secondary']); ?>
        </section>
    </div>
</div>