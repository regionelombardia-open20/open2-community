<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\widgets
 * @category   CategoryName
 */

namespace open20\amos\community\widgets;

use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\helpers\Html;
use open20\amos\dashboard\models\AmosWidgets;
use open20\amos\dashboard\models\search\AmosWidgetsSearch;
use Yii;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;

/**
 * Class CommunityCardWidget
 * @package open20\amos\community\widgets
 */
class ConfigureDashboardCommunityWidget extends Widget
{
    /**
     * @var Community $model
     */
    public $model;
    public $hideParticipants = false;
    public $defaultWidgetSelected = [];
    public $disableTotalConfigurationInUpdate = false;


    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->model)) {
            throw new \Exception(AmosCommunity::t('amoscommunity', 'Missing model'));
        }
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function run()
    {
        $params = self::getDashBoardWidgets($this->model, $this->hideParticipants, $this->defaultWidgetSelected, $this->disableTotalConfigurationInUpdate);
        return $this->render('configure_dashboard_community', $params);
    }


    /**
     * @param $model
     * @param bool $hideParticipants
     * @param null $defaultWidgetSelected
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDashBoardWidgets($model, $hideParticipants = false, $defaultWidgetSelected = null, $disableTotalConfigurationInUpdate = false)
    {
        $canPersonalize = \Yii::$app->user->can('COMMUNITY_WIDGETS_ADMIN_PERSONALIZE');
        $util = new CommunityUtil();
        //non serve, il controllo del permesso viene fatto nell'action
//        if(!($util->isManagerLoggedUser($model) || \Yii::$app->user->can('ADMIN') || $model->isNewRecord)){
//            throw new ForbiddenHttpException('Accesso negato');
//        }


        $widgetSelected = [];
        $widgetIconsSelected = $model->amosWidgetsIcons;
        foreach ($widgetIconsSelected as $widget) {
            $widgetSelected[] = $widget->id;
        }
        $widgetGraphicsSelected = $model->amosWidgetsGraphics;
        foreach ($widgetGraphicsSelected as $widget) {
            $widgetSelected[] = $widget->id;
        }
        if (empty($widgetSelected) && !empty($defaultWidgetSelected)) {
            $widgetSelected = $defaultWidgetSelected;


        }
        $isSaved = $model->isSavedCommunityAmosDashoardMm();

        // --------- WIDGET ICONS
        if ($canPersonalize && $disableTotalConfigurationInUpdate == false) {
            $widgetIconSelectable = AmosWidgetsSearch::selectableIcon(0, null, true, true)->all();
        } else {
            $widgetIconSelectable = AmosWidgetsSearch::selectableIcon(1, 'community', true, true)->all();
        }
        //remove widget not visible
        $widgetIconsSelectableCopy = [];
        if (!$hideParticipants) {
            $widgetPartecipanti = AmosWidgets::findOne(['classname' => 'open20\amos\admin\widgets\icons\WidgetIconUserProfile']);
            $widgetIconsSelectableCopy = [$widgetPartecipanti];
        }
        foreach ($widgetIconSelectable as $key => $iconSelectable) {
            $obj = Yii::createObject($iconSelectable->classname);
            if ($obj->isVisible()) {
                $widgetIconsSelectableCopy[] = $iconSelectable;
            }
        }

        $widgetIconSelectable = $widgetIconsSelectableCopy;
        $providerIcon = new ArrayDataProvider(['allModels' => $widgetIconSelectable, 'pagination' => false]);


        // ----------  WIDGET GRAPHICS

        $order = 'default_order';
        if ($isSaved) {
            $order = 'n_order';
        }

        $forceAll = false;
        $widgetGraphicSelectableCopy = [];
        if ($canPersonalize && $disableTotalConfigurationInUpdate == false) {
            $forceAll = true;
            $widgetGraphicSelectable = AmosWidgetsSearch::selectableGraphic(0, null, $forceAll, true, $order, $model ? $model->id : null)->all();
        } else {
            $widgetGraphicSelectable = AmosWidgetsSearch::selectableGraphic(1, 'community', $forceAll, true, $order, $model ? $model->id : null)->all();
        }

        $query = AmosWidgetsSearch::selectable()
            ->andWhere(['module' => 'community'])
            ->andWhere(['type' => AmosWidgets::TYPE_GRAPHIC])
            ->andWhere(['status' => 1])
            ->orderBy('default_order ASC');

        if(!$forceAll){
            $query->andWhere(['sub_dashboard' => 1]);
        }
//        pr($query->createCommand()->rawSql);

        if ((\Yii::$app->getModule('dashboard')->useWidgetGraphicDashboardVisible == true) && $forceAll) {
//             $query->andWhere(['dashboard_visible' => 1]);
        }


//        pr(AmosWidgetsSearch::selectableGraphic(1, 'community', true, true, $order)->createCommand()->rawSql);
        //remove widget not visible
        $graphicsIds = [];
        foreach ($widgetGraphicSelectable as $key => $graphicSelectable) {
            $obj = Yii::createObject($graphicSelectable->classname);
            if ($obj->isVisible()) {
                $widgetGraphicSelectableCopy[] = $graphicSelectable;
                $graphicsIds [] = $graphicSelectable->id;
            }
        }
//        pr($graphicsIds);
//        pr($query->createCommand()->getRawSql());
        $allCommunityWidgetGraphics = $query->all();
        foreach ($allCommunityWidgetGraphics as $widgetGraph) {
            $obj = Yii::createObject($widgetGraph->classname);
            if ($obj->isVisible() && !in_array($widgetGraph->id, $graphicsIds)) {
                $widgetGraphicSelectableCopy[] = $widgetGraph;
//                pr($widgetGraph->classname);
//                pr( $widgetGraph->id);
            }
        }
//die;
        $widgetGraphicSelectable = $widgetGraphicSelectableCopy;
        $providerGraphic = new ArrayDataProvider([
            'allModels' => $widgetGraphicSelectable,
            'pagination' => false,
        ]);


        $params = [
            'widgetIconSelectable' => $widgetIconSelectable,
            'widgetGraphicSelectable' => $widgetGraphicSelectable,
            'widgetSelected' => $widgetSelected,
            'providerIcon' => $providerIcon,
            'providerGraphic' => $providerGraphic,
            'model' => $model,
            'isSaved' => $isSaved
        ];
        return $params;
    }


}
