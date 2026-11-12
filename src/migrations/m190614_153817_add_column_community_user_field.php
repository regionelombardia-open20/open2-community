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
 * Class m170301_090817_alter_table_community_add_flags
 *
 * Create flag fields:
 * 'validated_once' : true if community has been validated at least one time
 * 'visible_on_edit': true if community must still be visible if is in editing status and validated_once is true
 */
class m190614_153817_add_column_community_user_field extends \yii\db\Migration
{
    const COMMUNITY = 'community_user_field';


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::COMMUNITY, 'validator_classname', $this->string()->after('required'));
        $this->addColumn(self::COMMUNITY, 'unique', $this->integer(1)->defaultValue(0)->after('required'));
        return true;
    }
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::COMMUNITY, 'validator_classname');
        $this->dropColumn(self::COMMUNITY, 'unique');
        return true;
    }
}
