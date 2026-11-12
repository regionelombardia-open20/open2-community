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
use open20\amos\community\rules\ValidateSubcommunitiesRule;
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m180126_091534_fix_validate_subcommunities_permission
 */
class m180126_091534_fix_validate_subcommunities_permission extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'ValidateSubcommunities',
                'type' => Permission::TYPE_PERMISSION,
                'ruleName' => ValidateSubcommunitiesRule::className(),
                'description' => 'Permission to validate subcommunities under a specific community parent',
                'parent' => ['COMMUNITY_MEMBER', 'AMMINISTRATORE_COMMUNITY']
            ],
            [
                'name' => 'COMMUNITY_VALIDATE',
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'addParents' => ['ValidateSubcommunities'],
                ]
            ],
            [
                'name' => 'COMMUNITY_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Update permission for model Community',
                'ruleName' => null,
                'update' => true,
                'newValues' => [
                    'addParents' => ['ValidateSubcommunities'],
                    'removeParents' => [ValidateSubcommunitiesRule::className()],
                ]
            ],
            [
                'name' => ValidateSubcommunitiesRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'removeParents' => ['COMMUNITY_CREATOR', 'COMMUNITY_VALIDATE'],
                ]
            ],
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_DRAFT,
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'removeParents' => [ValidateSubcommunitiesRule::className()],
                ]
            ],
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE,
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'removeParents' => [ValidateSubcommunitiesRule::className()],
                ]
            ],
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED,
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'removeParents' => [ValidateSubcommunitiesRule::className()],
                ]
            ],
            [
                'name' => 'open20\amos\community\widgets\icons\WidgetIconToValidateCommunities',
                'type' => Permission::TYPE_PERMISSION,
                'update' => true,
                'newValues' => [
                    'removeParents' => [ValidateSubcommunitiesRule::className()],
                ]
            ],
        ];
    }
}
