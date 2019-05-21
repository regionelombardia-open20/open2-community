<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\community\models\Community $model
 * @var string $tabActive
 */

$this->title = AmosCommunity::t('amoscommunity', '#deleted_community_title');
$this->params['breadcrumbs'][] = ['label' => AmosCommunity::t('amoscommunity', 'Community'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="community-view">
    <div class="col-xs-12">
        <h2><?= AmosCommunity::t('amoscommunity', '#deleted_community_text'); ?></h2>
    </div>
    <div class="clearfix"></div>
</div>
