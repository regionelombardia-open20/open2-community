<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community
 * @category   CategoryName
 */

use lispa\amos\core\forms\editors\m2mWidget\M2MWidget;
use lispa\amos\community\AmosCommunity;
use lispa\amos\core\user\User;
use lispa\amos\projectmanagement\models\Projects;

/**
 * @var \lispa\amos\community\models\Community $model
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
            /** @var \lispa\amos\admin\models\UserProfile $userProfile */
            $userProfile = $model->getProfile();

            $url = $userProfile->getAvatarUrl('original');

            return \lispa\amos\core\helpers\Html::img($url, [
                'class' => 'gridview-image',
                'alt' => AmosCommunity::t('amoscommunity', 'User image')
            ]);
        }
    ],
    'name' => [
        'attribute' => 'profile.surnameName',
        'label' =>  AmosCommunity::t('amoscommunity', 'Name'),
        'headerOptions' => [
            'id' => AmosCommunity::t('amoscommunity', 'Name'),
        ],
        'contentOptions' => [
            'headers' => AmosCommunity::t('amoscommunity', 'Name'),
        ]
    ]
];

if ($model->context == 'lispa\amos\projectmanagement\models\Projects') {
    $columns['organization'] = [
        'label' => AmosCommunity::t('amosproject_management', 'Organizations'),
        //'format' => 'html',
        'value' => function ($model,$rowId,$key) {
            $get = Yii::$app->request->get();

            $project = Projects::findOne(['community_id' => $get['id']]);

            $joinedOrganizations = $project->joinedOrganizations;
            $userOrganizations = [];

            if(!empty($joinedOrganizations)) {
                /** @var \lispa\amos\organizzazioni\models\Aziende $joinedOrganization */
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
    'moduleClassName' => \lispa\amos\community\AmosCommunity::className(),
    'postName' => 'Community',
    'postKey' => 'user',
    'targetColumnsToView' => $columns,
]);
?>
