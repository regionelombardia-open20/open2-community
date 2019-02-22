<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\assets\AmosCommunityAsset;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\rules\DeleteOwnCommunityRelationRule;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\user\User;
use lispa\amos\core\utilities\JsUtility;
use Yii;
use yii\base\Widget;
use yii\web\View;
use yii\widgets\PjaxAsset;

/**
 * Class UserNetworkWidget
 * @package lispa\amos\community\widgets
 */
class UserNetworkWidget extends Widget
{
    /**
     * @var int $userId
     */
    public $userId = null;

    /**
     * @var bool|false true if we are in edit mode, false if in view mode or otherwise
     */
    public $isUpdate = false;

    /**
     * @var string $gridId
     */
    public $gridId = 'user-community-grid';

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->userId)) {
            throw new \Exception(AmosCommunity::t('amoscommunity', 'Missing user id'));
        }
        AmosCommunityAsset::register($this->getView());
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $gridId = $this->gridId;
        $url = \Yii::$app->urlManager->createUrl([
            '/community/community/user-network',
            'userId' => $this->userId,
            'isUpdate' => $this->isUpdate
        ]);
        $searchPostName = 'searchCommunityName';

        $js = JsUtility::getSearchM2mFirstGridJs($gridId, $url, $searchPostName);
        PjaxAsset::register($this->getView());
        $this->getView()->registerJs($js, View::POS_LOAD);

         $itemsMittente = [
             'logo_id' => [
                 'headerOptions' => [
                     'id' => AmosCommunity::t('amoscommunity', 'Logo'),
                 ],
                 'contentOptions' => [
                     'headers' => AmosCommunity::t('amoscommunity', 'Logo'),
                 ],
                 'label' => AmosCommunity::t('amoscommunity', 'Logo'),
                 'format' => 'raw',
                 'value' => function ($model) {
                        return CommunityCardWidget::widget(['model' => $model]);
                 }
             ],
             [
                 'attribute' => 'name',
                 'format' => 'html',
                 'value' => function($model) {
                     /** @var Community $model */
                     return Html::a($model->name, ['/community/community/view', 'id' => $model->id], [
                         'title' => AmosCommunity::t('amoscommunity', 'Apri il profilo della community {community_name}', ['community_name' => $model->name])
                     ]);
                 }
             ],
             'communityType' => [
                 'attribute' => 'communityType',
                 'format' => 'html',
                 'value' => function($model){
                     /** @var Community $model */
                     if(!is_null($model->community_type_id)) {
                         return AmosCommunity::t('amoscommunity', $model->communityType->name);
                     }else{
                         return '-';
                     }
                 }
             ],
             'created_by' => [
                 'attribute' => 'created_by',
                 'format' => 'html',
                 'value' => function($model){
                     /** @var Community $model */
                     $name = '-';
                     if(!is_null($model->created_by)) {
                         $creator = User::findOne($model->created_by);
                         if(!empty($creator)) {
                             return $creator->getProfile()->getNomeCognome();
                         }
                     }
                     return $name;
                 }
             ],
            'status' => [
                'attribute' => 'status',
                'label' => AmosCommunity::t('amoscommunity', 'Status'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'value' => function($model){
             /** @var Community $model */
                    $mmrow = CommunityUserMm::findOne(['user_id' => $this->userId, 'community_id' => $model->id]);
                    return  $mmrow->status;
                }
            ],
            'role' => [
                'attribute' => 'role',
                'label' => AmosCommunity::t('amoscommunity', 'Role'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'value' => function($model){
                    /** @var Community $model */
                    $mmrow = CommunityUserMm::findOne(['user_id' => $this->userId, 'community_id' => $model->id]);
                    return  $mmrow->role;
                }
            ],
         ];

        $communityObject = Yii::createObject(Community::className());
        $communities = $communityObject->getUserNetworkQuery($this->userId);

        if(!Yii::$app->user->can('ADMIN')) {
            $communitiesMms = CommunityUserMm::find()->andWhere(['user_id' => Yii::$app->user->id])->select('community_id');
            $communities->andWhere([
                'or',
                [
                    '<>',
                    'community.community_type_id',
                    \lispa\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED
                ],
                ['in', 'community.id', $communitiesMms]
            ]);
        }

        if(isset($_POST[$searchPostName])){
            $searchName = $_POST[$searchPostName];
            if(!empty($searchName)){
                $communities->andWhere(['LIKE', 'community.name', $searchName]);
            }
        }

        $model = User::findOne($this->userId)->getProfile();
        $loggedUserId = Yii::$app->getUser()->id;
        $this->isUpdate = $this->isUpdate && ($loggedUserId == $model->user_id);
        $module = \Yii::$app->getModule(AmosCommunity::getModuleName());
        $communityType = $module->communityType;

        $widget = \lispa\amos\core\forms\editors\m2mWidget\M2MWidget::widget([
            'model' => $model,
            'modelId' => $model->id,
            'modelData' => $communities,
            'overrideModelDataArr' => true,
            'forceListRender' => true,
            'targetUrlParams' => [
                'viewM2MWidgetGenericSearch' => true
            ],
            'gridId' => $gridId,
            'firstGridSearch' => true,
            'itemsSenderPageSize' => 10,
            'pageParam' => 'page-community',
            'disableCreateButton' => true,
            'createAssociaButtonsEnabled' => !$module->disableCommunityAssociationUserProfile && $this->isUpdate && (CommunityType::COMMUNITY_TYPE_CLOSED != $communityType),
            'btnAssociaLabel' => AmosCommunity::t('amoscommunity', 'Add new community'),
            'actionColumnsTemplate' => $this->isUpdate ? '{joinCommunity}{deleteRelation}' : '',
            'deleteRelationTargetIdField' => 'user_id',
            'targetUrl' => '/community/community/associate-community-m2m',
            'createNewTargetUrl' => '/admin/user-profile/create',
            'moduleClassName' => AmosCommunity::className(),
            'targetUrlController' => 'community',
            'postName' => 'User',
            'postKey' => 'user',
            'permissions' => [
                'add' => 'USERPROFILE_UPDATE',
                'manageAttributes' => 'USERPROFILE_UPDATE'
            ],
            'actionColumnsButtons' => [
                'joinCommunity' => function ($url, $model) {
                    $btn = JoinCommunityWidget::widget(['model' => $model, 'isGridView' => true]);
                    return $btn;
                },

                'deleteRelation' => function ($url, $model) {
                    $url = '/community/community/elimina-m2m';
                    $module = \Yii::$app->getModule(AmosCommunity::getModuleName());
                    $communityId = $model->id;
                    $targetId = $this->userId;
                    $urlDelete = Yii::$app->urlManager->createUrl([
                        $url,
                        'id' => $communityId,
                        'targetId' => $targetId
                    ]);
                    $loggedUser = Yii::$app->getUser();

                    if (\Yii::$app->user->can(DeleteOwnCommunityRelationRule::className(), ['model' => $model]) && !$module->disableCommunityAssociationUserProfile && $loggedUser->id == $this->userId && ($model->created_by != $loggedUser->id || $loggedUser->can('ADMIN'))) {
                        $btnDelete = Html::a( AmosIcons::show('close', ['class' => 'btn-delete-relation']),
//                            '<p class="btn btn-tool-secondary">' . AmosIcons::show('close') . '</p>',
                            $urlDelete,
                            [
                                'title' => AmosCommunity::t('amoscommunity', 'Delete'),
                                'data-confirm' => AmosCommunity::t('amoscommunity', 'Are you sure to cancel your subscrition to this community?'),
                            ]
                        );
                    } else {
                        $btnDelete = '';
                    }
                    return $btnDelete;
                },
            ],
            'itemsMittente' => $itemsMittente,
        ]);

        return "<div id='".$gridId."' data-pjax-container='".$gridId."-pjax' data-pjax-timeout=\"1000\">"
                ."<h3>".AmosCommunity::tHtml('amoscommunity', 'Community to which you participate')."</h3>"
                .$widget."</div>";
    }
}
