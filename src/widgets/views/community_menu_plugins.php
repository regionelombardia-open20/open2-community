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
use open20\amos\community\widgets\CommunityHeaderWidget;

/**
 * @var $widgets
 * @var $limit
 * @var $model
 */
$i = 1;

foreach ($widgets as $widget) {
    $label = $widget['icon'] . '<span class="label-menu-plugin">' . $widget['label'] . '</span>';
    if ($i <= $limit) {
        $active = false;
        if ($widget['moduleName'] == \Yii::$app->controller->module->id) {
            $active = true;
        }
        if (!empty($widget['bulletCount'])) {
            $label = '<span class="badge-news"></span>' . $label;
        }
        echo \yii\helpers\Html::a($label, $widget['url'], [
            'title' => $widget['description'],
            'target' => $widget['targetUrl'],
            'class' => $active ? 'active nav-item-plugin' . $class : 'nav-item-plugin' . $class
        ]);
    }
    $i++;

    $status = null;
    if (!\Yii::$app->user->isGuest) {
        $status = \open20\amos\community\widgets\CommunityHeaderWidget::getSubscribtionStatus($model);
    }
    $unsubscribeClass = !($status == CommunityHeaderWidget::SUBSCRIBTION_STATUS_SUBSCRIBED) ? 'not-subscribed' : '';
} ?>

<?php if (count($widgets) > $limit) {

?>
    <div class="menu-other-community-plugin <?= $unsubscribeClass ?>">
        <div class="dropdown wrapper-dropdown-other-plugin">
            <button class="btn btn-other-menu dropdown-toggle" type="button" id="dropdownOtherPluginMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" title="<?= AmosCommunity::t('amoscommunity', 'Tutti i plugin') ?>"><span class="mdi mdi-dots-horizontal"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-other-plugin" aria-labelledby="dropdownOtherPluginMenu">
                <ul class="list-menu-dropdown">
                    <?php foreach ($widgets as $widget) {
                        $active = false;
                        if ($widget['moduleName'] == \Yii::$app->controller->module->id) {
                            $active = true;
                        }
                        $label = $widget['icon'] . '<span class="label-menu-plugin">' . $widget['label'] . '</span>'; ?>
                        <li>
                            <?php if (!empty($widget['bulletCount'])) {
                                $label = '<span class="badge-news"></span>' . $label;
                            } ?>
                            <?= \yii\helpers\Html::a($label, $widget['url'], [
                                'title' => $widget['description'],
                                'target' => $widget['targetUrl'],
                                'class' => $active ? 'active nav-item-plugin nav-item-plugin-dropdown' : 'nav-item-plugin nav-item-plugin-dropdown'
                            ]) ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

<?php } ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prendi tutti i modal dentro .navbar-community-wrapper
        var modals = document.querySelectorAll('.navbar-community-wrapper .modal');

        // Itera attraverso ciascun modal e spostalo fuori dalla navbar-community-wrapper
        modals.forEach(function(modal) {
            document.body.appendChild(modal);
        });
    });

    window.addEventListener('load', function() {
        // Seleziona l'elemento .navbar-community-right
        var navbarCommunityRight = document.querySelector('.navbar-community-right');

        // Seleziona l'elemento .menu-other-community-plugin
        var menuOtherCommunityPlugin = document.querySelector('.menu-other-community-plugin.not-subscribed');

        if (navbarCommunityRight && menuOtherCommunityPlugin) {
            // Calcola la larghezza di .navbar-community-right
            var navbarWidth = navbarCommunityRight.offsetWidth;

            // Sottrai 110 pixel dalla larghezza calcolata
            var newRightValue = navbarWidth - 0;

            // Imposta la proprietà right di .menu-other-community-plugin
            menuOtherCommunityPlugin.style.right = newRightValue + 'px';
        }
    });
</script>