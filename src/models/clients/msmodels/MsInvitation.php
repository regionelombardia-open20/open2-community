<?php

namespace open20\amos\community\models\clients\msmodels;

class MsInvitation extends AbstractMsTeams
{
    public $odataContext;
    public $id;
    public $inviteRedeemUrl;
    public $invitedUserDisplayName;
    public $invitedUserType;
    public $invitedUserEmailAddress;
    public $sendInvitationMessage;
    public $resetRedemption;
    public $inviteRedirectUrl;
    public $status;
    public $invitedUserMessageInfo;
    public $invitedUser;


    public function getUserId(){
        if(!empty( $this->invitedUser['id'])) {
            return $this->invitedUser['id'];
        }
        return null;
    }

}