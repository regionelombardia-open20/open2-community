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
use open20\amos\core\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var \open20\amos\community\widgets\graphics\WidgetGraphicsCommunityReports $widget
 * @var array $downloadConfs
 */

$index = 0;

?>

<div class="box-widget">
    <div class="box-widget-toolbar row nom">
        <h2 class="box-widget-title col-xs-10 nop"><?= AmosCommunity::t('amoscommunity', 'Reports') ?></h2>
    </div>
    <section>
        <div role="listbox">
            <div class="list-items">
                <div class="widget-listbox-option row list-items" role="option">
                    <?php foreach ($downloadConfs as $downloadConf): ?>
                        <?php
                        if (isset($downloadConf['hideThis']) && $downloadConf['hideThis']) {
                            continue;
                        }
                        ?>
                        <?php if ($index != 0): ?>
                            <hr>
                        <?php endif; ?>
                        <?php $index++; ?>
                        <article class="col-xs-12 nop">
                            <div class="container-text col-xs-12 nop">
                                <p class="box-widget-subtitle pull-left"><?= $downloadConf['text']; ?></p>
                                <span class="pull-right">
                                    <?= Html::a(AmosCommunity::t('amoscommunity', 'Download'), $downloadConf['url'], [
                                        'class' => 'btn btn-navigation-primary',
                                        'target' => '_popup',
                                        'title' => AmosCommunity::t('amoscommunity', 'Download')
                                    ]); ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</div>
