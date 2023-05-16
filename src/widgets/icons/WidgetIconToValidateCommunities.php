<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\widgets\icons
 * @category   CategoryName
 */

namespace open20\amos\community\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\models\search\CommunitySearch;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconToValidateCommunities
 * @package open20\amos\community\widgets\icons
 */
class WidgetIconToValidateCommunities extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
        ];

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Communities to validate'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of communities to validate'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('community');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('group');
        }

        $url        = ['/community/community/to-validate-communities'];
        $checkScope = $this->checkScope('community');
        if (!empty($checkScope)) {
            $url = ['/community/subcommunities/to-validate-communities', 'id' => $checkScope];
        }

        $this->setUrl($url);
        $this->setCode('COMMUNITY');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(), $paramsClassSpan
            )
        );
    }

    /**
     * @return string
     */
    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'Communities to validate');
    }
}