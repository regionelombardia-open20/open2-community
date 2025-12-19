<?php


/**
 *
 */
?>


<div class="m-b-15">
    <?= $model->description ?>
</div>
<div class="m-b-15">
   

</div>
<div class="m-b-15">
    <?php
    $tagsWidget = '';

    $tagsWidget = \open20\amos\core\forms\ListTagsWidget::widget([
        'userProfile' => $model->id,
        'className' => $model->className(),
        'viewFilesCounter' => true,
    ]);
    ?>
    <div class="tag-container">
        <?= $tagsWidget ?>
    </div>

</div>

<!-- <div class="text-right">
    <em>< ?= \Yii::t('ski_trucks', "Fonte dati") . ':' . ' ' ?><a href="https://www.sportinmontagna.regione.lombardia.it/osm/#/home" target="_blank" title="< ?= \Yii::t('ski_trucks', "Vai al sito")?>">Sport in montagna</a></em>
</div> -->
