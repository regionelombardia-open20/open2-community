<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m171120_081544_add_community_permission_admin_all_widget
 */
class m171120_081544_add_community_permission_admin_all_widget extends AmosMigrationPermissions
{
    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => \lispa\amos\community\widgets\icons\WidgetIconAdminAllCommunity::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission description',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY' ],
                'dontRemove' => true
            ],

        ];
    }
}
