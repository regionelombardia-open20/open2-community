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

namespace open20\amos\community\models\clients\msmodels;

use yii\base\Model;

abstract class  AbstractMsTeams extends Model
{
    public $odataType;
    public $odataContext;
    public $odataCount;
    public $odataEtag;
    public $microsoftGraphDownloadUrl;


    public function __construct($config = [])
    {
        if (isset($config) && is_array($config)) {
            $this->mapData($config);
        }
//        parent::__construct($config);
    }

    /**
     * Map an array of data to the class properties.
     *
     * @param array $data
     */
    public function mapData(array $data)
    {
        foreach ($data as $key => $value) {
            $property = $this->mapKeyToProperty($key);
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Map special keys to class properties.
     *
     * @param string $key
     * @return string
     */
    protected function mapKeyToProperty($key)
    {
        $map = [
            '@odata.context' => 'odataContext',
            '@odata.type' => 'odataType',
            '@odata.count' => 'odataCount',
            '@odata.etag' => 'odataEtag',
            'calendar@odata.associationLink' => 'calendar_associationLink',
            'calendar@odata.navigationLink' => 'calendar_navigationLink',
            '@microsoft.graph.downloadUrl' => 'microsoftGraphDownloadUrl'
        ];

        return isset($map[$key]) ? $map[$key] : $key;
    }

}