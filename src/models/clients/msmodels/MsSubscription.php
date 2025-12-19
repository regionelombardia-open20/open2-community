<?php

namespace open20\amos\community\models\clients\msmodels;

class MsSubscription extends AbstractMsTeams
{
    public $id;
    public $resource;
    public $applicationId;
    public $changeType;
    public $clientState;
    public $notificationUrl;
    public $notificationQueryOptions;
    public $lifecycleNotificationUrl;
    public $expirationDateTime;
    public $creatorId;
    public $includeResourceData;
    public $latestSupportedTlsVersion;
    public $encryptionCertificate;
    public $encryptionCertificateId;
    public $notificationUrlAppId;

}