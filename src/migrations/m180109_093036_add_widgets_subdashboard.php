<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\migrations
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

use open20\amos\community\models\Community;
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
            'classname' => 'open20\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'open20\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'open20\amos\news\widgets\icons\WidgetIconNewsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->insert('amos_widgets', [
            'classname' => 'open20\amos\showcaseprojects\widgets\icons\WidgetIconShowcaseProjectsDashboard',
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
            'classname' => 'open20\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'open20\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'open20\amos\news\widgets\icons\WidgetIconNewsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);

        $this->delete('amos_widgets', [
            'classname' => 'open20\amos\showcaseprojects\widgets\icons\WidgetIconShowcaseProjectsDashboard',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
        ]);
    }
}
