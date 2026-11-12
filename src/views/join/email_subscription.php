<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community\views\join
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

use open20\amos\community\AmosCommunity;

/**
 * @var \open20\amos\events\models\Event $event
 * @var \open20\amos\core\user\User $user
 * @var \open20\amos\admin\models\UserProfile $profile
 */

$message = AmosCommunity::t('amosevents', 'Gentile {name_surname}', ['name_surname' => $profile->getNomeCognome()]) . ', <br>' . AmosCommunity::t('amosevents', 'sei iscritto all\'evento di Blu Sea Land 2020:');
$messageAfter = AmosCommunity::t('amosevents', 'Riceverai notifiche riguradanti questo evento') .'<br>'. AmosCommunity::t('amosevents', 'Grazie e a presto.');
?>

<p><?= $message ?></p>
<p><b><?= $event->title ?></b><br><?= $event->summary ?></p>
<p><?= $messageAfter ?></p>