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

class m221014_101700_alter_column_status_bookmarks extends \yii\db\Migration
{
    const TABLE_NAME = 'bookmarks';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn(self::TABLE_NAME, 'status', 'string not null');
        return true;
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn(self::TABLE_NAME, 'status', 'integer');
        return true;
    }
}