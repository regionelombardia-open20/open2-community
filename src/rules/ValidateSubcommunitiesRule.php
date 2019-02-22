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
use lispa\amos\core\rules\DefaultOwnContentRule;

/**
 * Class CommunityValidatorUpdateContentRule
 * @package lispa\amos\community\rules
 */
class ValidateSubcommunitiesRule extends DefaultOwnContentRule
{
    /**
     * @inheritdoc
     */
    public $name = 'validateSubcommunities';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['model'])) {
            /** @var Community $model */
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

            if($model instanceof Community) {
                if ($model->getCommunityModel()->parent_id == null) {
                    if($model->isNewRecord){
                        $parentId = \Yii::$app->request->getQueryParam('parentId');
                        if(!is_null($parentId)){
                            $parent = $parent = Community::findOne($parentId);
                        }
                    }
                } else {
                    $parent = Community::findOne($model->getCommunityModel()->parent_id);
                }
                if(isset($parent)){
                    return ($parent->isCommunityManager($user));
                }
            }
        }
        return false;
    }
}
