<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\forms
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets;

use lispa\amos\community\models\Community;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\news\models\News;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * Class PublishedContentsWidget
 * @package lispa\amos\core\forms
 */
class CommunityPublishedContentsWidget extends Widget
{

    /**
     * @var string classname of the model of the listed objects
     */
    public $modelCommunity;
    /**
     * @var string classname of the model of the listed objects
     */
    public $modelContent;

    /**
     * @var string label for the model of the listed objects
     */
    public $modelLabel;


    public $iconsContents = [
        'lispa\amos\news\models\News' => 'feed',
        'lispa\amos\discussioni\models\DiscussioniTopic' => 'comment',
        'lispa\amos\documenti\models\Documenti' => 'file-text-o',
        'lispa\amos\partnershipprofiles\models\PartnershipProfiles' => 'lightbulb-o',
        'lispa\amos\risultati\models\Risultati' => 'gears',
        'lispa\amos\showcaseprojects\models\ShowcaseProjectProposal' => 'gears',
    ];


    /**
     * @inheritdoc
     */
    public function init()
    {
        /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
        $object = Yii::createObject($this->modelContent);
        $this->modelLabel = $object->getGrammar()->getModelLabel();

    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        $count = 0;

        /** @var \lispa\amos\cwh\query\CwhActiveQuery $cwhActiveQuery */
        $cwhActiveQuery = null;

        if (isset($moduleCwh)) {
            $query = new \lispa\amos\cwh\query\CwhActiveQuery($this->modelContent);
            $query->filterByPublicationNetwork(Community::getCwhConfigId(), $this->modelCommunity->id);
            $count = $query->count();
            $count = '(' . $count . ')';
        }

        $icons = !empty($this->iconsContents[$this->modelContent]) ? AmosIcons::show($this->iconsContents[$this->modelContent], [], 'dash') : '';
        return  Html::tag('div',
                $icons .
                Html::tag('span', $count, ['class' => 'counter']) .
                Html::tag('span', $this->modelLabel, ['class' => 'model-label']),
                ['class' => 'content-widget-item']);
    }


}