<?php

use \lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;
use lispa\amos\projectmanagement\rules\TaskOrganizationsMmRule;

class m180827_150544_to_validate_community_workflow_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [

            [
                'name' => \lispa\amos\community\rules\workflow\CommunityWorkflowDraftRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Rule to validate rule',
                'ruleName' => \lispa\amos\community\rules\workflow\CommunityWorkflowDraftRule::className(),
                'parent' => ['AMMINISTRATORE_COMMUNITY', 'COMMUNITY_CREATE', 'COMMUNITY_CREATOR', 'COMMUNITY_MEMBER', 'COMMUNITY_UPDATE', 'COMMUNITY_VALIDATE', 'lispa\amos\community\rules\UpdateOwnWorkgroupsRulE'],
                ],
            [
                'name' => 'CommunityWorkflow/DRAFT',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['AMMINISTRATORE_COMMUNITY', 'COMMUNITY_CREATE', 'COMMUNITY_CREATOR', 'COMMUNITY_MEMBER', 'COMMUNITY_UPDATE', 'COMMUNITY_VALIDATE', 'lispa\amos\community\rules\UpdateOwnWorkgroupsRulE'],
                    'addParents' => [\lispa\amos\community\rules\workflow\CommunityWorkflowDraftRule::className()]
                ],
            ],


        ];
    }
}
