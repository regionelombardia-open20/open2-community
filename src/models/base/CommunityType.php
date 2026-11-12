<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
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

use open20\amos\community\AmosCommunity;

/**
 * This is the base-model class for table "community_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 */
class CommunityType extends \open20\amos\core\record\Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'community_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosCommunity::t('amoscommunity', 'ID'),
            'name' => AmosCommunity::t('amoscommunity', 'Name'),
            'description' => AmosCommunity::t('amoscommunity', 'Description'),
            'created_at' => AmosCommunity::t('amoscommunity', 'Created at'),
            'updated_at' => AmosCommunity::t('amoscommunity', 'Updated at'),
            'deleted_at' => AmosCommunity::t('amoscommunity', 'Deleted at'),
            'created_by' => AmosCommunity::t('amoscommunity', 'Created by'),
            'updated_by' => AmosCommunity::t('amoscommunity', 'Updated by'),
            'deleted_by' => AmosCommunity::t('amoscommunity', 'Deleted by'),
        ];
    }

}
