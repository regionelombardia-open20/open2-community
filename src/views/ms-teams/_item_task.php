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

use yii\helpers\Html;
use \open20\amos\core\icons\AmosIcons;

/**
 * @var $task \open20\amos\community\models\clients\msmodels\MsPlannerTask
 */
?>
<div class="item-task">

    <div class="title-item">
        <?php
        $style = '';
        $labelTitle = "Contrassegna l'attività come completata";
        $value = ($task->percentComplete == 100) ? true : false;
        if ($value) {
            $style = 'text-decoration: line-through;';
            $labelTitle = "Contrassegna l'attività come incompleta";
        }

        $userAssigned = $task->getAssignedUsers();
        foreach ($userAssigned as $userAssign) {
            $taskUserIds [] = $userAssign->id;
        }

        if (in_array(\Yii::$app->user->id, $taskUserIds)) {
            if ($user) { ?>
                <button type="button" class="click-done-status status-button <?= $value ? 'active' : '' ?>"
                        data-key="<?= $task->id ?>"
                        id="status_<?= $user->id ?>_<?= $task->id ?>" title="<?= $labelTitle ?>">
                    <span class="icon-task mdi mdi-24px"></span>
                </button>
            <?php } else { ?>
                <button type="button" class="click-done-status status-button <?= $value ? 'active' : '' ?>"
                        data-key="<?= $task->id ?>"
                        id="status_<?= $task->id ?>" title="<?= $labelTitle ?>">
                    <span class="icon-task mdi mdi-24px"></span>
                </button>
            <?php } ?>
        <?php } ?>

        <a data-key="<?= $hashedId ?>" style="<?= $style ?>" class='link-task' href="#" title="Apri dettaglio attività"><strong><?= $task->title ?></strong></a>
    </div>
    <div class="item-body">
        <?php foreach ($task->getCategories() as $category) { ?>
            <span class="mdi mdi-tag-outline"></span> <?= $category ?>
        <?php } ?>
        <div class="date-item">
            <span class="mdi mdi-calendar-text-outline"></span> <?= $task->formattedDueDateTime() ?>
        </div>
    </div>
</div>

<script>


</script>