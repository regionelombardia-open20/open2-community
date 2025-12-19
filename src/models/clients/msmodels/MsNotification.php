<?php

namespace open20\amos\community\models\clients\msmodels;

class MsNotification extends AbstractMsTeams
{
    public $tenantId;
    public $changeType;
    public $subscriptionId;
    public $resource;
    public $clientState;
    public $resourceData;
    public $subscriptionExpirationDateTime;


    public function getResourceId(){
        return $this->resourceData->id;
    }
}