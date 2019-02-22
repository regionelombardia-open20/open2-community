<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\models
 * @category   CategoryName
 */

namespace lispa\amos\community\models;

use yii\base\Model;

/**
 * Class CommunityReport
 * @package lispa\amos\community\models
 */
class CommunityReport extends Model
{
    /**
     * @var int $id
     */
    public $id;
    
    /**
     * @var string $title
     */
    public $title;
    
    /**
     * @var mixed $reportValue
     */
    public $reportValue;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'reportValue'], 'safe'],
            [['title'], 'string'],
            [['id'], 'integer'],
        ];
    }
}
