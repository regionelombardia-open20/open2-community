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
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\icons\AmosIcons;

use yii\db\Query;
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
    public function init()
    {
        parent::init();

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'Community'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Community module'));

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
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

        $this->setBulletCount(
            $this->makeBulletCounter(\Yii::$app->user->id)
        );
    }

    /**
     * 
     * @param type $user_id
     * @return type
     */
    public function makeBulletCounter($user_id = null)
    {
        return $this->getBulletCountChildWidgets($user_id);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * 
     * @param type $user_id
     * @return int - the sum of bulletCount internal widget
     */
    private function getBulletCountChildWidgets($user_id = null)
    {
        /** @var AmosUserDashboards $userModuleDashboard */
        $userModuleDashboard = AmosUserDashboards::findOne([
            'user_id' => $user_id,
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
        $nameSpace = $this->getNamespace();
        $tmp = [];
        foreach ($listWidgetChild as $widgetChild) {
            if ($widgetChild->amos_widgets_classname != $nameSpace) {
                $tmp[] = $widgetChild->amos_widgets_classname;
            }
        }

        $query = new Query();
        $query
            ->select([
                'id', 'classname', 'type', 'module', 'status', 'child_of',
                'dashboard_visible', 'deleted_at'
            ])
            ->from(AmosWidgets::tableName())
            ->andWhere([
                'classname' => $tmp,
                'type' => AmosWidgets::TYPE_ICON,
            ]);
        
        $amosWidgets = $query->all();

        $count = 0;
        foreach ($amosWidgets as $k => $amosWidget) {
            $widget = \Yii::createObject($amosWidget['classname']);
            
            $count += (int)$widget->getBulletCount();
        }

        return $count;
    }

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
