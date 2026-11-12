<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
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
 * Class m170201_101806_insert_community_types
 */
class m170201_101806_insert_community_types extends \yii\db\Migration
{

    const COMMUNITY_TYPES_TABLE = 'community_types';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $communityTypesColumns = Array(
            'id',
            'name',
            'description'
        );

        $rowsToInsert = Array(
            [1, 'Aperta', 'Una community aperta è visibile a tutti e vi ci si può iscrivere liberamente senza richieste di autorizzazione.'],
            [2, 'Riservata', 'Una community riservata è visibile a tutti ma vi ci si può accedere solo in seguito all\'acettazione della richiesta di iscrizione da parte del Community Manager.'],
            [3, 'Chiusa', 'Una community chiusa non è visibile a tutti e vi ci si può accedere solo su invito del Community Manager quando l\'invito viene accettato dall\'utente).']
        );

        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable(self::COMMUNITY_TYPES_TABLE);
        $this->batchInsert(self::COMMUNITY_TYPES_TABLE, $communityTypesColumns, $rowsToInsert);
        $this->execute("SET foreign_key_checks = 1;");
        return true;
    }

    public function safeDown()
    {
        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable(self::COMMUNITY_TYPES_TABLE);
        $this->execute("SET foreign_key_checks = 1;");
        return true;
    }
}
