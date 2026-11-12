<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\events\migrations
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

use open20\amos\core\migration\AmosMigrationWidgets;
use open20\amos\dashboard\models\AmosWidgets;

/**
 * Class m180410_095645_add_events_community_widgets
 */
class m180522_110000_add_community_widgets extends AmosMigrationWidgets
{
    const EVENTS_MODULE_NAME = 'community';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => 'open20\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti',
                'type' => AmosWidgets::TYPE_GRAPHIC,
                'module' => self::EVENTS_MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 40,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1
            ],
            [
                'classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsMyCommunities',
                'type' => AmosWidgets::TYPE_GRAPHIC,
                'module' => self::EVENTS_MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 30,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1
            ],
            [
                'classname' => 'open20\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni',
                'type' => AmosWidgets::TYPE_GRAPHIC,
                'module' => self::EVENTS_MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 20,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1
            ],
            [
                'classname' => 'open20\amos\news\widgets\graphics\WidgetGraphicsUltimeNews',
                'type' => AmosWidgets::TYPE_GRAPHIC,
                'module' => self::EVENTS_MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 10,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1
            ],
        ];
    }
}
