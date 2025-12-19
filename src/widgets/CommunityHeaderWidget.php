<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 10/06/2019
 * Time: 15:52
 */

namespace open20\amos\community\widgets;



use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\user\User;
use yii\base\Widget;

class CommunityHeaderWidget extends Widget
{
    const SUBSCRIBTION_STATUS_SUBSCRIBED = 'subscribed';
    const SUBSCRIBTION_STATUS_WAITING = 'waiting';

    public $model;
    public $form;
    public $isView = false;
    public $user_id;
    public $isOrganizzazioniCommunity = false;

    public function init()
    {
        parent::init();
       if (is_null($this->model)) {
            throw new \Exception(AmosCommunity::t('amoscommunity', 'Missing model'));
        }
       \Yii::$app->params['twoColorIcons'] = true;
    //    \Yii::$app->view->params['containerFullWidth2'] = true;
    }

    public function run()
    {
        $model = $this->model;
        // nella preview community non mostro lo scope se sono nella community principale
        if(\Yii::$app->controller->id == 'community' && \Yii::$app->controller->action->id == 'view'  && $model->parent_id == null){
            return '';
        }
        $this->registerLoader();
        return $this->render('community_header_widget', ['model' => $model, 'isOrganizzazioniCommunity' => $this->isOrganizzazioniCommunity]);
        

    }

    public static function getSubscribtionStatus($model){
        $status = null;
        $userCommunity = CommunityUtil::getMemberCommunityLogged($model->id);
        $user = User::findOne(\Yii::$app->user->id);
        if ($user) {
            $userProfile = $user->userProfile;
        }



        if (!empty($userProfile) && $userProfile->validato_almeno_una_volta && !is_null($userCommunity)) {
            if (in_array($userCommunity->status, [CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER, CommunityUserMm::STATUS_WAITING_OK_USER])) {
                $status = self::SUBSCRIBTION_STATUS_WAITING;
            } else {
                $status = self::SUBSCRIBTION_STATUS_SUBSCRIBED;
            }
        }
        return $status;
    }

    public function registerLoader(){
        $js = <<< JS
            $(window).on('load', function() {
                $('.loading').hide();
    // This code will run once the entire page is fully loaded
});
JS;
        $this->view->registerJs($js);

    }


}