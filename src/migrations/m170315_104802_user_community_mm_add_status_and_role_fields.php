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

use yii\db\Migration;

/**
 * Class m170315_104802_user_community_mm_add_status_and_role_fields
 */
class m170315_104802_user_community_mm_add_status_and_role_fields extends Migration
{
    const COMMUNITY_USER_MM = 'community_user_mm';
    const STATUS = 'status';
    const ROLE = 'role';
    const COLUMN_STATUS_TYPE = "VARCHAR(100) AFTER user_id " ;
    const COLUMN_ROLE_TYPE = "VARCHAR(100) AFTER status " ;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::COMMUNITY_USER_MM, self::STATUS, self::COLUMN_STATUS_TYPE);
        $this->addColumn(self::COMMUNITY_USER_MM, self::ROLE, self::COLUMN_ROLE_TYPE);
        $this->update(self::COMMUNITY_USER_MM, [self::STATUS => 'ACTIVE']);
        $this->update(self::COMMUNITY_USER_MM, [self::ROLE => 'COMMUNITY_MANAGER']);

        return true;
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::COMMUNITY_USER_MM, self::ROLE);
        $this->dropColumn(self::COMMUNITY_USER_MM, self::STATUS);
        return true;
    }
}
