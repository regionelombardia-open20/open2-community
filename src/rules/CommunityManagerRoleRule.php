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
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\rules\DefaultOwnContentRule;
use yii\helpers\Url;

/**
 * Class CreateSubcommunitiesRule
 * @package open20\amos\community\rules
 */
class CommunityManagerRoleRule extends DefaultOwnContentRule
{
    /**
     * @inheritdoc
     */
    public $name = 'communityManagerRole';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $cwhModule = \Yii::$app->getModule('cwh');
        if (isset($params['model'])) {
            if (isset($cwhModule)) {
                $cwhModule->setCwhScopeFromSession();
                if (!empty($cwhModule->userEntityRelationTable)) {
                    $entityId = $cwhModule->userEntityRelationTable['entity_id'];
                    $model = Community::findOne($entityId);
                    return $this->hasRole($user, $model);
                }
            }
        }
        else {
            //  Check you have the permission when you are in the community dashboard
           $currentUrl = explode("?", Url::current());
            if($currentUrl[0]=='/community/join/index' || $currentUrl[0]=='/community/join'){
                $post = \Yii::$app->getRequest()->post();
                $get = \Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = Community::findOne($get['id']);
                } elseif (isset($post['id'])) {
                    $model = Community::findOne($post['id']);
                }
                if(!empty($model)) {
                    if (!empty($model->getWorkflowStatus())) {
                        if (($model->getWorkflowStatus()->getId() == Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE && $model->validated_once !=1 ) && !(\Yii::$app->user->can('COMMUNITY_VALIDATOR', ['model' => $model]))) {
                            return false;
                        }
                    }
                    return $this->hasRole($user, $model);
                }
            }
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $model Community
     * @return bool
     */
    public function hasRole($user_id, $model){
        $communityUserMm = CommunityUserMm::find()
            ->andWhere(['user_id' => $user_id])
            ->andWhere(['community_id' => $model->id])
            ->andWhere(['role' => CommunityUserMm::ROLE_COMMUNITY_MANAGER])
            ->one();

        if(!empty($communityUserMm)){
            return true;
        }
        return false;
    }
}
