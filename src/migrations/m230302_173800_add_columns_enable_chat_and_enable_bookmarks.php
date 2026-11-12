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
 * Class m230302_173800_add_columns_enable_chat_and_enable_bookmarks
 */
class m230302_173800_add_columns_enable_chat_and_enable_bookmarks extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY,
            'enable_chat',
            $this->boolean()->defaultValue(true)->after('hide_participants')->comment('Enable chat')
        );
        $this->addColumn(
            self::COMMUNITY,
            'enable_bookmarks',
            $this->boolean()->defaultValue(true)->after('enable_chat')->comment('Enable bookmarks')
        );
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::COMMUNITY, 'enable_chat');
        $this->dropColumn(self::COMMUNITY, 'enable_bookmarks');
        return true;
    }
}