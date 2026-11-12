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

namespace open20\amos\community\models\base;

use Yii;

/**
 * This is the base-model class for table "community_amos_widgets_mm".
 *
 * @property integer $id
 * @property integer $community_id
 * @property integer $amos_widgets_id
 * @property string $widget_label
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\community\models\Community $community
 * @property \open20\amos\dashboard\models\AmosWidgets $amosWidgets
 */
class  CommunityAmosWidgetsMm extends \open20\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'community_amos_widgets_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['community_id', 'amos_widgets_id'], 'required'],
            [['community_id', 'amos_widgets_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['widget_label'], 'string', 'max' => 255],
            [['community_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\community\models\Community::className(), 'targetAttribute' => ['community_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amoscommunity', 'ID'),
            'community_id' => Yii::t('amoscommunity', 'Community'),
            'amos_widgets_id' => Yii::t('amoscommunity', 'Widget'),
            'widget_label' => Yii::t('amoscommunity', 'Label widget'),
            'created_at' => Yii::t('amoscommunity', 'Created at'),
            'updated_at' => Yii::t('amoscommunity', 'Updated at'),
            'deleted_at' => Yii::t('amoscommunity', 'Deleted at'),
            'created_by' => Yii::t('amoscommunity', 'Created by'),
            'updated_by' => Yii::t('amoscommunity', 'Updated by'),
            'deleted_by' => Yii::t('amoscommunity', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunity()
    {
        return $this->hasOne(\open20\amos\community\models\Community::className(), ['id' => 'community_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmosWidgets()
    {
        return $this->hasOne(\open20\amos\dashboard\models\AmosWidgets::className(), ['id' => 'amos_widgets_id']);
    }
}
