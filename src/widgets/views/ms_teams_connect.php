<?php

use open20\amos\community\AmosCommunity;

$redirectUrl = \yii\helpers\Url::current();
if(\Yii::$app->controller->id == 'user-profile' && \Yii::$app->controller->action->id == 'update'){
        $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        $redirectUrl = "/". \open20\amos\admin\AmosAdmin::getModuleName().'/user-profile/update?id='.$profile->id.'#tab-settings';
}
?>
<?= $this->render('parts/_alert_ms_teams', [
    'title' => AmosCommunity::t('amoscommunity', "Abilita le funzionalità Microsoft con Open2.0"),
    'textDescription' => "<strong class='m-r-5'>" . \yii\helpers\Html::a(AmosCommunity::t('amoscommunity', "Collega ora il tuo account Microsoft"), ['/community/ms-teams/choose-mail-microsoft', 'urlRedirect' => $redirectUrl]) . "</strong>"
        . ' ' . AmosCommunity::t('amoscommunity', "per abilitare le funzionalità avanzate di Open 2.0, quali Canali Teams, TO-DO, File sharing e molto altro.")
]);
?>
