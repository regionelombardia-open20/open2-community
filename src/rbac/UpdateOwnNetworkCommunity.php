<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
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

namespace open20\amos\community\rbac;

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Class UpdateOwnUserProfile
 * @package open20\amos\community\rbac
 */
class UpdateOwnNetworkCommunity extends Rule
{
    public $name = 'isCommunityInYourNetwork';
    public $description = '';

    public function execute($user, $item, $params)
    {
        /** @var Community $model */
        $model = ((isset($params['model']) && $params['model']) ? $params['model'] : new Community());

        if (!isset($model->id)) {
            $post = \Yii::$app->getRequest()->post();
            $get = \Yii::$app->getRequest()->get();
            if (isset($get['id'])) {
                $model = $this->instanceModel($model, $get['id']);
            } elseif (isset($post['id'])) {
                $model = $this->instanceModel($model, $post['id']);
            }
        }

        if (($model instanceof Community)) {
            if (!empty($model->getWorkflowStatus())) {
                if (($model->getWorkflowStatus()->getId() == Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE && $model->validated_once !=1 ) && !(\Yii::$app->user->can('COMMUNITY_VALIDATOR', ['model' => $model]))) {
                    return false;
                }
            }
            return $model->isNetworkUser($model->id, $user);
        }
        return false;
    }

    /**
     * @param Community $model
     * @param int $modelId
     * @return mixed
     */
    private function instanceModel($model, $modelId)
    {
        /** @var UserProfile $userProfileInstance */
        $userProfileInstance = AmosCommunity::instance()->createModel('Community');
        $instancedModel = $userProfileInstance::findOne($modelId);

        if (!is_null($instancedModel)) {
            $model = $instancedModel;
        }

        return $model;
    }
}
