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
use open20\amos\community\models\search\CommunitySearch;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\widget\WidgetGraphic;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;

/**
 * Class WidgetGraphicsComments
 * @package open20\amos\community\widgets\graphics
 */
class WidgetGraphicsComments extends WidgetGraphic
{
    private $community, $refresh, $numberToView;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosCommunity::t('amoscommunity', 'Live chat'));
        $this->setDescription(AmosCommunity::t('amoscommunity', 'Messaggia con i partecipanti della community'));
        $communityId = CommunityUtil::getCommunityScope();
        if ($communityId) {
            $this->community = Community::findOne($communityId);
        }
        $this->setClassCustom('square-widget-graphic');
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return "<div class='widget-graphic-cms-bi-less widgets-icon widgets-chat-community'>" . \open20\amos\comments\widgets\CommentsWidget::widget([
            'model' => $this->community,
            'layoutInverted' => true,
            'chat' => true
        ]) . "</div> 
        <div class='placeholder-widget placeholder-widget-chat'>".$this->view->render('@vendor/open20/amos-community/src/widgets/graphics/views/_placeholder_widget')."</div>";
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

    /**
     *
     * @return integer
     */
    public function getRefresh()
    {
        return $this->refresh;
    }

    /**
     *
     * @param integer $refresh
     */
    public function setRefresh($refresh)
    {
        $this->refresh = $refresh;
    }

    /**
     *
     * @return integer
     */
    public function getNumberToView()
    {
        return $this->numberToView;
    }

    /**
     *
     * @param integer $numberToView
     */
    public function setNumberToView($numberToView)
    {
        $this->numberToView = $numberToView;
    }
}
