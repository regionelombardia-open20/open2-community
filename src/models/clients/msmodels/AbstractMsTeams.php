<?php

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