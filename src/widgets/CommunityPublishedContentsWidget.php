<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\core\forms
 * @category   CategoryName
 */

namespace open20\amos\community\widgets;

use open20\amos\community\models\Community;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\news\models\News;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use open20\amos\core\widget\WidgetAbstract;

/**
 * Class PublishedContentsWidget
 * @package open20\amos\core\forms
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


    public $frameworkIcons = AmosIcons::DASH;

    public $iconsContents = [
        'open20\amos\news\models\News' => 'feed',
        'open20\amos\events\models\Event' => 'feed',
        'open20\amos\discussioni\models\DiscussioniTopic' => 'comment',
        'open20\amos\documenti\models\Documenti' => 'file-text-o',
        'open20\amos\partnershipprofiles\models\PartnershipProfiles' => 'lightbulb-o',
        'open20\amos\risultati\models\Risultati' => 'gears',
        'open20\amos\showcaseprojects\models\ShowcaseProjectProposal' => 'gears',
        'open20\amos\sondaggi\models\Sondaggi' => 'sondaggi'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {

        if(!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS){
            $this->frameworkIcons = AmosIcons::IC;

            $this->iconsContents = [
                'open20\amos\news\models\News' => 'news',
                'open20\amos\events\models\Event' => 'eventi',
                'open20\amos\discussioni\models\DiscussioniTopic' => 'disc',
                'open20\amos\documenti\models\Documenti' => 'fatture',
                'open20\amos\partnershipprofiles\models\PartnershipProfiles' => 'propostecollaborazione',
                'amos\results\models\Result' => 'risultati',
                'open20\amos\showcaseprojects\models\Initiative' => 'iniziative',
                'open20\amos\sondaggi\models\Sondaggi' => 'sondaggi',
            ];
        }

        /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
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

        /** @var \open20\amos\cwh\query\CwhActiveQuery $cwhActiveQuery */
        $cwhActiveQuery = null;

        if (isset($moduleCwh)) {
            $query = new \open20\amos\cwh\query\CwhActiveQuery($this->modelContent);
            $query->filterByPublicationNetwork(Community::getCwhConfigId(), $this->modelCommunity->id);
            $count = $query->count();

            if(empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] != WidgetAbstract::ENGINE_ROWS){
                $count = '(' . $count . ')';
            }
        }

        $icons = !empty($this->iconsContents[$this->modelContent]) ? AmosIcons::show($this->iconsContents[$this->modelContent], [], $this->frameworkIcons) : '';
        return  Html::tag('div',
                $icons .
                Html::tag('span', $count, ['class' => 'counter']) .
                Html::tag('span', $this->modelLabel, ['class' => 'model-label']),
                ['class' => 'content-widget-item']);
    }


}