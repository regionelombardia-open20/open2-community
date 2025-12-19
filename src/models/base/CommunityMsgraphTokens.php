<?php

namespace open20\amos\community\models\base;

use Yii;

/**
 * This is the base-model class for table "community_msgraph_tokens".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $msgraph_user_id
 * @property string $access_token
 * @property string $refresh_token
 * @property string $expire_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  CommunityMsgraphTokens extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'community_msgraph_tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['msgraph_user_id', 'access_token', 'refresh_token', 'expire_date'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amoscommunity', 'ID'),
            'user_id' => Yii::t('amoscommunity', 'Titolo del link'),
            'access_token' => Yii::t('amoscommunity', 'access token'),
            'refresh_token' => Yii::t('amoscommunity', 'refresh token'),
            'expire_date' => Yii::t('amoscommunity', 'expire date'),
            'created_at' => Yii::t('amoscommunity', 'Created at'),
            'updated_at' => Yii::t('amoscommunity', 'Updated at'),
            'deleted_at' => Yii::t('amoscommunity', 'Deleted at'),
            'created_by' => Yii::t('amoscommunity', 'Created by'),
            'updated_by' => Yii::t('amoscommunity', 'Updated by'),
            'deleted_by' => Yii::t('amoscommunity', 'Deleted by'),
        ];
    }
    public function getUser()
    {
        return $this->hasOne(\open20\amos\core\user\User::className(), ['id' => 'user_id']);
    }

}
