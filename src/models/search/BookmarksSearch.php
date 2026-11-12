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

namespace open20\amos\community\models\search;

use open20\amos\community\utilities\CommunityUtil;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use open20\amos\community\models\Bookmarks;

/**
 * BookmarksSearch represents the model behind the search form about `open20\amos\community\models\Bookmarks`.
 */
class BookmarksSearch extends \open20\amos\community\models\Bookmarks
{

//private $container; 

    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['id', 'community_id', 'creatore_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['titolo', 'link', 'data_pubblicazione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['Community', 'safe'],
            ['User', 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params, $queryType = null, $limit = null, $onlyDrafts = false, $pageSize = null)
    {
        //init the default search values
        #$this->initOrderVars();

        //check params to get orders value
        #$this->setOrderVars($params);

        $query = Bookmarks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('community');
        $query->joinWith('user');

        if (isset($params['id'])) {
            $query->andWhere(['community_id' => $params['id']]);
        }

        if (isset($params['BookmarksSearch']['orderAttribute']) && $params['BookmarksSearch']['orderAttribute'] !== '') {
            $query->orderBy([$params['BookmarksSearch']['orderAttribute'] => (int)$params['BookmarksSearch']['orderType']]);
        }

        // Se l'utente non ha accesso ai permessi di bookmark admin
        if(!Yii::$app->user->can('BOOKMARKS_ADMIN') && !CommunityUtil::loggedUserIsCommunityManager($params['id'])){
            $query->andFilterWhere(['or',
                ['bookmarks.status' => [Bookmarks::BOOKMARKS_STATUS_PUBLISHED]],
                ['creatore_id' => Yii::$app->user->id]
                ]);
        }

        $dataProvider->setSort([
            'attributes' => [
                'titolo' => [
                    'asc' => ['bookmarks.titolo' => SORT_ASC],
                    'desc' => ['bookmarks.titolo' => SORT_DESC],
                ],
                'data_pubblicazione' => [
                    'asc' => ['bookmarks.data_pubblicazione' => SORT_ASC],
                    'desc' => ['bookmarks.data_pubblicazione' => SORT_DESC],
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'data_pubblicazione' => $this->data_pubblicazione,
            'community_id' => $this->community_id,
            'creatore_id' => $this->creatore_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
            ->andFilterWhere(['like', 'link', $this->link]);

        return $dataProvider;
    }
}
