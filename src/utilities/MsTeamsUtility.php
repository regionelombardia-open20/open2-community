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