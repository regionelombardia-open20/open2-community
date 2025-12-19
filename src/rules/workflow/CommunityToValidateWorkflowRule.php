<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\projectmanagement\rules\workflow
 * @category   CategoryName
 */

namespace open20\amos\community\rules\workflow;

use open20\amos\core\rules\ToValidateWorkflowContentRule;
use open20\amos\community\models\Community;

class CommunityToValidateWorkflowRule extends ToValidateWorkflowContentRule
{

    public function ruleLogic($user, $item, $params, $model) {
                //if you have the permission to validate a news and you are in Draft, you cannot send the publish request

        if(!empty($model)){
            
            $status = $model->getWorkflowStatus() ? $model->getWorkflowStatus()->getId() : null;
            if($status == Community::COMMUNITY_WORKFLOW_STATUS_DRAFT){
                               
                if(\Yii::$app->user->can(Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED, ['model' => $model])){
                    return false;
                }
            }
        }
        return true;
    }

}