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

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
use open20\amos\community\models\Community;

/**
 * @var yii\web\View $this
 * @var open20\amos\community\models\Bookmarks $model
 */

$this->title = Yii::t('amoscore', 'tutti i bookmarks', [
    'modelClass' => 'Bookmarks',
]);

$this->params['forceBreadcrumbs'][] = ['label' => 'Community', 'url' => ['/community/community/index']];
$this->params['forceBreadcrumbs'][] = ['label' => $model->community->name, 'url' => ['/community/join/open-join', 'id' => $model->community->id]];
$this->params['forceBreadcrumbs'][] = ['label' => 'Bookmarks', 'url' => ['/community/bookmarks/index', 'id' => $model->community->id]];
$this->params['forceBreadcrumbs'][] = ['label' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bookmarks-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'titolo',
            [
                'attribute' => 'link',
                'format' => 'url'
            ],
            [
                'attribute' => 'data_pubblicazione',
                'format' => ['datetime', (isset(Yii::$app->getModule('datecontrol')->displaySettings['data'])) ? Yii::$app->getModule('datecontrol')->displaySettings['data'] : 'dd/MM/yyyy'],
            ],
            [
                'attribute' => 'community_id',
                'label' => 'Community',
                'value' => $model->getCommunityName()
            ],
            [
                'attribute' => 'creatore_id',
                'label' => 'Creatore',
                'value' => $model->getUserNames()
            ],
        ],
    ]) ?>

</div>

<div id="form-actions" class="bk-btnFormContainer pull-right">
    <?= Html::a(Yii::t('amoscore', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?></div>
