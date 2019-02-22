<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\rules
 * @category   CategoryName
 */

namespace lispa\amos\community\rules;

use lispa\amos\core\rules\DefaultOwnContentRule;
use lispa\amos\projectmanagement\utility\ProjectManagementUtility;
use yii\rbac\Rule;

/**
 * Class DeleteOwnCommunitiesRule
 * @package lispa\amos\community\rules
 */
class DeleteOwnCommunityRelationRule extends Rule
{
    public $name = 'deleteOwnCommunityRelation';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $modulePm = \Yii::$app->getModule('project_management');
        if(!empty($modulePm) && !empty($params['model'])){
            return \Yii::$app->user->can('lispa\amos\projectmanagement\rules\PmDeleteOwnCommunityRelationRule', ['model' => $params['model']]) ;
        }
        else
            return true;
    }
}
