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
use lispa\amos\core\widget\WidgetIcon;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconAdminAllCommunity
 * @package lispa\amos\community\widgets\icons
 */
class WidgetIconAdminAllCommunity extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Administrate communities'));
        $this->setDescription(AmosCommunity::t('amoscommunity', "Allow user to edit the community entity"));
        
        $this->setIcon('group');
        $url = ['/community/community/admin-all-communities'];
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $url = ['/community/subcommunities/admin-all-communities', 'id' => $scope['community']];
            }
        }
        $this->setUrl($url);
        $this->setCode('ADMIN-ALL-COMMUNITY');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);
        
        /*$communitySearch = new CommunitySearch();
        $notifier = \Yii::$app->getModule('notify');
        $count = 0;
        if($notifier)
        {
            $count = $notifier->countNotRead(Yii::$app->getUser()->id, Community::class, $communitySearch->buildQuery('all',[]));
        }
        $this->setBulletCount($count);
        */
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-lightPrimary'
        ]));
    }
    
    /**
     * @return string
     */
    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'All communities');
    }
}
