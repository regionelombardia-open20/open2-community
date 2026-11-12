<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
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



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240607_104600_add_columns_ms_teams extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_channel_id',
            $this->string()->defaultValue(null)->after('enable_bookmarks')->comment('Ms Channel id')
        );
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_team_id',
            $this->string()->defaultValue(null)->after('enable_bookmarks')->comment('Ms Team id')
        );

        $this->addColumn(
            self::COMMUNITY,
            'enable_msgraph_teams',
            $this->integer(1)->defaultValue(0)->after('enable_bookmarks')->comment('Enable Microsoft TEAMS')
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(
            self::COMMUNITY,
            'msgraph_channel_id');
        $this->dropColumn(
            self::COMMUNITY,
            'msgraph_team_id');

        $this->dropColumn(
            self::COMMUNITY,
            'enable_msgraph_teams' );
        return true;
    }
}