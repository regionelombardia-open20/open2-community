<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community
 * @category   CategoryName
 */

namespace open20\amos\community\models;
use open20\amos\community\models\clients\MsGraphBuilder;
use open20\amos\community\models\clients\msmodels\MsTeamMember;
use yii\db\ActiveQuery;

/**
 * Class CommunityUserMm
 * This is the model class for table "community_user_mm".
 * @package backend\modules\corsi\models
 */
class CommunityUserMm extends \open20\amos\community\models\base\CommunityUserMm
{

    /**
     * Gets the list of community manager email address for a specific community
     *
     * @param integer $communityId The Id of community to search community/user relations
     * @return array $emailArray The array containing community manager email
     */
    public function getCommunityManagerMailList($communityId){

        /** @var ActiveQuery $queryManagers */
        $community = Community::findOne($communityId);
        $callingModule = \Yii::createObject($community->context);
        $managerRole = $callingModule->getManagerRole();
        $queryManagers = CommunityUserMm::find()->andWhere([
            'community_id' => $communityId,
            'role' => $managerRole
        ]);
        $queryManagers->innerJoin('user', 'user_id = user.id');
        $queryManagers->select('user.email');
        $emailArray = $queryManagers->column();
        return $emailArray;
    }

    /**
     * @param $role
     * @return mixed|string
     */
    public static function mapRoleMsTeams($role){
        $roles = [
            CommunityUserMm::ROLE_COMMUNITY_MANAGER => MsTeamMember::ROLE_OWNER,
            CommunityUserMm::ROLE_PARTICIPANT => MsTeamMember::ROLE_MEMBER,
        ];
        if(!empty($roles[$role])){
            return $roles[$role];
        }
        return MsTeamMember::ROLE_MEMBER;
    }

    /**
     * @param $community
     * @param $userCommunity CommunityUserMm
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function updateMsChannelMemberRole($community, $userCommunity){
        $parent = Community::getCommunityRoot($community);
        if($parent->enable_msgraph_teams) {
            if($userCommunity->msgraph_membership_id) {
                $msBuilder = new MsGraphBuilder();
                if ($userCommunity->role == CommunityUserMm::ROLE_COMMUNITY_MANAGER) {
                    $msBuilder->assignLicensetouser($userCommunity->user_id);
                }

                $channelId = $community->msgraph_channel_id;
                $teamId = $parent->msgraph_team_id;
                $msRole = CommunityUserMm::mapRoleMsTeams($userCommunity->role);
                $msBuilder->updateChannelMemberRole($teamId, $channelId, $userCommunity, $msRole);
            }
        }
    }
}
