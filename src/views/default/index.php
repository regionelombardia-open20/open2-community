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

/** @var \open20\amos\dashboard\models\AmosUserDashboards $currentDashboard * */
/** @var \yii\web\View $this * */

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\views\assets\AmosCoreAsset;
use open20\amos\dashboard\AmosDashboard;
use open20\amos\dashboard\assets\ModuleDashboardAsset;

AmosCoreAsset::register($this);

ModuleDashboardAsset::register($this);

AmosIcons::map($this);

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = ['label' => AmosCommunity::t('amoscommunity', 'Community'), 'url' => ['/community']];

?>

<input type="hidden" id="saveDashboardUrl"
       value="<?= Yii::$app->urlManager->createUrl(['dashboard/manager/save-dashboard-order']); ?>"/>
<input type="hidden" id="currentDashboardId"
       value="<?= $currentDashboard['id'] ?>"/>

<div id="dashboard-edit-toolbar" class="pull-right hidden">
    <?= Html::a(AmosCommunity::t('amoscommunity', 'Save'), 'javascript:void(0);', [
        'id' => 'dashboard-save-button',
        'class' => 'btn btn-success bk-saveOrder',
    ]);
    ?>

    <?= Html::a(AmosCommunity::t('amoscommunity', 'Cancel'), \yii\helpers\Url::current(), [
        'class' => 'btn btn-danger bk-saveDelete',
    ]);
    ?>

</div>

<?php /*
* @$widgetsIcon elenco dei plugin ad icona
* @$widgetsGrafich elenco dei plugin ad grafici
* @$dashboardsNumber numero delle dashboard da mostrare
*/

?>

<nav data-dashboard-index="<?= $currentDashboard->slide ?>">

    <div class="actions-dashboard-container">
        <ul id="widgets-icon" class="bk-sortableIcon plugin-list"
            role="menu">
            <?php //indice di questa dashboard
            $thisDashboardIndex = 'dashboard_' . $currentDashboard->slide;

            //recupera i widgets di questa dashboard
            $thisDashboardWidgets = $currentDashboard->amosWidgetsSelectedIcon;

            if ($thisDashboardWidgets && count($thisDashboardWidgets) > 0) {

                foreach ($thisDashboardWidgets as $widget) {
                    $widgetObj = Yii::createObject($widget['classname']);
                    echo $widgetObj::widget();
                }
            } else {
                AmosDashboard::t('amoscommunity', 'There is no widget selected for this dashboard');
            }
            ?>
        </ul>
    </div>

</nav>







