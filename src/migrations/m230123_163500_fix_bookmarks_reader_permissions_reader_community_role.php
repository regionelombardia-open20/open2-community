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

use open20\amos\community\rules\UserIsNotCommunityReaderBookmarksRule;
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230123_163500_fix_bookmarks_reader_permissions_reader_community_role
 */
class m230123_163500_fix_bookmarks_reader_permissions_reader_community_role extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => UserIsNotCommunityReaderBookmarksRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Regola che controlla se un utente non ha il ruolo READER',
                'ruleName' => UserIsNotCommunityReaderBookmarksRule::className(),
                'parent' => [
                    'BOOKMARKS_READER'
                ]
            ],
            [
                'name' => 'BOOKMARKS_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model Bookmarks',
                'ruleName' => null,
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        UserIsNotCommunityReaderBookmarksRule::className()
                    ],
                    'removeParents' => [
                        'BOOKMARKS_READER'
                    ]
                ]
            ],
            [
                'name' => 'BOOKMARKS_READER',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Ruolo che permette di vedere i community link',
                'ruleName' => null,
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'VALIDATED_BASIC_USER'
                    ],
                    'removeParents' => [
                        'BASIC_USER'
                    ]
                ]
            ]
        ];
    }
}
