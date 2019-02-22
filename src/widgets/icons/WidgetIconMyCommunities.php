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
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconMyCommunities
 * @package lispa\amos\community\widgets\icons
 */
class WidgetIconMyCommunities extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'My communities'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of my communities'));
        
        $this->setIcon('group');
        $url = ['/community/community/my-communities'];
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $url = ['/community/subcommunities/my-communities', 'id' => $scope['community']];
            }
        }
        $this->setUrl($url);
        $this->setCode('COMMUNITY');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);
        
        $communitySearch = new CommunitySearch();
        $notifier = \Yii::$app->getModule('notify');
        $count = 0;
        if ($notifier) {
            $count = $notifier->countNotRead(Yii::$app->getUser()->id, Community::class, $communitySearch->buildQuery([], 'own-interest'));
        }
        $this->setBulletCount($count);
        
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-lightPrimary'
        ]));
    }
    
    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'My communities');
    }
}
