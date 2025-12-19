<?php

namespace open20\amos\community\models\clients\msmodels;

class MsOrganization extends AbstractMsTeams
{
    public $id;
    public $deletedDateTime;
    public $businessPhones = [];
    public $city;
    public $country;
    public $countryLetterCode;
    public $createdDateTime;
    public $defaultUsageLocation;
    public $displayName;
    public $isMultipleDataLocationsForServicesEnabled;
    public $marketingNotificationEmails = [];
    public $onPremisesLastSyncDateTime;
    public $onPremisesSyncEnabled;
    public $partnerTenantType;
    public $postalCode;
    public $preferredLanguage;
    public $securityComplianceNotificationMails = [];
    public $securityComplianceNotificationPhones = [];
    public $state;
    public $street;
    public $technicalNotificationMails = [];
    public $tenantType;
    public $directorySizeQuotaUsed;
    public $directorySizeQuotaTotal;
    public $assignedPlans = [];
    public $privacyProfileContactEmail;
    public $privacyProfileStatementUrl;
    public $provisionedPlans = [];
    public $verifiedDomains = [];

    public function getDomains($isDefault = true){
        $domains = [];
        foreach($this->verifiedDomains as $values){
            if(!empty($values['name'])){
                if($isDefault && $values['isDefault'] == 1 || !$isDefault){
                    $domains []= $values['name'];
                }

            }
        }
        return $domains;
    }

}