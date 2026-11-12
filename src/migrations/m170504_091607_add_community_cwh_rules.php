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
use open20\amos\community\models\CommunityUserMm;
use open20\amos\cwh\models\CwhAuthAssignment;
use yii\db\Migration;

/**
 * Class m170504_091607_add_community_cwh_rules
 */
class m170504_091607_add_community_cwh_rules extends Migration
{

    public function safeUp()
    {
        $auth = \Yii::$app->getAuthManager();
        $cwhModule = Yii::$app->getModule('cwh');
        $classname = Community::className();

        $permissionCreateName = $cwhModule->permissionPrefix . "_CREATE_" . $classname;
        $permissionCwhCreate = $auth->createPermission($permissionCreateName);
        $permissionCwhCreate->description = "Create {$classname}";
        $ok = $auth->add($permissionCwhCreate);
        if (!$ok){
            echo "Error occurred while creating permission $permissionCreateName.\n";
            return false;
        }

        $permissionValidateName = $cwhModule->permissionPrefix . "_VALIDATE_" . $classname;
        $permissionCwhValidate = $auth->createPermission($permissionValidateName);
        $permissionCwhValidate->description = "Validate {$classname}";
        $ok = $auth->add($permissionCwhValidate);
        if (!$ok){
            echo "Error occurred while creating permission $permissionValidateName.\n";
            return false;
        }

        $communities = Community::find()->all();
        foreach ($communities as $community){
            $communityUserMms = CommunityUserMm::findAll([
                'community_id' => $community->id,
                'role' => CommunityUserMm::ROLE_COMMUNITY_MANAGER,
                'status' => CommunityUserMm::STATUS_ACTIVE
            ]);
            foreach ($communityUserMms as $communityUserMm){
                $cwhAuthAssignment = new CwhAuthAssignment();
                $cwhAuthAssignment->user_id = $communityUserMm->user_id;
                $cwhAuthAssignment->item_name = $permissionCreateName;
                $cwhAuthAssignment->cwh_nodi_id = 'community-' . $community->id;
                $ok = $cwhAuthAssignment->save(false);
                if (!$ok){
                    echo "Error occurred while assigning permission of $permissionCwhCreate->description for domain $cwhAuthAssignment->cwh_nodi_id to user with id $cwhAuthAssignment->user_id.\n";
                    return false;
                }
                $cwhAuthAssignment = new CwhAuthAssignment();
                $cwhAuthAssignment->user_id = $communityUserMm->user_id;
                $cwhAuthAssignment->item_name = $permissionValidateName;
                $cwhAuthAssignment->cwh_nodi_id = 'community-' . $community->id;
                $ok = $cwhAuthAssignment->save(false);
                if (!$ok){
                    echo "Error occurred while assigning permission of $permissionCwhValidate->description for domain $cwhAuthAssignment->cwh_nodi_id to user with id $cwhAuthAssignment->user_id.\n";
                    return false;
                }
            }
        }

        return true;
    }

    public function safeDown()
    {
        $auth = \Yii::$app->getAuthManager();
        $cwhModule = Yii::$app->getModule('cwh');
        $classname = Community::className();

        $permissionCreateName = $cwhModule->permissionPrefix . "_CREATE_" . $classname;
        $permissionCwhCreate = $auth->getPermission($permissionCreateName);

        $permissionValidateName = $cwhModule->permissionPrefix . "_VALIDATE_" . $classname;
        $permissionCwhValidate = $auth->getPermission($permissionValidateName);

        if (is_null($permissionCwhCreate) ) {
            echo "Permission $permissionCreateName does not exist.\n";
            return false;
        }
        if (is_null($permissionCwhValidate)){
            echo "Permission $permissionValidateName does not exist.\n";
            return false;
        }

        try {
            CwhAuthAssignment::deleteAll(['item_name' => [$permissionCreateName, $permissionValidateName]]);
        } catch (\Exception $exception) {
            echo "Error occurred while deleting cwh auth assignments\n";
            return false;
        }

        $ok = $auth->remove($permissionCwhCreate);
        if (!$ok) {
            echo "Error occurred while deleting permission $permissionCreateName\n";
            return false;
        }

        $ok = $auth->remove($permissionCwhValidate);
        if (!$ok) {
            echo "Error occurred while deleting permission $permissionValidateName\n";
            return false;
        }

        return true;
    }
}
