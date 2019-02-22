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
use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\dashboard\models\AmosUserDashboards;
use lispa\amos\dashboard\models\AmosUserDashboardsWidgetMm;
use lispa\amos\dashboard\models\AmosWidgets;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCommunityDashboard
 * @package lispa\amos\community\widgets\icons
 */
class WidgetIconCommunityDashboard extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
            if (!empty($moduleCwh->getCwhScope())) {
                $scope = $moduleCwh->getCwhScope();
                if (isset($scope['community'])) {
                    $community = Community::findOne($scope['community']);
                    if (!is_null($community) && ($community->context == Community::className())) {
                        return parent::isVisible();
                    }
                    return false;
                }
            }
        }
        return parent::isVisible();
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Community'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Community module'));
        
        $this->setIcon('group');
        
        $url = ['/community'];
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $url = ['/community/subcommunities/my-communities', 'id' => $scope['community']];
                $this->setLabel(AmosCommunity::tHtml('amoscommunity', '#widget_subcommunities_title'));
                $this->setDescription(AmosCommunity::t('amoscommunity', '#widget_subcommunities_description'));
            }
        }
        $this->setUrl($url);
        
        $this->setCode('COMMUNITY_MODULE');
        $this->setModuleName('community-dashboard');
        $this->setNamespace(__CLASS__);
        $this->setBulletCount($this->getBulletCountChildWidgets());
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
        return AmosCommunity::t('amoscommunity', 'Community dashboard');
    }
    
    /* TEMPORANEA */
    
    public function getOptions()
    {
        $options = parent::getOptions();
        
        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }
    
    public function getWidgetsIcon()
    {
        $widgets = [];
        
        $WidgetIconNewsCategorie = new WidgetIconCommunity();
        if ($WidgetIconNewsCategorie->isVisible()) {
            $widgets[] = $WidgetIconNewsCategorie->getOptions();
        }
        
        $WidgetIconNewsCreatedBy = new WidgetIconTipologiaCommunity();
        if ($WidgetIconNewsCreatedBy->isVisible()) {
            $widgets[] = $WidgetIconNewsCreatedBy->getOptions();
        }
        
        return $widgets;
    }
    
    /**
     * @return int - the sum of bulletCount internal widget
     * @throws \yii\base\InvalidConfigException
     */
    private function getBulletCountChildWidgets()
    {
        /** @var AmosUserDashboards $userModuleDashboard */
        $userModuleDashboard = AmosUserDashboards::findOne([
            'user_id' => \Yii::$app->user->id,
            'module' => AmosCommunity::getModuleName()
        ]);
        if (is_null($userModuleDashboard)) return 0;
        
        $listWidgetChild = $userModuleDashboard->amosUserDashboardsWidgetMms;
        if (is_null($listWidgetChild)) return 0;
        
        $count = 0;
        /** @var AmosUserDashboardsWidgetMm $widgetChild */
        foreach ($listWidgetChild as $widgetChild) {
            if ($widgetChild->amos_widgets_classname != $this->getNamespace()) {
                $amosWidget = AmosWidgets::findOne(['classname' => $widgetChild->amos_widgets_classname]);
                if (!empty($amosWidget)) {
                    if ($amosWidget->type == AmosWidgets::TYPE_ICON) {
                        $widget = \Yii::createObject($widgetChild->amos_widgets_classname);
                        $val = $widget->getBulletCount();
                        
                        $bulletCount = empty($val) ? 0 : $val;
                        $count = $count + $bulletCount;
                    }
                }
            }
        }
        return $count;
    }
}
