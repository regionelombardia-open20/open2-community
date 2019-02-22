<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\community\widgets\graphics\WidgetGraphicsCommunityReports;
use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m171219_095101_community_reports_graphic_widget_permission
 */
class m171219_095101_community_reports_graphic_widget_permission extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';
        return [
            [
                'name' => WidgetGraphicsCommunityReports::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetGraphicsCommunityReports',
                'parent' => ['AMMINISTRATORE_COMMUNITY']
            ]
        ];
    }
}
