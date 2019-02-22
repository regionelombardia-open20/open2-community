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
 * Class m170719_122922_permissions_community
 */
class m170719_122922_permissions_community extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \lispa\amos\community\rbac\UpdateOwnCommunityProfile::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di aggiornare il proprio profilo della community',
                'ruleName' => \lispa\amos\community\rbac\UpdateOwnCommunityProfile::className(),
                'parent' => ['ADMIN', 'BASIC_USER']
            ],
            [
                'name' => \lispa\amos\community\rbac\UpdateOwnNetworkCommunity::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di aggiornare le community nella tua rete',
                'ruleName' => \lispa\amos\community\rbac\UpdateOwnNetworkCommunity::className(),
                'parent' => ['ADMIN', 'BASIC_USER']
            ]
        ];
    }
}
