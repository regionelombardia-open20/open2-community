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

use open20\amos\community\AmosCommunity;

/** @var $type
 * @var $task
 */
?>
<?php if ($type == 'calendar') {?>
    <div id="calendarModal" class="modal-planner modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span
                                class="sr-only">close</span></button>
                    <div class="title-item">
                        <div id="modalRadio" style="display:none">
                            <button type="button" class="click-done-status status-button"
                                    data-key=""
                                    id="status_">
                                <span class="icon-task mdi mdi-24px"></span>
                            </button>
                        </div>
                        <h5 id="modalTitle" class="modal-title"></h5>
                    </div>
                </div>
                <div id="modalBody" class="modal-body">
                    <div  class="label-mst-meeting" id="modalTeamsMeetlabel" style="display:none">Microsoft Teams Meet</div>
                    <div id="modalHour"></div>
                    <div id="modalContent"></div>

                </div>
                <div class="modal-footer">
                    <div id="modalTeamsMeet" style="display:none">
                        <a href="" target="_blank" id="joinUrl" class="btn btn-primary">Partecipa</a>
                    </div>
                    <div id="modalTeamsTask" style="display:none">
                        <a href="" target="_blank" id="taskUrl" class="btn btn-primary">Vedi dettagli su Teams</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else {
//    pr($taskLinks);
    $isMeet = false;
    if (get_class($task) == \open20\amos\community\models\clients\msmodels\MsCalendarEvent::className()) {
        $isMeet = true;
        $title = $task->subject;
        $startDate = $task->getObjStartDateTime();
        $endDate = $task->getObjEndDateTime();
        $hour = $startDate->format('d/m/Y') . ' | ' . $startDate->format('H:i') . ' - ' . $endDate->format('H:i');
        $joinUrl = $task->onlineMeeting['joinUrl'];
    } else {
        if(!empty($task->title)) {
            $title = $task->title;
            $dueDateTime = $task->getObjDueDateTime();
            if($dueDateTime) {
                $hour = $dueDateTime->format('d/m/Y') . ' | ' . $dueDateTime->format('H:i');
                $value = ($task->percentComplete == 100) ? true : false;

                if (!empty($taskLinks[$task->id])) {
                    $urlTaskMeet = $taskLinks[$task->id];
                }
            }
        }

    }
    ?>
    <div id="calendarModal_<?= $hashedId ?>" class="modal-planner modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                        <span class="sr-only">close</span>
                    </button>
                    <div class="title-item">
                        <?php if (!$isMeet) { ?>
                            <button type="button" class="click-done-status status-button <?= $value ? 'active' : '' ?>"
                                    data-key="<?= $task->id ?>"
                                    id="status_<?= $task->id ?>" title="<?= $title ?>">
                                <span class="icon-task mdi mdi-24px"></span>
                            </button>
                        <?php } else { ?>
                            <span class="mdi mdi-calendar mdi-24px"></span>
                        <?php } ?>
                        <h5 id="modalTitle" class="modal-title"><?= $title ?></h5>
                    </div>
                </div>
                <div id="modalBody" class="modal-body">
                    <?php if ($isMeet) { ?>
                        <div class="label-mst-meeting"><?= AmosCommunity::t('amoscommunity', "Microsoft Teams Meeting") ?></div>
                    <?php } else { ?>
                        <?php foreach ($task->getCategories() as $category) { ?>
                            <span class="mdi mdi-tag-outline"></span> <?= $category ?>
                        <?php } ?>
                    <?php } ?>
                    <div><span class="mdi mdi-calendar-text-outline"></span> <?= $hour ?></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= \Yii::t('amoscommunity', 'Chiudi') ?></button>
                    <?php if ($joinUrl) { ?>
                        <a href="<?= $joinUrl ?>" target="_blank" id="joinUrl" class="btn btn-primary">Partecipa</a>
                    <?php } ?>
                    <?php if ($urlTaskMeet) { ?>
                        <a href="<?= $urlTaskMeet ?>" target="_blank" id="joinUrl" class="btn btn-primary">Vedi dettagli su Teams</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>