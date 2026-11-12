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
use open20\amos\community\utilities\CommunityUtil;

/**
 * @var $this \yii\web\View
 * @var $tasks
 * @var $communityId
 * @var $model \open20\amos\community\models\Community
 * @var $isCommunityManager boolean
 */
$js = <<<JS
    $('.click-done-status').on('click', function(e){
        e.preventDefault();
        let current = $(this);
         $.ajax({
            url: '/community/ms-teams/update-task',
            type: 'get',
            data: {id: $(this).attr('data-key')},
            success: function (data) {
                //console.log(data);
                let link = $(current).parents('.item-task').find('.link-task');
                if($(current).parents('.modal-planner')){
                    if(data){
                        $('#modalTitle').attr('style',"text-decoration: line-through;");
                        $(current).addClass('active');
                    }else {
                         $('#modalTitle').attr('style',"");
                         $(current).removeClass('active');
                    }
                }else{
                    if(data){
                        $(link).attr('style',"text-decoration: line-through;");
                        $(current).addClass('active');
                    }else {
                         $(link).attr('style',"");
                         $(current).removeClass('active');
    
                    }
                }
            }
         });
      });

    $('.link-meet,.link-task').on('click', function(e){
        e.preventDefault();
        let id = $(this).attr('data-key');
        

        $('#calendarModal_'+id).modal('show');
             // $('#modalTitle').html(event.title);
             //        $('#modalHour').html(event.nonstandard['hour']);
             //        if(event.nonstandard['is_meet']){
             //           $('#joinUrl').attr('href', event.nonstandard['joinUrl']);
             //           $('#modalTeamsMeet').show();
             //        }else{
             //           $('#modalTeamsMeet').hide();
             //        }
             //       // $('#modalContent').html(event.nonstandard['html']);
             //        $('#calendarModal').modal('show');
             //        $('#eventUrl').attr('href',event.url);
    });

// Funzione per aggiornare l'icona in base allo stato del bottone
function updateIcon(button) {
    const iconTask = button.querySelector('.icon-task');
    
    if (iconTask) {
        if (button.classList.contains('active')) {
            iconTask.classList.add('mdi-checkbox-marked-circle');
            iconTask.classList.remove('mdi-checkbox-blank-circle-outline');
        } else {
            iconTask.classList.add('mdi-checkbox-blank-circle-outline');
            iconTask.classList.remove('mdi-checkbox-marked-circle');
        }
    }
}

// Inizializza lo stato delle icone al caricamento della pagina
document.querySelectorAll('.status-button').forEach(button => {
    updateIcon(button);  // Aggiorna l'icona in base allo stato iniziale

    // Aggiungi l'event listener per gestire il click
    button.addEventListener('click', function() {
        this.classList.toggle('active');
        updateIcon(this);  // Aggiorna l'icona al clic
    });
});

JS;

$this->registerJs($js);
//$currentView = \Yii::$app->request->get('view');
$this->title = AmosCommunity::t('amoscommunity', 'Planner');
$this->params['breadcrumbs'][] = $this->title;

