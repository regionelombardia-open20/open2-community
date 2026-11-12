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

/**
 * @property string $odataContext
 * @property string $id
 * @property string $replyToId
 * @property string $etag
 * @property string $messageType
 * @property string $createdDateTime
 * @property string $lastModifiedDateTime
 * @property string $lastEditedDateTime
 * @property string $deletedDateTime
 * @property string $subject
 * @property string $summary
 * @property string $chatId
 * @property string $importance
 * @property string $locale
 * @property string $webUrl
 * @property array $policyViolation
 * @property array $eventDetail
 * @property array $from
 * @property array $body
 * @property array $channelIdentity
 * @property array $attachments
 * @property array $mentions
 * @property array $reactions
 */

namespace open20\amos\community\models\clients\msmodels;

use open20\amos\admin\models\UserProfile;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;

class MsChannelMessage extends AbstractMsTeams
{

    public $id;
    public $replyToId;
    public $etag;
    public $messageType;
    public $createdDateTime;
    public $lastModifiedDateTime;
    public $lastEditedDateTime;
    public $deletedDateTime;
    public $subject;
    public $summary;
    public $chatId;
    public $importance;
    public $locale;
    public $webUrl;
    public $policyViolation;
    public $eventDetail;
    public $from;
    public $body;
    public $channelIdentity;
    public $attachments;
    public $mentions;
    public $reactions;

    public function getCreatorMsUserId(){
        if(!empty($this->from['user']['id'])){
            return $this->from['user']['id'];
        }
        return null;
    }

    public function getCreatorUserProfile()
    {
        $msUserId = $this->getCreatorMsUserId();
        $token = CommunityMsgraphTokens::find()->andWhere(['msgraph_user_id' => $msUserId])->one();
        if($token){
            return $token->user->userProfile;
        }
        return null;
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getCreatedDate(){
        $createdDate = null;
        if(!empty($this->createdDateTime)){
            $createdDateTime = new \DateTime($this->createdDateTime, new \DateTimeZone('UTC'));
            $createdDateTime->setTimezone(new \DateTimeZone('Europe/Rome'));

            if($createdDateTime){
                $createdDate = $createdDateTime->format('Y-m-d H:i:s');
            }
        }
        return $createdDate;
    }

    public function getContentMessage(){
        if(!empty($this->body['content'])){
            return $this->body['content'];
        }
        return '';
    }
    public function getFormattedMessage(){
        $text= '';
        $message = $this->getContentMessage();
        $subject = $this->subject;
        $attachments = $this->getHtmlAttachments();
        if(!empty($subject)){
            $text.= "<div class='h6'>".$subject."</div>";
        }
        if(!empty($message)){
            $text.=$message;
        }
        if(!empty($attachments)){
            foreach($attachments as $file) {
                $text.=$file;
            }
        }
        return $text;

    }

    public function getHtmlAttachments(){
        $files = [];
        foreach($this->attachments as $attachment){
            $files[] = Html::a(AmosIcons::show('attachment-alt',['class'=>'m-r-5']).' '.$attachment['name'], $attachment['contentUrl'],[
                'title' => 'Scarica ' . $attachment['name'],
                'class' => 'label label-default m-r-5',
                'target' => '_blank'
            ]);
        }
        return $files;
    }
}