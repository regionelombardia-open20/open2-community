<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\rules
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

namespace open20\amos\community\rules;

use open20\amos\community\models\Community;
use open20\amos\core\rules\DefaultOwnContentRule;

/**
 * Class CommunityValidatorUpdateContentRule
 * @package open20\amos\community\rules
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
