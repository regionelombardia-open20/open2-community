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
class m180306_153522_permissions_community_roles_rules extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \lispa\amos\community\rules\AuthorRoleRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if you are an author',
                'ruleName' => \lispa\amos\community\rules\AuthorRoleRule::className(),
                'parent' => ['COMMUNITY_READER','COMMUNITY_MEMBER']
            ],
            [
                'name' => \lispa\amos\community\rules\EditorRoleRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if you are an author',
                'ruleName' => \lispa\amos\community\rules\EditorRoleRule::className(),
                'parent' => ['COMMUNITY_READER','COMMUNITY_MEMBER']
            ],
            [
                'name' => \lispa\amos\community\rules\CommunityManagerRoleRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Check if you are an author',
                'ruleName' => \lispa\amos\community\rules\CommunityManagerRoleRule::className(),
                'parent' => ['COMMUNITY_READER','COMMUNITY_MEMBER']
            ],
        ];
    }
}
