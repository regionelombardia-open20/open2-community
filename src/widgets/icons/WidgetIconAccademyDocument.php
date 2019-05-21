<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets\icons;

use lispa\amos\attachments\models\File;
use lispa\amos\community\AmosCommunity;
use lispa\amos\core\user\User;
use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconAccademyDocument
 * @package lispa\amos\community\widgets\icons
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
        if (isset(\Yii::$app->params['isPoi']) && (\Yii::$app->params['isPoi'] === true)) {
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
                $cwhScope = $moduleCwh->getCwhScope();
                if (!is_null($cwhScope) && (count($cwhScope) > 0)) {
                    if ($cwhScope['community'] == 2761) {
                        /** @var User $loggedUser */
                        $loggedUser = \Yii::$app->user->identity;
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

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
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
