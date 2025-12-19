<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\widgets\graphics
 * @category   CategoryName
 */

namespace open20\amos\community\widgets\graphics;

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\core\widget\WidgetGraphic;
use open20\amos\community\models\search\BookmarksSearch;
use open20\amos\community\utilities\CommunityUtil;

/**
 * Class WidgetGraphicsComments
 * @package open20\amos\community\widgets\graphics
 */
class WidgetGraphicsLinks extends WidgetGraphic
{
    private $community;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setDefaultValues();
        $this->setLabel(AmosCommunity::t('amoscommunity', 'Bookmark'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Aggiungi e condividi dei link'));
        $communityId = CommunityUtil::getCommunityScope();
        if($communityId){
            $this->community = Community::findOne($communityId);
        }
        $this->setClassCustom('square-widget-graphic');

        #$this->setDefaultValues();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $viewPath     = '@vendor/open20/amos-community/src/widgets/graphics/views/';
        $viewToRender = $viewPath.'links';

        if($_GET){
            $this->setCommunity(Community::findOne($_GET));
        }
        $communityLinks = $this->community->getSomeLinks();

        $isMemberActive = CommunityUtil::userIsCommunityMemberActive($this->community->id);

        $modelSearch = new BookmarksSearch();
        $dataProvider = $modelSearch->search([]);
        $dataProvider->query->limit(5);
        return $this->render(
            $viewToRender,
            [
                'model' => $this->community,
                'data' => $communityLinks,
                'dataP' => $dataProvider,
                'isMemberActive' => $isMemberActive,
                'limit' => $dataProvider->query->limit
            ]
        ) . "<div class='placeholder-widget'>".$this->view->render('@vendor/open20/amos-community/src/widgets/graphics/views/_placeholder_widget')."</div>";
    }

    /**
     *
     * @return integer
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     *
     * @param integer $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;
    }

    public function setDefaultValues()
    {
        $this->setCode('COMMENTSCOMMUNITY_GRAPHIC');
        $this->setLabel(AmosCommunity::t('amoscommunity', 'CLinks'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'View the list of links'));
        if (empty($this->getCommunity())) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (!empty($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope['community'])) {
                    $this->setCommunity($scope['community']);
                }
            }
        }
        $moduleCommunity = \Yii::$app->getModule(AmosCommunity::getModuleName());
    }

    public function isVisible()
    {
        return true;
    }
}