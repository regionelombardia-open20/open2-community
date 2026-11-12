<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\partnershipprofiles\rules
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

namespace open20\amos\community\rules;
use open20\amos\admin\models\UserProfile;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityType;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\partnershipprofiles\models\ExpressionsOfInterest;
use open20\amos\partnershipprofiles\models\PartnershipProfiles;

/**
 * Class UpdateOwnExprOfIntRule
 * @package open20\amos\partnershipprofiles\rules
 */
class ReadCommunityRule extends \open20\amos\core\rules\BasicContentRule
{
    public $name = 'ReadCommunity';

    /**
     * Rule to Read Community
     * @inheritdoc
     */
    public function ruleLogic($user, $item, $params, $model)
    {
        /** @var Community $model */
        if($model->community_type_id == CommunityType::COMMUNITY_TYPE_OPEN || $model->community_type_id == CommunityType::COMMUNITY_TYPE_PRIVATE){
            return true;
        } else if($model->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED){
            $communityUserMm = CommunityUserMm::find()->andWhere(['community_id' => $model->id])->andWhere(['user_id' => $user])->one();
            if(!empty($communityUserMm)) {
                return true;
            }
        }
        return false;
    }
}
