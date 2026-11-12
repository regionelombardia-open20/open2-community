<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\views\community
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

use open20\amos\core\forms\editors\m2mWidget\M2MWidget;
use open20\amos\community\AmosCommunity;
use open20\amos\core\user\User;
use open20\amos\projectmanagement\models\Projects;

/**
 * @var \open20\amos\community\models\Community $model
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => AmosCommunity::t('amoscommunity', 'Community'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = AmosCommunity::t('amoscommunity', 'Invite Users');

$searchObj = (Yii::createObject($model->context));

$columns =  [
    'User image' =>[
        'headerOptions' => [
            'id' => AmosCommunity::t('amoscommunity', 'User image'),
        ],
        'contentOptions' => [
            'headers' => AmosCommunity::t('amoscommunity', 'User image'),
        ],
        'label' => AmosCommunity::t('amoscommunity', 'User image'),
        'format' => 'html',
        'value' => function ($model) {
            /** @var \open20\amos\admin\models\UserProfile $userProfile */
            $userProfile = $model->getProfile();

            $url = $userProfile->getAvatarUrl('original');

            return \open20\amos\core\helpers\Html::img($url, [
                'class' => 'gridview-image',
                'alt' => AmosCommunity::t('amoscommunity', 'User image')
            ]);
        }
    ],
    'name' => [
        'label' =>  AmosCommunity::t('amoscommunity', 'Name'),
        'headerOptions' => [
            'id' => AmosCommunity::t('amoscommunity', 'Name'),
        ],
        'contentOptions' => [
            'headers' => AmosCommunity::t('amoscommunity', 'Name'),
        ],
        'value'=> function($url, $model){
            return "<span data-toggle=\"tooltip\" data-placement=\"top\" title=\"testo lungo\" >$model->profile->nomeCognome</span>";
        }
    ]
];

if ($model->context == 'open20\amos\projectmanagement\models\Projects') {
    $columns['organization'] = [
        'label' => AmosCommunity::t('amosproject_management', 'Organizations'),
        //'format' => 'html',
        'value' => function ($model,$rowId,$key) {
            $get = Yii::$app->request->get();

            $project = Projects::findOne(['community_id' => $get['id']]);

            $joinedOrganizations = $project->joinedOrganizations;
            $userOrganizations = [];

            if(!empty($joinedOrganizations)) {
                /** @var \open20\amos\organizzazioni\models\Aziende $joinedOrganization */
                foreach ($joinedOrganizations as $joinedOrganization) {
                    foreach ($joinedOrganization->employees as $joinedOrganizationEmployee) {
                        $userIds[] = $joinedOrganizationEmployee->id;
                    }
                    if(in_array($model->id, $userIds)){
                        $userOrganizations[] = $joinedOrganization->denominazione;
                    }
                }
            }

            return implode(', ', $userOrganizations);
        }
    ];
}
?>

<?= M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => $model->getCommunityUsers(),
    'modelDataArrFromTo' => [
        'from' => 'id',
        'to' => 'id'
    ],
    'modelTargetSearch' => [
        'class' => User::className(),
        'query' => $searchObj->getAdditionalAssociationTargetQuery($model->id),
    ],
    'relationAttributesArray' => ['status', 'role'],
    'targetUrlController' => 'community',
    'moduleClassName' => \open20\amos\community\AmosCommunity::className(),
    'postName' => 'Community',
    'postKey' => 'user',
    'targetColumnsToView' => $columns,
]);
?>
