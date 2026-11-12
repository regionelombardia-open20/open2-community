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

use \open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;
use open20\amos\projectmanagement\rules\TaskOrganizationsMmRule;

class m180827_150544_to_validate_community_workflow_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [

            [
                'name' => \open20\amos\community\rules\workflow\CommunityWorkflowDraftRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Rule to validate rule',
                'ruleName' => \open20\amos\community\rules\workflow\CommunityWorkflowDraftRule::className(),
                'parent' => ['AMMINISTRATORE_COMMUNITY', 'COMMUNITY_CREATE', 'COMMUNITY_CREATOR', 'COMMUNITY_MEMBER', 'COMMUNITY_UPDATE', 'COMMUNITY_VALIDATE', 'open20\amos\community\rules\UpdateOwnWorkgroupsRulE'],
                ],
            [
                'name' => 'CommunityWorkflow/DRAFT',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['AMMINISTRATORE_COMMUNITY', 'COMMUNITY_CREATE', 'COMMUNITY_CREATOR', 'COMMUNITY_MEMBER', 'COMMUNITY_UPDATE', 'COMMUNITY_VALIDATE', 'open20\amos\community\rules\UpdateOwnWorkgroupsRulE'],
                    'addParents' => [\open20\amos\community\rules\workflow\CommunityWorkflowDraftRule::className()]
                ],
            ],


        ];
    }
}
