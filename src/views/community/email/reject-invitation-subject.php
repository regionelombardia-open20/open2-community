<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\views\community\email
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;

/**
 * @var \lispa\amos\community\utilities\EmailUtil $util
 */

?>
    <?= AmosCommunity::t('amoscommunity', "Invitation to"). " ".$util->community->name . " ".  AmosCommunity::t('amoscommunity', "has been rejected by"). " ". $util->userName ;?>