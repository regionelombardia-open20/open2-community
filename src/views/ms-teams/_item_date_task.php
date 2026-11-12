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

use open20\amos\community\utilities\CommunityUtil;

/**
 * @var $tasks []
 * @var $date string
 */


$user = \open20\amos\core\user\User::findOne($user_id);
$dateTime = null;
if ($date) {
    $dateTime = new \DateTime($date . ' 00:00:00');
}
?>

<div class="content-date-task">
    <div class="label-heading text-uppercase">
        <?php if ($dateTime) { ?>
            <strong><?= CommunityUtil::mapDayOfTheWeek($dateTime->format('w')) ?>
                <?= " " . $dateTime->format('d') . " " ?>
                <?= CommunityUtil::mapMonthNames($dateTime->format('n')) ?>
            </strong>
        <?php } ?>
    </div>

    <?php
    $i = 1;
    foreach ($tasks as $task) {
        $hash = hash('sha256', $task->id);
        $hashedId = htmlspecialchars($hash, ENT_QUOTES, 'UTF-8');
        ?>
        <?php if (get_class($task) == \open20\amos\community\models\clients\msmodels\MsCalendarEvent::className()) { ?>
            <?= $this->render('_item_meet', [
                'meet' => $task,
                'i' => $i,
                'hashedId' => $hashedId
            ])
            ?>

        <?php } else { ?>
            <?= $this->render('_item_task', [
                'task' => $task,
                'i' => $i,
                'hashedId' => $hashedId,
                'taskLinks' => $taskLinks
            ])
            ?>
        <?php } ?>
        <?= $this->render('_modal_task_meet', [
            'type' => 'date',
            'task' => $task,
            'hashedId' => $hashedId,
            'taskLinks' => $taskLinks
        ]) ?>

        <?php $i++;
    } ?>
</div>


