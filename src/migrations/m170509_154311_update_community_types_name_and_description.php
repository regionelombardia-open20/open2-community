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

use open20\amos\community\models\CommunityType;

/**
 * Class m170509_154311_update_community_types_name_and_description
 */
class m170509_154311_update_community_types_name_and_description extends \yii\db\Migration
{

    const COMMUNITY_TYPES_TABLE = 'community_types';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $rowsToUpdate = Array(
            ['id' => CommunityType::COMMUNITY_TYPE_OPEN, 'name' => 'Open', 'description' => 'An open community is visible to everyone and it is possible to join it without sending any authorization request'],
            ['id' => CommunityType::COMMUNITY_TYPE_PRIVATE, 'name' => 'Reserved', 'description' => 'A private community is visible to everyone but it is possible to sign into it only after the subscription request acceptance of the Community Manager'],
            ['id' => CommunityType::COMMUNITY_TYPE_CLOSED, 'name' => 'Limited to members', 'description' => 'A community limited to members is not visible to everyone and it is possible to sign into it only on Community Manager invitation, when the invitation is accepted by the user']
        );

        foreach ($rowsToUpdate as $row){
            $communityType = CommunityType::findOne($row['id']);
            $communityType->name = $row['name'];
            $communityType->description = $row['description'];
            $communityType->detachBehaviors();
            $communityType->save(false);
        }
        return true;
    }

    public function safeDown()
    {
        $rowsToUpdate = Array(
            ['id' => CommunityType::COMMUNITY_TYPE_OPEN, 'name' => 'Aperta', 'description' => 'Una community aperta è visibile a tutti e vi ci si può iscrivere liberamente senza richieste di autorizzazione.'],
            ['id' => CommunityType::COMMUNITY_TYPE_PRIVATE, 'name' => 'Riservata', 'description' => 'Una community riservata è visibile a tutti ma vi ci si può accedere solo in seguito all\'acettazione della richiesta di iscrizione da parte del Community Manager.'],
            ['id' => CommunityType::COMMUNITY_TYPE_CLOSED, 'name' => 'Chiusa', 'description' => 'Una community chiusa non è visibile a tutti e vi ci si può accedere solo su invito del Community Manager quando l\'invito viene accettato dall\'utente).']
        );

        foreach ($rowsToUpdate as $row){
            $communityType = CommunityType::findOne($row['id']);
            $communityType->name = $row['name'];
            $communityType->description = $row['description'];
            $communityType->detachBehaviors();
            $communityType->save(false);
        }
        return true;
    }
}
