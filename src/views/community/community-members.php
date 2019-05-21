<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community
 * @category   CategoryName
 */

echo \lispa\amos\community\widgets\CommunityMembersWidget::widget([
    'model' => $model,
    'showRoles' => $showRoles,
    'showAdditionalAssociateButton' => $showAdditionalAssociateButton,
    'additionalColumns' => $additionalColumns,
    'viewEmail' => $viewEmail,
    'checkManagerRole' => $checkManagerRole,
    'addPermission' => $addPermission,
    'manageAttributesPermission' => $manageAttributesPermission,
    'forceActionColumns' => $forceActionColumns,
    'actionColumnsTemplate' => $actionColumnsTemplate,
    'viewM2MWidgetGenericSearch' => $viewM2MWidgetGenericSearch,
    'targetUrlParams' => $targetUrlParams,
    'enableModal' => $enableModal,
    'gridId' => $gridId,
    'communityManagerRoleName' => $communityManagerRoleName
]);


?>