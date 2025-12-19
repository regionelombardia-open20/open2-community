<?php

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


