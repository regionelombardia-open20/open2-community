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