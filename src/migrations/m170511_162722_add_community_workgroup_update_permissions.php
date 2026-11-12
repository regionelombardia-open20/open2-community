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
use open20\amos\community\rules\UpdateOwnWorkgroupsRule;
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

/**
 * Class m170511_162722_add_community_workgroup_update_permissions
 */
class m170511_162722_add_community_workgroup_update_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return ArrayHelper::merge(
            $this->setModelPermissions(),
            $this->setWorkflowPermissions()
        );
    }
    
    private function setModelPermissions()
    {
        return [
            [
                'name' => UpdateOwnWorkgroupsRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Update permission for model Community',
                'ruleName' => UpdateOwnWorkgroupsRule::className(),
            ],
            [
                'name' => 'COMMUNITY_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Update permission for model Community',
                'parent' => [UpdateOwnWorkgroupsRule::className()],
                'dontRemove' => true
            ]
        ];
    }
    
    private function setWorkflowPermissions()
    {
        return [
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_DRAFT,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission workflow community status draft',
                'parent' => [UpdateOwnWorkgroupsRule::className()],
                'dontRemove' => true
            ],
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission workflow community status to validate',
                'parent' => [UpdateOwnWorkgroupsRule::className()],
                'dontRemove' => true
            ],
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission workflow community status validated',
                'parent' => [UpdateOwnWorkgroupsRule::className()],
                'dontRemove' => true
            ]
        ];
    }
}
