<?php

namespace open20\amos\community\models\clients\msmodels;

use yii\base\Model;

class MsChannel extends AbstractMsTeams
{
    public $id;
    public $createdDateTime;
    public $displayName;

    public $description;
    public $isFavoriteByDefault;

    public $email;
    public $tenantId;
    public $webUrl;
    public $membershipType;

}