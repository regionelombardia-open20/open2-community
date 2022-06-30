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
use open20\amos\dashboard\models\AmosUserDashboards;
use open20\amos\dashboard\models\AmosUserDashboardsWidgetMm;
use open20\amos\dashboard\models\AmosWidgets;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCommunityDashboard
 * @package open20\amos\community\widgets\icons
 */
class WidgetIconCommunityDashboard extends WidgetIcon
{
    /*
     * to avoid multiple calling
     */
    protected static $_called = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Community'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Community module'));

        $paramsClassSpan = [
            'bk-backgroundIcon',
        ];

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('community');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('group');
        }

        $url = ['/community'];
        $scopeId = $this->checkScope('community');
        if ($scopeId !== false) {
            $url = ['/community/subcommunities/my-communities', 'id' => $scopeId];
            $this->setLabel(AmosCommunity::tHtml('amoscommunity', '#widget_subcommunities_title'));
            $this->setDescription(AmosCommunity::t('amoscommunity', '#widget_subcommunities_description'));
        }
        $this->setUrl($url);

        $this->setCode('COMMUNITY_MODULE');
        $this->setModuleName('community-dashboard');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );

        // To avoid multiple call
        if (self::$_called === false) {
            self::$_called = true;
            return ;
        }

        if ($this->disableBulletCounters == false) {
            $this->setBulletCount(
                $this->makeBulletCounter(
                    Yii::$app->getUser()->getId()
                )
            );
        }
    }

    /**
     * 
     * @param type $userId
     * @param type $className
     * @param type $externalQuery
     * @return type
     */
    public function makeBulletCounter($userId = null, $className = null, $externalQuery = null)
    {
        return $this->getBulletCountChildWidgets($userId);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * 
     * @param type $userId
     * @return int - the sum of bulletCount internal widget
     */
    private function getBulletCountChildWidgets($userId = null)
    {
        /** @var AmosUserDashboards $userModuleDashboard */
        $userModuleDashboard = AmosUserDashboards::findOne([
            'user_id' => $userId,
            'module' => AmosCommunity::getModuleName()
        ]);

        if (is_null($userModuleDashboard)) {
            return 0;
        }

        $listWidgetChild = $userModuleDashboard->amosUserDashboardsWidgetMms;
        if (is_null($listWidgetChild)) {
            return 0;
        }

        /** @var AmosUserDashboardsWidgetMm $widgetChild */
//        $nameSpace = $this->getNamespace();
//        $tmp = [];
//        foreach ($listWidgetChild as $widgetChild) {
//            if ($widgetChild->amos_widgets_classname != $nameSpace) {
//                $tmp[] = $widgetChild->amos_widgets_classname;
//            }
//        }
//
//        $query = new Query();
//        $amosWidgets = $query
//            ->select([
//                'id', 'classname', 'deleted_at'
//            ])
//            ->from(AmosWidgets::tableName())
//            ->andWhere([
//                'classname' => $tmp,
//                'type' => AmosWidgets::TYPE_ICON,
//                'deleted_at' => null,
//            ])
//            ->all();
//
//        $count = 0;
//        foreach ($amosWidgets as $k => $amosWidget) {
//            $widget = \Yii::createObject($amosWidget['classname']);
//
//            $count += (int) $widget->getBulletCount();
//        }
        $widgetAllCommunity = new WidgetIconCommunity();

        return $widgetAllCommunity->getBulletCount();
    }

    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');

        if (isset($moduleCwh)) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
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
     * @return string
     */
    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'Community dashboard');
    }

    /**
     * Aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
     * 
     * @return type
     */
    public function getOptions()
    {
        return ArrayHelper::merge(
                parent::getOptions(),
                ['children' => $this->getWidgetsIcon()]
        );
    }

    /**
     * 
     * @return type
     */
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

}
