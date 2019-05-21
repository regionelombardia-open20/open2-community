<?php

namespace lispa\amos\community\models\base;

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
 * @property \lispa\amos\community\models\Community $community
 * @property \lispa\amos\dashboard\models\AmosWidgets $amosWidgets
 */
class  CommunityAmosWidgetsMm extends \lispa\amos\core\record\Record
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
            [['community_id'], 'exist', 'skipOnError' => true, 'targetClass' => Community::className(), 'targetAttribute' => ['community_id' => 'id']],
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
        return $this->hasOne(\lispa\amos\community\models\Community::className(), ['id' => 'community_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmosWidgets()
    {
        return $this->hasOne(\lispa\amos\dashboard\models\AmosWidgets::className(), ['id' => 'amos_widgets_id']);
    }
}
