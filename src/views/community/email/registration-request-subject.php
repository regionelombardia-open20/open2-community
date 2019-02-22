<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;

/** @var \lispa\amos\community\utilities\EmailUtil $util */

?>

<?= $util->userName. " ".  AmosCommunity::t('amoscommunity', "asked to register to"). " ". $util->community->name;?>