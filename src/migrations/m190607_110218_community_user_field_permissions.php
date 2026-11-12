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


/**
* Class m190607_110218_community_user_field_permissions*/
class m190607_110218_community_user_field_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'COMMUNITYUSERFIELD_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model CommunityUserField',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_COMMUNITY', \open20\amos\community\rules\CommunityManagerRoleRule::className()]
                ],
                [
                    'name' =>  'COMMUNITYUSERFIELD_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model CommunityUserField',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_COMMUNITY' ,'COMMUNITY_MEMBER']
                    ],
                [
                    'name' =>  'COMMUNITYUSERFIELD_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model CommunityUserField',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_COMMUNITY', \open20\amos\community\rules\CommunityManagerRoleRule::className()]
                ],
                [
                    'name' =>  'COMMUNITYUSERFIELD_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model CommunityUserField',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_COMMUNITY',\open20\amos\community\rules\CommunityManagerRoleRule::className()]
                ],
            // ------------------------

            [
                'name' =>  'COMMUNITYUSERFIELDVAL_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model CommunityUserFieldVal',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY','COMMUNITY_MEMBER']
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDVAL_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model CommunityUserFieldVal',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY','COMMUNITY_MEMBER']
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDVAL_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model CommunityUserFieldVal',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY','COMMUNITY_MEMBER']
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDVAL_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model CommunityUserFieldVal',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY','COMMUNITY_MEMBER']
            ],

            // ------------

            [
                'name' =>  'COMMUNITYUSERFIELDTYPE_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model CommunityUserFieldType',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY',\open20\amos\community\rules\CommunityManagerRoleRule::className()]
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDTYPE_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model CommunityUserFieldType',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY',\open20\amos\community\rules\CommunityManagerRoleRule::className()]
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDTYPE_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model CommunityUserFieldType',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY', \open20\amos\community\rules\CommunityManagerRoleRule::className()]
            ],
            [
                'name' =>  'COMMUNITYUSERFIELDTYPE_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model CommunityUserFieldType',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_COMMUNITY', \open20\amos\community\rules\CommunityManagerRoleRule::className()]
            ],


        ];
    }
}
