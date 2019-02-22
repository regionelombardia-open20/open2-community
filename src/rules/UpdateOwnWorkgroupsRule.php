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

use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityInterface;
use lispa\amos\core\record\Record;
use lispa\amos\core\rules\DefaultOwnContentRule;

/**
 * Class UpdateOwnWorkgroupsRule
 * @package lispa\amos\community\rules
 */
class UpdateOwnWorkgroupsRule extends DefaultOwnContentRule
{
    /**
     * @inheritdoc
     */
    public $name = 'updateOwnWorkgroups';
    
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];
            if (!isset($model->id)) {
                $post = \Yii::$app->getRequest()->post();
                $get = \Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = $this->instanceModel($model, $get['id']);
                } elseif (isset($post['id'])) {
                    $model = $this->instanceModel($model, $post['id']);
                }
            }
            
            if ($model instanceof CommunityInterface) {
                // The model isn't an instance of Community. It checks immediately
                // if the model community creator is the same as the logged user.
                return ($model->community->created_by == $user);
            } elseif (($model instanceof Community) && ($model->context != Community::className())) {
                // The model is an instance of Community. It checks the community context.
                // If the community context is not equal to the community classname it checks
                // if the model community creator is the same as the logged user.
                return ($model->created_by == $user);
            }
        }
        
        return false;
    }
}
