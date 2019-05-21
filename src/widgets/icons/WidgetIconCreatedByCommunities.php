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

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\search\CommunitySearch;
use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCreatedByCommunities
 * @package lispa\amos\community\widgets\icons
 */
class WidgetIconCreatedByCommunities extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Communities created by me'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of communities created by me'));

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('community');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('group');
        }

        $url = ['/community/community/created-by-communities'];
        $scopeId = $this->checkScope('community');
        if ($scopeId != false) {
            $url = ['/community/subcommunities/created-by-communities', 'id' => $scopeId];
        }
        $this->setUrl($url);

        $this->setCode('COMMUNITY');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );

        $this->setBulletCount(
            $this->makeBulletCounter(null)
        );
    }

    /**
     * Make the number to set in the bullet count.
     * 
     * @param type $user_id
     * @return type
     */
    public function makeBulletCounter($user_id = null)
    {
        $modelSearch = new CommunitySearch();
        $query = $modelSearch->searchCreatedByMeQuery([]);

        return $query
            ->andWhere([Community::tableName() . '.status' => $modelSearch->getDraftStatus()])
            ->asArray()
            ->count();
    }

    /**
     * @return string
     */
    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'Communities created by me');
    }

}
