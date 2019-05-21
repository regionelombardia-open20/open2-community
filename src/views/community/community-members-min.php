<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community
 * @category   CategoryName
 */

 echo \lispa\amos\community\widgets\mini\CommunityMembersMiniWidget::widget([
    'model' => $model,
    'targetUrlParams' => [
        'viewM2MWidgetGenericSearch' => true
    ],
    'isUpdate' => $isUpdate
]);


?>