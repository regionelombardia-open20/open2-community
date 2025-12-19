<?php

namespace open20\amos\community\widgets;

use open20\amos\admin\models\UserProfile;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityMsgraphTokens;
use yii\base\Widget;

class MsTeamsConnectWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $moduleCommunity = \Yii::$app->getModule('community');
        $profile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id ])->one();
        if($profile && $profile->enable_msgraph && $moduleCommunity->enableMicrosoftTeams) {
            $isConnected = CommunityMsgraphTokens::find()->andWhere(['user_id' => \Yii::$app->user->id])->count();
            if (!$isConnected) {
                return $this->render('ms_teams_connect');
            }
        }
        return '';

    }


}