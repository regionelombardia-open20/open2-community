<?php

namespace open20\amos\community\models\clients\msmodels;


/**
 * Class AccountDetails
 *
 * @property string $accountName Account name
 * @property string $accountId Account ID
 * @property string $appliesTo Applies to (e.g., User, Company)
 * @property string $capabilityStatus Capability status (e.g., Enabled)
 * @property int $consumedUnits Consumed units
 * @property string $id Unique identifier
 * @property string $skuId SKU ID
 * @property string $skuPartNumber SKU part number
 * @property array $subscriptionIds List of subscription IDs
 * @property array $prepaidUnits Prepaid units details
 * @property array $servicePlans List of service plans
 */
class MsLicense extends AbstractMsTeams
{
    public $accountName;
    public $accountId;
    public $appliesTo;
    public $capabilityStatus;
    public $consumedUnits;
    public $id;
    public $skuId;
    public $skuPartNumber;
    public $subscriptionIds;
    public $prepaidUnits;
    public $servicePlans;


}