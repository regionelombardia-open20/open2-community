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
use yii\db\Migration;


/**
 * Class m200924_104110_li_modello_permissions*/
class m240801_184710_community_validations_permissions_1 extends Migration
{

    public function safeUp()
    {

        $auth = \Yii::$app->authManager;
        $rule = \open20\amos\community\rules\workflow\CommunityToValidateWorkflowRule::className();
        $ruleObj = new \open20\amos\community\rules\workflow\CommunityToValidateWorkflowRule();
        $auth->add($ruleObj);
        
        $permissionName = \open20\amos\community\models\Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE;
        $permission = $auth->getPermission($permissionName);
        
        $permission->ruleName = $rule;
        $auth->update($permission->name, $permission);
        
        
    }
}
