<?php

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
