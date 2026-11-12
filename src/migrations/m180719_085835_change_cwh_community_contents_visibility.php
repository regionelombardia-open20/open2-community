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
 * Class m180719_085835_change_cwh_community_contents_visibility
 */
class m180719_085835_change_cwh_community_contents_visibility extends Migration {

    /**
     * string TABLENAME
     */
    const COMMUNITY = '{{%community}}';
    const CWH_CONFIG = '{{%cwh_config}}';
    const RAW_SQL_COMMUNITY = 'select 
concat(\'community-\',`community`.`id`) AS `id`,
3 AS `cwh_config_id`,
`community`.`id` AS `record_id`,
\'open20\\\\amos\\\\community\\\\models\\\\Community\' AS `classname`,
(CASE `community`.`community_type_id` WHEN 1 THEN 1 ELSE  0 END) AS `visibility`,
`community`.`created_at` AS `created_at`,
`community`.`updated_at` AS `updated_at`,
`community`.`deleted_at` AS `deleted_at`,
`community`.`created_by` AS `created_by`,
`community`.`updated_by` AS `updated_by`,
`community`.`deleted_by` AS `deleted_by` 

from `community`';
    const NEW_RAW_SQL_COMMUNITY = 'select 
concat(\'community-\',`community`.`id`) AS `id`,
3 AS `cwh_config_id`,
`community`.`id` AS `record_id`,
\'open20\\\\amos\\\\community\\\\models\\\\Community\' AS `classname`,
`community`.`contents_visibility` AS `visibility`,
`community`.`created_at` AS `created_at`,
`community`.`updated_at` AS `updated_at`,
`community`.`deleted_at` AS `deleted_at`,
`community`.`created_by` AS `created_by`,
`community`.`updated_by` AS `updated_by`,
`community`.`deleted_by` AS `deleted_by` 

from `community`';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchemaComm = \Yii::$app->db->schema->getTableSchema(self::COMMUNITY);
        if (!isset($tableSchemaComm->columns['contents_visibility'])) {
            $this->addColumn(self::COMMUNITY, 'contents_visibility', $this->boolean()->notNull()->defaultValue(0)->after('hide_participants'));
        }
        $tableSchema = $this->db->schema->getTableSchema(self::CWH_CONFIG);
        $rawSqlColumn = $tableSchema->getColumn('raw_sql');
        if (!is_null($rawSqlColumn)) {
            $this->update(self::CWH_CONFIG, ['raw_sql' => self::NEW_RAW_SQL_COMMUNITY], ['tablename' => \open20\amos\community\models\Community::tableName()]);
        }
        \open20\amos\cwh\utility\CwhUtil::createCwhView();
        return true;
    }

    public function safeDown() {
        $this->dropColumn(self::COMMUNITY, 'contents_visibility');
        $tableSchema = $this->db->schema->getTableSchema(self::CWH_CONFIG);
        $rawSqlColumn = $tableSchema->getColumn('raw_sql');
        if (!is_null($rawSqlColumn)) {
            $this->update(self::CWH_CONFIG, ['raw_sql' => self::RAW_SQL_COMMUNITY], ['tablename' => \open20\amos\community\models\Community::tableName()]);
        }
        \open20\amos\cwh\utility\CwhUtil::createCwhView();
        return true;
    }
}
