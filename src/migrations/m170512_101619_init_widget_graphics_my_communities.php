<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\dashboard\models\AmosWidgets;
use lispa\amos\core\migration\AmosMigrationWidgets;

/**
 * Class m170512_101619_init_widget_graphics_my_communities
 */
class m170512_101619_init_widget_graphics_my_communities extends AmosMigrationWidgets
{
    const MODULE_NAME = 'community';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities::className(),
                'type' => AmosWidgets::TYPE_GRAPHIC,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'child_of' => \lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard::className(),
                'default_order' => 1
            ],
        ];
    }
}