if ($date) {
    $currentDate = new \DateTime($date);
    $nextMonthDate = clone $currentDate;
    $lastMonthDate = clone $currentDate;
    $nextMonthDate->modify('first day of next month');
    $lastMonthDate->modify('first day of last month');
} else {
    $nextMonthDate = new \DateTime('first day of next month');
    $lastMonthDate = new \DateTime('first day of last month');
    $currentDate = new \DateTime();
}
?>
<?php if (empty($linkPlannerTeams) && $isCommunityManager) {?>
    <div class="alert alert-warning"><?= AmosCommunity::t('amoscommunuty', "Non è ancora stato attivato il planner su Microsoft Teams. <a target=\"_blank\" href='{link}'>Accedi per consultare il team <span class=\"icon mdi mdi-arrow-right-circle-outline\"></span></a>",[
            'link' => $linkTeamChannel
        ]) ?></div>
<?php } ?>

    <div class="row m-t-20">
        <div class="col-md-4">
            <strong class="text-secondary m-r-10"><?= AmosCommunity::t('amoscommunuty', 'TO DO') ?></strong>
            <?php
            if (!empty($linkPlannerTeams)) {
                echo \yii\helpers\Html::a(
                    '<span class="mdi mdi-microsoft-teams mdi-24px m-r-5"></span>' . '<strong>Apri su teams</strong>' . '<span class="icon mdi mdi-arrow-right-circle-outline"></span>',
                    $linkPlannerTeams,
                    ['class' => 'link-all text-uppercase m-t-0 p-t-0 align-items-center small color-ms-teams']
                );
            } ?>

        </div>
        <div class="col-md-8">
            <div class="filter-view-planner">

                <strong class="text-secondary"><?= AmosCommunity::t('amoscommunuty', 'Visualizza') ?> </strong>
                <?php if ($isCommunityManager) {
                    echo \yii\helpers\Html::a(
                        AmosCommunity::t('amoscommunity', 'Per persona'),
                        ['/community/ms-teams/planner-index', 'id' => $communityId, 'view' => 'users'],
                        ['class' => ($currentView == 'users' || empty($currentView)) ? 'bold' : '']
                    );
                } ?>
                <?php echo \yii\helpers\Html::a(
                    AmosCommunity::t('amoscommunity', 'Per data di scadenza'),
                    ['/community/ms-teams/planner-index', 'id' => $communityId, 'view' => 'date'],
                    ['class' => $currentView == 'date' ? 'bold' : '']
                ) ?>
                <?php echo \yii\helpers\Html::a(
                    AmosCommunity::t('amoscommunity', 'Calendario'),
                    ['/community/ms-teams/planner-index', 'id' => $communityId, 'view' => 'calendar'],
                    ['class' => $currentView == 'calendar' ? 'bold' : '']
                ) ?>
            </div>
        </div>
    </div>

<?php


