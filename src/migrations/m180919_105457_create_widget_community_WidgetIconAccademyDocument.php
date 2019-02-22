<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;

/**
 * Class m180919_105457_create_widget_community_WidgetIconAccademyDocument
 */
class m180919_105457_create_widget_community_WidgetIconAccademyDocument extends AmosMigrationWidgets
{
    const MODULE_NAME = 'community';
    
    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\community\widgets\icons\WidgetIconAccademyDocument::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'child_of' => null,
                'default_order' => 100,
                'dashboard_visible' => 0
            ]
        ];
    }
}
