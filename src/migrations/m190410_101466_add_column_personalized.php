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
class m190410_101466_add_column_personalized extends Migration {

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $table = $this->db->schema->getTableSchema(\open20\amos\community\models\CommunityAmosWidgetsMm::tableName());
        if (!isset($table->columns['personalized'])) {
            $this->addColumn(\open20\amos\community\models\CommunityAmosWidgetsMm::tableName(), 'personalized', $this->integer(1)->defaultValue(0)->after('amos_widgets_id'));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn(\open20\amos\community\models\CommunityAmosWidgetsMm::tableName(), 'personalized');
    }

}
