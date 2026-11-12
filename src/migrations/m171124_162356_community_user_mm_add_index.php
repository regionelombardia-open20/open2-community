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

use open20\amos\core\migration\libs\common\MigrationCommon;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m171124_162356_community_user_mm_add_index
 */
class m171124_162356_community_user_mm_add_index extends Migration
{
    const TABLE = 'community_user_mm';
    
    private $indexName = '';
    
    public function init()
    {
        parent::init();
        
        $this->indexName = self::TABLE . '_user_community_deleted_at_idx';
    }
    
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->checkIndexExists()) {
            $this->createIndex($this->indexName, self::TABLE, ['user_id', 'community_id', 'deleted_at']);
        } else {
            MigrationCommon::printConsoleMessage('Indice esistente');
        }
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->checkIndexExists()) {
            if ($this->db->driverName = 'mysql') {
                $this->execute('SET FOREIGN_KEY_CHECKS=0;');
            }
            $this->dropIndex($this->indexName, self::TABLE);
            if ($this->db->driverName = 'mysql') {
                $this->execute('SET FOREIGN_KEY_CHECKS=1;');
            }
        } else {
            MigrationCommon::printConsoleMessage('Indice inesistente');
        }
        return true;
    }
    
    /**
     * @return bool
     */
    private function checkIndexExists()
    {
        $query = new Query();
        $query->select('INDEX_NAME')->distinct();
        $query->from('INFORMATION_SCHEMA.STATISTICS');
        $query->where(['TABLE_SCHEMA' => 'poi', 'TABLE_NAME' => self::TABLE]);
        $indexes = $query->all();
        $indexExists = false;
        foreach ($indexes as $index) {
            if (in_array($this->indexName, $index)) {
                $indexExists = true;
            }
        }
        return $indexExists;
    }
}
