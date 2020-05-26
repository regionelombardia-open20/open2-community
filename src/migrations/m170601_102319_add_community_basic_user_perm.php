<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */

use open20\amos\core\migration\AmosMigrationPermissions;

class m170601_102319_add_community_basic_user_perm extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [
            [
                'name' => 'COMMUNITY_MEMBER',
                'update' => true,
                'newValues' => [
                    'addParents' => ['VALIDATED_BASIC_USER']
                ]
            ],

        ];
    }
}
