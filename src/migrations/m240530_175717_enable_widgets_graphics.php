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

/**
 * Class m200702_123817_add_column_redirect
 *
 */
class m240530_175717_enable_widgets_graphics extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets', [
            'sub_dashboard' => 1
        ], ['classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsComments', 'module' => 'community']);

        $this->insert('amos_widgets', [
            'sub_dashboard' => 1,
            'classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsLinks',
            'module' => 'community',
            'default_order' => 1,
            'type'=> 'GRAPHIC'
            ]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', [
            'sub_dashboard' => 0
        ], ['classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsComment', 'module' => 'community']);

        $this->delete('amos_widgets', [
            'sub_dashboard' => 1,
            'classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsLinks',
            'module' => 'community',
            'default_order' => 1,
            'type'=> 'GRAPHIC'
        ]);
        return true;
    }
}
