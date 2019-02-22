<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\models;

/**
 * Class CommunityType
 * This is the model class for table "community_types".
 * @package lispa\amos\community\models
 */
class CommunityType extends \lispa\amos\community\models\base\CommunityType
{
    /**
     * Constants for ID of the three community types
     */
    const COMMUNITY_TYPE_OPEN = 1;
    const COMMUNITY_TYPE_PRIVATE = 2;
    const COMMUNITY_TYPE_CLOSED = 3;
}
