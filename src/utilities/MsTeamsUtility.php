<?php

namespace open20\amos\community\utilities;

use open20\amos\community\models\clients\MsGraphBuilder;
use open20\amos\community\models\Community;
use Yii;

/**
 * Utility class for MsTeams
 */
class MsTeamsUtility
{
    /**
     * @return void
     */
    public static function registerWebHooks()
    {
        $msBuilder = new MsGraphBuilder();
        $subscription = null;
        $subscription = $msBuilder->subscribeToWebHookMessages();
        $subscription = $msBuilder->subscribeToWebHookChannels();
        $subscription = $msBuilder->subscribeToWebHookTeams();

        $communities = Community::find()
            ->andWhere(['enable_msgraph_teams' => true])
            ->andWhere(['parent_id' => null])
            ->asArray()->all();

        /** @var  $community Community */
        foreach ($communities as $community) {
            if ($community['msgraph_team_id']) {
                $subscription = $msBuilder->subscribeToWebHookChannelMember($community['msgraph_team_id']);
                $subscription = $msBuilder->subscribeToWebHookTeamMember($community['msgraph_team_id']);
                $subscription = $msBuilder->subscribeToWebHookDriveFolder($community['msgraph_team_id'], $community['msgraph_channel_id']);
            }
        }

        if (!$subscription) {
            $subscriptions = $msBuilder->subscriptionList();
            foreach ($subscriptions as $sub) {
//                if($sub->notificationUrl == 'https://front-poi-dev2.devel.demotestwip.it/community/ms-teams/change-channel-members') {
//                    $resp = $msBuilder->unsubscribeWebHook($sub->id);
//                }//
//                if($sub->notificationUrl == 'https://front-poi-dev2.devel.demotestwip.it/community/ms-teams/change-team-members') {
//                    $resp = $msBuilder->unsubscribeWebHook($sub->id);
//                }
                $resp = $msBuilder->reauthorizeWebHook($sub->id);
                $resp = $msBuilder->renewSubscriptionWebHook($sub->id);
            }
        }
die;

        if (!$subscription) {
            Yii::$app->response->redirect(['/'])->send();
        }
        die;
    }
}