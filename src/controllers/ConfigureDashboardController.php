<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\controllers;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\assets\AmosCommunityAsset;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityAmosWidgetsMm;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\models\search\CommunitySearch;
use lispa\amos\community\rbac\UpdateOwnNetworkCommunity;
use lispa\amos\community\utilities\CommunityUtil;
use lispa\amos\community\widgets\ConfigureDashboardCommunityWidget;
use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\dashboard\AmosDashboard;
use lispa\amos\dashboard\models\AmosWidgets;
use lispa\amos\dashboard\models\search\AmosWidgetsSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ConfigureDashboardController extends CrudController
{
    /**
     * @var string $layout
     */
//    public $layout = 'main';
//    public $layout = 'room';

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->setModelObj(AmosCommunity::instance()->createModel('Community'));
        $this->setModelSearch(new CommunitySearch());

        $this->setAvailableViews([]);

        AmosCommunityAsset::register(Yii::$app->view);
        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosDashboard::t('amosdashboard',
                    '{iconaTabella}'.Html::tag('p', AmosDashboard::t('amosdashboard', 'Tabella')),
                    [
                        'iconaTabella' => AmosIcons::show('view-list-alt')
                    ]),
                'url' => '?currentView=grid'
            ],
            'icon' => [
                'name' => 'icon',
                'label' => AmosDashboard::t('amosdashboard',
                    '{iconaElenco}'.Html::tag('p', AmosDashboard::t('amosdashboard', 'Icone')),
                    [
                        'iconaElenco' => AmosIcons::show('grid')
                    ]),
                'url' => '?currentView=icon'
            ],
        ]);

        parent::init();

        if(!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->view->pluginIcon = 'dash dash-dashboard';
        }

        $this->setUpLayout();

    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function behaviors()
    {
        $behaviors =
                [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'index',
                            ],
                            'roles' => ['COMMUNITY_WIDGETS_CONFIGURATOR'],
                        ],
                    ]
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post', 'get']
                    ]
                ]
        ];
        return $behaviors;
    }

    public function actionIndex($layout = null, $id = null)
    {
        Url::remember();
        $this->setUpLayout('list');
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (is_null($id) && isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $id = $scope['community'];
            }
        }
        $model = $this->findModel($id);
        $params = ConfigureDashboardCommunityWidget::getDashBoardWidgets($model);
        if(\Yii::$app->request->isPost){
            $model->saveDashboardCommunity();
            return $this->redirect(['/community/configure-dashboard', 'id' => $id]);
        }

        $params ['currentView'] = $this->getCurrentView();
        $params ['availableViews'] = $this->getAvailableViews();

        return $this->render('index', $params);
    }

}