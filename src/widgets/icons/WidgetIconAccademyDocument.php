<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\widgets\icons
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

namespace open20\amos\community\widgets\icons;

use open20\amos\core\user\User;
use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;

use open20\amos\attachments\models\File;

use open20\amos\community\AmosCommunity;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconAccademyDocument
 * @package open20\amos\community\widgets\icons
 */
class WidgetIconAccademyDocument extends WidgetIcon
{

    /**
     * @var bool $downloadEnabled
     */
    private $downloadEnabled = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $url = [''];
        if (isset(Yii::$app->params['isPoi']) && (Yii::$app->params['isPoi'] === true)) {
            $moduleCwh = Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
                $cwhScope = $moduleCwh->getCwhScope();
                if (!is_null($cwhScope) && (count($cwhScope) > 0)) {
                    if ($cwhScope['community'] == 2761) {
                        /** @var User $loggedUser */
                        $loggedUser = Yii::$app->user->identity;
                        $registrazioneLabLombardia = \openinnovation\landing\models\LandingLaboratorioLombardia::findOne(['email' => $loggedUser->email]);
                        if (!is_null($registrazioneLabLombardia) && !is_null($registrazioneLabLombardia->getProposal())) {
                            /** @var File $proposal */
                            $proposal = $registrazioneLabLombardia->getProposal();
                            $proposal->getUrl();
                            $this->downloadEnabled = true;
                            $url = $proposal->getWebUrl();
                        }
                    }
                }
            }
        }

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Proposta allegata'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Visualizza la tua proposta'));
        $this->setIcon('file-text-o');
        $this->setUrl($url);
        $this->setTargetUrl('_blank');
        $this->setCode('COMMUNITY_ACCADEMY_DOCUMENT');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $paramsClassSpan = [];
        }

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        if (!$this->downloadEnabled) {
            return false;
        }

        return parent::isVisible();
    }

}
