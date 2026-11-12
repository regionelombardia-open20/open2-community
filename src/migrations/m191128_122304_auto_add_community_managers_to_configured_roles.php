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

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\migration\libs\common\MigrationCommon;
use yii\db\ActiveQuery;
use yii\db\Migration;

/**
 * Class m191128_122304_auto_add_community_managers_to_configured_roles
 */
class m191128_122304_auto_add_community_managers_to_configured_roles extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Time limit to 8 hours
        set_time_limit(8 * 60 * 60);
        ini_set('memory_limit', '4096M');
        
        $className = 'm191128_122304_auto_add_community_managers_to_configured_roles';
        $communityModule = AmosCommunity::instance();
        if (!is_null($communityModule)) {
            $autoCommunityManagerRoles = $communityModule->autoCommunityManagerRoles;
            if (is_array($autoCommunityManagerRoles)) {
                if (!empty($autoCommunityManagerRoles)) {
                    /** @var ActiveQuery $query */
                    $query = Community::find();
                    $allCommunities = $query->all();
                    foreach ($allCommunities as $community) {
                        /** @var Community $community */
                        $ok = CommunityUtil::autoAddCommunityManagersToCommunity($community, CommunityUserMm::STATUS_ACTIVE, $communityModule);
                        if (!$ok) {
                            MigrationCommon::printConsoleMessage('Errore aggiunta dei community manager alla community con ID "' . $community->id . '"');;
                        }
                    }
                } else {
                    MigrationCommon::printConsoleMessage($className . ': autoCommunityManagerRoles vuoto.');
                }
            } else {
                MigrationCommon::printConsoleMessage($className . ': autoCommunityManagerRoles non è un array.');
            }
        } else {
            MigrationCommon::printConsoleMessage($className . ': modulo community non configurato. Nulla da fare.');
        }
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m191128_122304_auto_add_community_managers_to_configured_roles cannot be reverted.\n";
        return false;
    }
}
