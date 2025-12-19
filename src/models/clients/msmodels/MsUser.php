<?php

namespace open20\amos\community\models\clients\msmodels;

class MsUser extends AbstractMsTeams
{
    public $odataContext;
    public $businessPhones = [];
    public $displayName;
    public $givenName;
    public $jobTitle;
    public $mail;
    public $mobilePhone;
    public $officeLocation;
    public $preferredLanguage;
    public $surname;
    public $userPrincipalName;
    public $id;

}