<?php

use \lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;
use lispa\amos\projectmanagement\rules\TaskOrganizationsMmRule;

class m180419_150544_task_delete_own_community_relation_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [

            [
                'name' => \lispa\amos\community\rules\DeleteOwnCommunityRelationRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Rule on exit form your own community',
                'ruleName' => \lispa\amos\community\rules\DeleteOwnCommunityRelationRule::className(),
                'parent' => ['COMMUNITY_READER'],
            ],

        ];
    }
}
