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

<?= AmosCommunity::t('amoscommunity', "Welcome to"). " ". $util->community->name;?>
