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

use open20\amos\core\views\AmosGridView;

$this->title = "Lista web hooks";
$dataProvider->pagination = false;
?>
<div class="col-md-12 m-b-20">

</div>

<div class="container-change-view">
    <div class="btn-tools-container flexbox">
        <?= \yii\helpers\Html::a('Registra web hooks', ['/community/ms-teams/register-web-hooks'], [
            'class' => 'btn btn-secondary',
            'title' => "Registra web hooks",
            'data-confirm' => "Sei sicuro di procedere?",

        ]) ?>
        <?= \yii\helpers\Html::a('Annulla web hooks', ['/community/ms-teams/unsubscribe-web-hooks'], [
            'class' => 'btn btn-secondary-outline',
            'title' => "Annulla web hooks",
            'data-confirm' => "Sei sicuro di procedere?",
        ]) ?>
        <div class="tools-right">
            <div class="btn-group btn show-hide-element btn-secondary" title="Azzera opzioni di ricerca"
                 data-toggle-element="form-search" tabindex="0"><span class="am am-tune"> </span> <span class="m-l-5">Filtri</span>
            </div>

        </div>
    </div>
</div>
<?= $this->render('_search') ?>
<div>
    <?php
    echo AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
//            'id',
            'resource',
            [
                'attribute' => 'notificationUrl',
                'value' => function ($model) {
                    $array = explode('/community/', $model->notificationUrl);
                    return $array[0];
                },
                'label' => 'Dominio'
            ],
            [
                'attribute' => 'notificationUrl',
                'value' => function ($model) {
                    $array = explode('/community/', $model->notificationUrl);
                    return $array[1];
                },
                'label' => 'Callback'
            ],
            [
                'attribute' => 'expirationDateTime',
                'value' => function ($model) {
                    $dateTime = new DateTime($model->expirationDateTime);
                    return $dateTime->format('d/m/Y H:i');
                }
            ]
        ]
    ]);
    ?>
</div>
