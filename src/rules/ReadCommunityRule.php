<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\partnershipprofiles\rules
 * @category   CategoryName
 */

namespace lispa\amos\community\rules;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\partnershipprofiles\models\ExpressionsOfInterest;
use lispa\amos\partnershipprofiles\models\PartnershipProfiles;

/**
 * Class UpdateOwnExprOfIntRule
 * @package lispa\amos\partnershipprofiles\rules
 */
class ReadCommunityRule extends \lispa\amos\core\rules\BasicContentRule
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
        } elseif($model->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED){
            $communityUserMm = CommunityUserMm::find()->andWhere(['community_id' => $model->id])->andWhere(['user_id' => $user])->one();
            if(!empty($communityUserMm)) {
                return true;
            }
        }
        return false;
    }
}
