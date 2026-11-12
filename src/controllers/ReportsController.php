<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\controllers
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\community\controllers;

use open20\amos\community\exceptions\CommunityException;
use open20\amos\community\models\Community;
use open20\amos\community\utilities\ReportsUtility;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ReportsController
 * @package open20\amos\community\controllers
 */
class ReportsController extends Controller
{
    /**
     * @var ReportsUtility $reportUtility
     */
    private $reportUtility = null;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->reportUtility = new ReportsUtility();
    }
    
    /**
     * @inheritdoc
     * @return mixed
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'uploaded-files-size',
                            'contacts-list',
                            'documents-types',
                            'access-frequency',
                            'all-uploaded-files-size',
                            'all-contacts-list',
                            'all-documents-types',
                            'all-access-frequency',
                        ],
                        'roles' => ['AMMINISTRATORE_COMMUNITY']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ]
            ]
        ]);
        return $behaviors;
    }
    
    /**
     * @param int $id
     * @return string
     * @throws CommunityException
     * @throws NotFoundHttpException
     */
    public function actionUploadedFilesSize($id)
    {
        $community = $this->findModel($id);
        $excel = $this->reportUtility->uploadedFileSizesByCommunity($community);
        return $excel;
    }
    
    /**
     * @return string
     * @throws CommunityException
     */
    public function actionAllUploadedFilesSize()
    {
        $excel = $this->reportUtility->uploadedFileSizes();
        return $excel;
    }
    
    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionContactsList($id)
    {
        $community = $this->findModel($id);
        $models = $this->reportUtility->getCommunityAndSubcommunitiesUsersData($community);
        $columns = $this->reportUtility->getCommunityAndSubcommunitiesUsersColumns();
        $excel = $this->reportUtility->makeExcel($models, $columns);
        return $excel;
    }
    
    /**
     * @return string
     */
    public function actionAllContactsList()
    {
        $models = $this->reportUtility->getAllCommunityAndSubcommunitiesUsersData();
        $columns = $this->reportUtility->getCommunityAndSubcommunitiesUsersColumns();
        $excel = $this->reportUtility->makeExcel($models, $columns);
        return $excel;
    }
    
    /**
     * @param int $id
     * @return string
     * @throws CommunityException
     * @throws NotFoundHttpException
     */
    public function actionDocumentsTypes($id)
    {
        $community = $this->findModel($id);
        $excel = $this->reportUtility->uploadedFileTypesByCommunity($community);
        return $excel;
    }
    
    /**
     * @return string
     * @throws CommunityException
     */
    public function actionAllDocumentsTypes()
    {
        $excel = $this->reportUtility->uploadedFileTypes();
        return $excel;
    }
    
    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAccessFrequency($id)
    {
        $community = $this->findModel($id);
        $models = $this->reportUtility->getCommunityFrequencyData($community);
        $columns = $this->reportUtility->getCommunityFrequencyColumns();
        $excel = $this->reportUtility->makeExcel($models, $columns);
        return $excel;
    }
    
    /**
     * @return string
     */
    public function actionAllAccessFrequency()
    {
        $models = $this->reportUtility->getAllCommunityFrequencyData();
        $columns = $this->reportUtility->getCommunityFrequencyColumns();
        $excel = $this->reportUtility->makeExcel($models, $columns);
        return $excel;
    }
    
    /**
     * @param int $id
     * @return Community
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Community::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $model;
    }
}
