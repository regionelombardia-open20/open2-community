<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
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

/** @var $dataProvider \yii\data\ActiveDataProvider*/

use open20\amos\community\AmosCommunity;
use open20\amos\admin\widgets\UserCardWidget;
use open20\amos\core\helpers\Html;



\yii\bootstrap\Modal::begin([
    'id' => 'view-all-members',
    'header' => "<h3>" .AmosCommunity::t('amoscommunity', 'Participants') . "</h3>",
    'size' => 'modal-lg',
]);
    \yii\widgets\Pjax::begin([
        'id' => 'pjax-container-view-all',
        'timeout' => 2000,
        'enablePushState' => false,
        'enableReplaceState' => false,
        'clientOptions' => ['data-pjax-container' => 'grid-view-all-members', 'method' => 'POST' ]
    ]);

    echo \open20\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'grid-view-all-members',
        'columns' => [
            'Photo' => [
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Photo'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Photo'),
                ],
                'label' => AmosCommunity::t('amoscommunity', 'Photo'),
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \open20\amos\admin\models\UserProfile $userProfile */
                    $userProfile = $model->user->getProfile();
                    return UserCardWidget::widget(['model' => $userProfile]);
                }
            ],
            'name' => [
                'attribute' => 'user.userProfile.surnameName',
                'label' => AmosCommunity::t('amoscommunity', 'Name'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'name'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'name'),
                ],
                'value' => function($model){
                    $adminModuleName = \open20\amos\admin\AmosAdmin::getModuleName();
                    return Html::a($model->user->userProfile->surnameName, ['/'.$adminModuleName.'/user-profile/view', 'id' => $model->user->userProfile->id ], [
                        'title' => AmosCommunity::t('amoscommunity', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->user->userProfile->surnameName])
                    ]);
                },
                'format' => 'html'
            ],
            'status' => [
                'attribute' => 'status',
                'label' => AmosCommunity::t('amoscommunity', 'Status'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Status'),
                ],
                'value' => function($model){
                    return $model->status;
                }
            ],
            'role' => [
                'attribute' => 'role',
                'label' => AmosCommunity::t('amoscommunity', 'Role'),
                'headerOptions' => [
                    'id' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'contentOptions' => [
                    'headers' => AmosCommunity::t('amoscommunity', 'Role'),
                ],
                'value' => function($model){
                    return $model->role;
                }
            ],
        ]
    ]);
    \yii\widgets\Pjax::end();


\yii\bootstrap\Modal::end();
