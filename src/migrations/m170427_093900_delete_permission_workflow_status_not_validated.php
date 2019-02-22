<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;
use lispa\amos\community\models\Community;

/**
 * Class m170427_093900_delete_permission_workflow_status_not_validated
 */
class m170427_093900_delete_permission_workflow_status_not_validated extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setProcessInverted(true);
    }

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => Community::COMMUNITY_WORKFLOW_STATUS_NOT_VALIDATED,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permession workflow community staus not validated',
                'ruleName' => null,
                'parent' => ['COMMUNITY_VALIDATE', 'AMMINISTRATORE_COMMUNITY', 'COMMUNITY_VALIDATOR']
            ]
        ];
    }
}
