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