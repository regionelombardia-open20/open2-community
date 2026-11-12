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

use open20\amos\core\views\DataProviderView;
use open20\amos\community\models\Community;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\community\models\search\BookmarksSearch $model
 */

$this->title = Yii::t('amoscore', 'tutti i bookmarks', [
    'modelClass' => 'Bookmarks',
]);
$setCommunityBreadcrumbs = true;
$cwhmodule = Yii::$app->getModule('cwh');
if($cwhmodule) {
    $scope = $cwhmodule->getCwhScope();
    if (!is_null($scope)) {
        $setCommunityBreadcrumbs = false;
    }
}
if($setCommunityBreadcrumbs) {
    $this->params['forceBreadcrumbs'][] = ['label' => 'Community', 'url' => ['/community/community/index']];
    $this->params['forceBreadcrumbs'][] = ['label' => Community::findOne(Yii::$app->request->get()['id'])->name, 'url' => ['/community/join/open-join', 'id' => Yii::$app->request->get()['id']]];
}
$this->params['forceBreadcrumbs'][] = ['label' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bookmarks-index">
    <?= $this->render('_search', ['model' => $model]); ?>

    <?= $this->render('_order', ['model' => $model, 'currentView' => $currentView]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,

        'listView' => [
            'itemView' => '_item',
            'masonry' => FALSE,

            // Se masonry settato a TRUE decommentare e settare i parametri seguenti
            // nel CSS settare i seguenti parametri necessari al funzionamento tipo
            // .grid-sizer, .grid-item {width: 50&;}
            // Per i dettagli recarsi sul sito http://masonry.desandro.com

            //'masonrySelector' => '.grid',
            //'masonryOptions' => [
            //    'itemSelector' => '.grid-item',
            //    'columnWidth' => '.grid-sizer',
            //    'percentPosition' => 'true',
            //    'gutter' => '20'
            //]
        ],/*
        'iconView' => [
        'itemView' => '_icon'
        ],
        'mapView' => [
        'itemView' => '_map',          
        'markerConfig' => [
        'lat' => 'domicilio_lat',
        'lng' => 'domicilio_lon',
        'icon' => 'iconMarker',
        ]
        ],
        'calendarView' => [
        'itemView' => '_calendar',
        'clientOptions' => [
        //'lang'=> 'de'
        ],
        'eventConfig' => [
        //'title' => 'titleEvent',
        //'start' => 'data_inizio',
        //'end' => 'data_fine',
        //'color' => 'colorEvent',
        //'url' => 'urlEvent'
        ],
        'array' => false,//se ci sono più eventi legati al singolo record
        //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
        ]*/
    ]); ?>

</div>
