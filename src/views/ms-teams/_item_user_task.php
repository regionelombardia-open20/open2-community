<?php
/**
 * @var $tasks []
 * @var $user_id string
 */

$user = \open20\amos\core\user\User::findOne($user_id);

?>

<div class="item-user-task content-user-task">
    <div class="label-heading user-heading">
        <?= \open20\amos\admin\widgets\UserCardWidget::widget(['model' => $user->userProfile]) ?>
        <strong><?= $user->userProfile->nomeCognome ?></strong>
    </div>
    <?php
    $i = 1;
    foreach ($tasks as $task) {
        $hash = hash('sha256', $task->id);
        $hashedId = htmlspecialchars($hash, ENT_QUOTES, 'UTF-8'); ?>
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
                'user' => $user,
                'i' => $i,
                'hashedId' => $hashedId,
            ])
            ?>
        <?php } ?>
        <?= $this->render('_modal_task_meet', [
            'type' => 'users',
            'task' => $task,
            'hashedId' => $hashedId,
            'taskLinks' => $taskLinks
        ]) ?>

        <?php $i++;
    } ?>
</div>
