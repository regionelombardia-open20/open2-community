<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\community\models\Community;
use yii\db\Migration;

/**
 * Class m171219_111336_add_community_field_hits
 */
class m180109_093036_add_widgets_subdashboard extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->insert('amos_widgets', [
            'classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'lispa\amos\news\widgets\icons\WidgetIconNewsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'lispa\amos\showcaseprojects\widgets\icons\WidgetIconShowcaseProjectsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->delete('amos_widgets', [
            'classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'lispa\amos\news\widgets\icons\WidgetIconNewsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'lispa\amos\showcaseprojects\widgets\icons\WidgetIconShowcaseProjectsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);
    }
}
