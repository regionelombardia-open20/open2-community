<?php

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
