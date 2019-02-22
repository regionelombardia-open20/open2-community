<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\projectmanagement\rules\workflow
 * @category   CategoryName
 */

namespace lispa\amos\community\rules\workflow;


use lispa\amos\community\models\Community;
use lispa\amos\core\rules\BasicContentRule;
use Yii;

class CommunityWorkflowDraftRule extends BasicContentRule
{

    public $name = 'communityWorkflowDraft';

    /**
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @param Community $model
     * @return bool
     * @throws \lispa\amos\community\exceptions\CommunityException
     */
    public function ruleLogic($user, $item, $params, $model)
    {
        if(\Yii::$app->user->can('COMMUNITY_VALIDATOR')){
            return true;
        }

        $modelOld = Community::findOne($model->id);
        if($modelOld->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED){
            return false;
        }
        //se la manifestazione è in carico a qualcouno può modificare lo stoto solo chi l'ha incarico

        return false;
    }

}