<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets\graphics;

use lispa\amos\core\widget\WidgetGraphic;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\search\CommunitySearch;
use lispa\amos\notificationmanager\base\NotifyWidgetDoNothing;

/**
 * Class WidgetGraphicsMyCommunities
 * @package lispa\amos\community\widgets\graphics
 */
class WidgetGraphicsMyCommunities extends WidgetGraphic
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setCode('MY_COMMUNITIES_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'My communities'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of my communities'));
    }

    public function getHtml()
    {
        $search = new CommunitySearch();
        $search->setNotifier(new NotifyWidgetDoNothing());

        $viewToRender = 'my_communities';
        $numberToView = 3;
        if(is_null(\Yii::$app->getModule('layout'))){
            $viewToRender = 'my_communities_old';
            $numberToView = 3;
        }

        /** show subcommunities if you are inside a community and if you have anabled show subcommunities */
        $moduleCommunity = \Yii::$app->getModule('community');
        $showSubscommunities = $moduleCommunity->showSubcommunities;
        $linkToSubcommunities = false;
        /** @var AmosCwh $moduleCwh */
        if($showSubscommunities) {
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope['community'])) {
                    $search->subcommunityMode = true;
                    $linkToSubcommunities = true;
                }
            }
        }

        $communitiesList = $search->searchMyCommunities($_GET, $numberToView);

        return $this->render($viewToRender, [
            'communitiesList' => $communitiesList,
            'widget' => $this,
            'toRefreshSectionId' => 'widgetGraphicMyCommunities',
            'linkToSubcommunities' => $linkToSubcommunities
        ]);
    }
}