<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\migrations
 * @category   CategoryName
 */



use open20\amos\community\rules\WidgetIconBookmarksRule;
use open20\amos\community\widgets\icons\WidgetIconBookmarks;
use open20\amos\community\widgets\icons\WidgetIconMsPlanner;
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 *
 * Class m230731_093900_update_widget_icon_bookmarks_permissions
 */
class m240718_170100_permission_widget_planner extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => WidgetIconMsPlanner::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso widget icon planner',
                'ruleName' => null,
                'parent' => ['ADMIN','BASIC_USER']

            ],
        ];
    }
}
