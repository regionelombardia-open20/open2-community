<?php

namespace open20\amos\community\widgets\icons;

use open20\amos\community\models\Community;
use open20\amos\core\record\Record;
use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;
use open20\amos\community\AmosCommunity;
use Yii;
use yii\helpers\ArrayHelper;

class WidgetIconMsPlanner extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-lightPrimary'
        ];

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Planner'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Planner'));

        if (!empty(Yii::$app->params['twoColorIcons'])) {
            $this->setIconFramework(AmosIcons::TWOTONES);
//            $this->setClassSpan('classe-test');
            $paramsClassSpan = ['classe-test'];

            $this->setIcon('view_week');
        } else if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::AM);
            $this->setIcon('view-week');
            $paramsClassSpan = [];
        } else {
            $this->setIconFramework('dash');
            $this->setIcon('video-camera');
        }

        $moduleCwh = Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $communityId = $scope['community'];
            }
        }


        $this->setUrl(Yii::$app->urlManager->createUrl(['/community/ms-teams/planner-index', 'id' => $communityId]));
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );

        // Read and reset counter from bullet_counters table, bacthed calculated!

//        if ($this->disableBulletCounters == false ) {
//            $this->setBulletCount(Record::getStaticBullet(Record::BULLET_TYPE_ALL, false, 'bookmarks', false, true));
//        }
    }

    public function isVisible()
    {
        if (\Yii::$app->getUser()->can($this->getWidgetPermission())) {
            $moduleCwh = Yii::$app->getModule('cwh');
            $moduleCommunity = Yii::$app->getModule('community');
            if($moduleCommunity && $moduleCommunity->enableMicrosoftTeams && \Yii::$app->user->can('MSTEAMS_CREATOR') &&  \Yii::$app->controller->action->id == 'create'){
                return true;
            }
            if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
                $scope = $moduleCwh->getCwhScope();
                if (isset($scope['community'])) {
                    $communityId = $scope['community'];   if(empty($communityId)){
                        if(\Yii::$app->controller->module->id == 'community') {
                            $communityId = \Yii::$app->request->get('id');
                        }
                    }
                    $community = Community::findOne($communityId);
                    if ($community && $community->enable_msgraph_teams) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}