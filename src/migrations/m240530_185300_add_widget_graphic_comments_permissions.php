<?php


use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240530_185300_add_widget_graphic_comments_permissions extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';

        return [
            [
                'name' => \open20\amos\community\widgets\graphics\WidgetGraphicsComments::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetGraphicsComments',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    'BASIC_USER'
                ]
            ],
            [
                'name' => \open20\amos\community\widgets\graphics\WidgetGraphicsLinks::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetGraphicsComments',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    'BASIC_USER'
                ]
            ]
        ];
    }
}