if ($currentView == 'date') { ?>
    <div class="planner-wrapper planner-nav-date">
        <?php echo \yii\helpers\Html::a(
            \open20\amos\core\icons\AmosIcons::show('arrow-left') . '<span>' . CommunityUtil::mapMonthNames($lastMonthDate->format('n')) . '</span>',
            ['/community/ms-teams/planner-index', 'id' => $communityId, 'view' => 'date', 'current_date' => $lastMonthDate->format('d-m-Y')],
            ['class' => 'btn btn-xs btn-default']
        ) ?>
        <strong><?php echo CommunityUtil::mapMonthNames($currentDate->format('n')) . ' ' . $currentDate->format('Y'); ?></strong>
        <?php echo \yii\helpers\Html::a(
            '<span>' . CommunityUtil::mapMonthNames($nextMonthDate->format('n')) . '</span>' . \open20\amos\core\icons\AmosIcons::show('arrow-right'),
            ['/community/ms-teams/planner-index', 'id' => $communityId, 'view' => 'date', 'current_date' => $nextMonthDate->format('d-m-Y')],
            ['class' => 'btn btn-xs btn-default']
        ) ?>

    </div>
    <div class="planner-wrapper planner-date">

        <?php
        $j = 0;
        foreach ($tasks as $date => $dateTasks) {
            if (!empty($dateTasks)) { ?>
                <div class="planner-col">
                    <?= $this->render('_item_date_task', [
                        'tasks' => $dateTasks,
                        'date' => $date,
                        'communityId' => $communityId,
                        'taskLinks' => $taskLinks
                    ]) ?>
                </div>
                <?php
                $j++;
            } ?>
        <?php }
        if ($j == 0) { ?>
            <div>
                <?= AmosCommunity::t('amoscommunity', "Nessun task/meeting presente per questo mese"); ?>
            </div>
        <?php } ?>
    </div>
<?php } else if ($currentView == 'calendar') { ?>
    <div class="planner-wrapper planner-calendar">

        <?php
        if ($dataProvider === null) { ?>
            <p><?= AmosCommunity::t('amoscommunity', "Il calendario è vuoto") ?></p>
        <?php } else {
            echo \open20\amos\core\views\CalendarView::widget(
                [
                    'dataProvider' => $dataProvider,
                    'itemView' => '_calendar',
                    'options' => [
                        'lang' => 'it',
                    ],
                    'eventConfig' => [
                        'id' => 'id',
                        'title' => 'title',
                        'start' => 'dataFine',
                        'end' => 'dataFine',
                        'color' => 'color',
                        'url' => 'url',
                    ],
                    'clientOptions' => [
                        'eventClick' => new \yii\web\JsExpression("function(event, jsEvent, view) {                   
                    $('#modalTitle').html(event.title);
                    let iconCalendar = '<span class=\"mdi mdi-calendar-text-outline\">';
                    if(event.nonstandard['is_meet']){
                      $('#modalHour').html( event.nonstandard['tags']+iconCalendar + event.nonstandard['date']+' | '+event.nonstandard['hour']);
                       $('#joinUrl').attr('href', event.nonstandard['joinUrl']);
                       $('#modalTeamsMeet').show();
                       $('#modalTeamsMeetlabel').show();
                       $('#modalRadio').hide();
                       $('#modalTeamsTask').hide();
                    }else{
                       $('#modalHour').html( event.nonstandard['tags']+ '<br>'+iconCalendar + event.nonstandard['date']);
                       $('#taskUrl').attr('href', event.nonstandard['taskUrl']);
                       $('#modalTeamsMeet').hide();
                       $('#modalTeamsTask').show();
                       $('#modalTeamsMeetlabel').hide();
                       $('#modalRadio').show();
                       let radio = $('#modalRadio button');
                       $(radio).attr('data-key', event.nonstandard['id']);
                       $(radio).attr('id','status_'+event.nonstandard['id']);
                       if(event.nonstandard['completed']){
                            $(radio).addClass('active');
                            $('#modalTitle').attr('style','text-decoration: line-through;');
                            $(radio).find('span').addClass('mdi-checkbox-marked-circle');
                            $(radio).find('span').removeClass('mdi-checkbox-blank-circle-outline');
                       }else{
                            $('#modalTitle').attr('style','');
                            $(radio).removeClass('active');
                            $(radio).find('span').addClass('mdi-checkbox-blank-circle-outline');
                            $(radio).find('span').removeClass('mdi-checkbox-marked-circle');
                       }
                    }
                    
                    console.log(event.nonstandard['debug']);

                    
                    $('#calendarModal').modal('show');
                    $('#eventUrl').attr('href',event.url);
                }"),
                        'eventRender' => new \yii\web\JsExpression('function(event, element) {
var text= "";

if(event.nonstandard[\'is_meet\']){
console.log(event.nonstandard[\'debug\']);

text = "Microsoft Teems Meet";
            } else {
            text = "Attività";
            }
      let style = \'\';
      if(event.nonstandard[\'completed\']){
        style = \'text-decoration: line-through;\'
      }
            element.html("<div class=\'custom-event\'><strong style=\'"+style+"\'>" +event.title + "</strong>"+"<div class=\'label-mst-meeting\'><small>"+text+"</small></div>"+event.nonstandard[\'hour\']+"</div>");
            
            // Aggiungi classi CSS personalizzate
            element.addClass("my-custom-class");

            // Aggiungi attributi personalizzati
            element.attr("data-custom-attribute", event.customAttribute);
        }'),

                        'fixedWeekCount' => false,
                        'eventTimeFormat' => 'H:mm',
                    ],
                    'ajaxEvents' => \yii\helpers\Url::to(['/community/ms-teams/jsoncalendar', 'community_id' => $model->id]),
                    'array' => false, //se ci sono più eventi legati al singolo record
                    //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
                ]
            );
        } ?>
    </div>
    <?= $this->render('_modal_task_meet', [
        'type' => 'calendar',
        'taskLinks' => $taskLinks
    ]) ?>

<?php } else {
    $k = 0; ?>
    <div class="planner-wrapper planner-user">
        <?php foreach ($tasks as $userId => $userTasks) {
            $k++; ?>
            <div class="planner-col">
                <?= $this->render('_item_user_task', [
                    'tasks' => $userTasks,
                    'user_id' => $userId,
                    'taskLinks' => $taskLinks

                ]) ?>
            </div>
        <?php } ?>
        <?php if ($k == 0) { ?>
            <?= AmosCommunity::t('amoscommunity', "Nessun task/meeting presente"); ?>
        <?php } ?>
    </div>
<?php } ?>