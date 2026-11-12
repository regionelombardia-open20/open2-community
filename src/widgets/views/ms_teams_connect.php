<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
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
