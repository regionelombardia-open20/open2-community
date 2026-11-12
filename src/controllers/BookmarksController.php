<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\controllers
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

namespace open20\amos\community\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\community\AmosCommunity;
use Yii;
use open20\amos\core\helpers\Html;

/**
 * Class BookmarksController
 * This is the class for controller "BookmarksController".
 * @package open20\amos\community\controllers
 */
class BookmarksController extends \open20\amos\community\controllers\base\BookmarksController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $get = Yii::$app->request->get();

        if (\Yii::$app->user->isGuest) {
            $titleSection = AmosCommunity::t('amoscommunity', 'Bookmarks');
            $urlLinkAll   = '';

            $labelSigninOrSignup = AmosCommunity::t('amoscommunity', '#beforeActionCtaLoginRegister');
            $titleSigninOrSignup = AmosCommunity::t(
                'amoscommunity',
                '#beforeActionCtaLoginRegisterTitle',
                ['platformName' => \Yii::$app->name]
            );
            $labelSignin = AmosCommunity::t('amoscommunity', '#beforeActionCtaLogin');
            $titleSignin = AmosCommunity::t(
                'amoscommunity',
                '#beforeActionCtaLoginTitle',
                ['platformName' => \Yii::$app->name]
            );

            $labelLink = $labelSigninOrSignup;
            $titleLink = $titleSigninOrSignup;
            $socialAuthModule = Yii::$app->getModule('socialauth');
            if ($socialAuthModule && ($socialAuthModule->enableRegister == false)) {
                $labelLink = $labelSignin;
                $titleLink = $titleSignin;
            }

            $ctaLoginRegister = Html::a(
                $labelLink,
                isset(\Yii::$app->params['linkConfigurations']['loginLinkCommon']) ? \Yii::$app->params['linkConfigurations']['loginLinkCommon']
                    : \Yii::$app->params['platform']['backendUrl'] . '/' . AmosAdmin::getModuleName() . '/security/login',
                [
                    'title' => $titleLink
                ]
            );
            $subTitleSection  = Html::tag(
                'p',
                AmosCommunity::t(
                    'amoscommunity',
                    '#beforeActionSubtitleSectionGuest',
                    ['platformName' => \Yii::$app->name, 'ctaLoginRegister' => $ctaLoginRegister]
                )
            );
        } else {
            $titleSection = AmosCommunity::t('amoscommunity', 'Bookmarks');

            /*
            $labelLinkAll = AmosNews::t('amoscommunity', 'Tutti i bookmark');
            $urlLinkAll   = '/community/bookmarks/index';
            $titleLinkAll = AmosNews::t('amoscommunity', 'Visualizza la lista dei bookmark');

            $subTitleSection = Html::tag(
                'p',
                AmosNews::t(
                    'amoscommunity',
                    '#beforeActionSubtitleSectionLogged',
                    ['platformName' => \Yii::$app->name]
                )
            );
            */
        }

        $labelCreate = AmosCommunity::t('amoscommunity', 'Nuovo');
        $titleCreate = AmosCommunity::t('amoscommunity', 'Crea un nuovo bookmark');
        $labelManage = AmosCommunity::t('amoscommunity', 'Gestisci');
        $titleManage = AmosCommunity::t('amoscommunity', 'Gestisci i bookmark');
        $urlCreate   = '/community/bookmarks/create?id=' . $get['id'];
        $urlManage   = null;

        $this->view->params = [
           // 'breadcrumbs' => $test,
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'bookmarks',
            'titleSection' => $titleSection,
            #'subTitleSection' => $subTitleSection,
            #'urlLinkAll' => $urlLinkAll,
            #'labelLinkAll' => $labelLinkAll,
            #'titleLinkAll' => $titleLinkAll,
            'labelCreate' => $labelCreate,
            'titleCreate' => $titleCreate,
            'labelManage' => $labelManage,
            'titleManage' => $titleManage,
            'urlCreate' => $urlCreate,
            'urlManage' => $urlManage,
        ];

        // Lasciare qui questo if e return perché se no va in loop...
        if (!parent::beforeAction($action)) {
            return false;
        }
        return true;
    }
}
