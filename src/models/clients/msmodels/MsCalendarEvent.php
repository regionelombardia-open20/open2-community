<?php

namespace open20\amos\community\models\clients\msmodels;
/**
 * Class MicrosoftEvent
 *
 * @var string $odata_etag
 * @var string $id
 * @var string $createdDateTime
 * @var string $lastModifiedDateTime
 * @var string $changeKey
 * @var array $categories
 * @var string $transactionId
 * @var string $originalStartTimeZone
 * @var string $originalEndTimeZone
 * @var string $iCalUId
 * @var string $uid
 * @var int $reminderMinutesBeforeStart
 * @var bool $isReminderOn
 * @var bool $hasAttachments
 * @var string $subject
 * @var string $bodyPreview
 * @var string $importance
 * @var string $sensitivity
 * @var bool $isAllDay
 * @var bool $isCancelled
 * @var bool $isOrganizer
 * @var bool $responseRequested
 * @var string $seriesMasterId
 * @var string $showAs
 * @var string $type
 * @var string $webLink
 * @var string $onlineMeetingUrl
 * @var bool $isOnlineMeeting
 * @var string $onlineMeetingProvider
 * @var bool $allowNewTimeProposals
 * @var string $occurrenceId
 * @var bool $isDraft
 * @var bool $hideAttendees
 * @var array $responseStatus
 * @var array $body
 * @var array $start
 * @var array $end
 * @var array $location
 * @var array $locations
 * @var array $recurrence
 * @var array $attendees
 * @var array $organizer
 * @var array $onlineMeeting
 * @var string $calendar_associationLink
 * @var string $calendar_navigationLink
 */
class MsCalendarEvent extends AbstractMsTeams
{
    public $odata_etag;
    public $id;
    public $createdDateTime;
    public $lastModifiedDateTime;
    public $changeKey;
    public $categories = [];
    public $transactionId;
    public $originalStartTimeZone;
    public $originalEndTimeZone;
    public $iCalUId;
    public $uid;
    public $reminderMinutesBeforeStart;
    public $isReminderOn;
    public $hasAttachments;
    public $subject;
    public $bodyPreview;
    public $importance;
    public $sensitivity;
    public $isAllDay;
    public $isCancelled;
    public $isOrganizer;
    public $responseRequested;
    public $seriesMasterId;
    public $showAs;
    public $type;
    public $webLink;
    public $onlineMeetingUrl;
    public $isOnlineMeeting;
    public $onlineMeetingProvider;
    public $allowNewTimeProposals;
    public $occurrenceId;
    public $isDraft;
    public $hideAttendees;
    public $responseStatus = [];
    public $body = [];
    public $start = [];
    public $end = [];
    public $location = [];
    public $locations = [];
    public $recurrence = [];
    public $attendees = [];
    public $organizer = [];
    public $onlineMeeting = [];
    public $calendar_associationLink;
    public $calendar_navigationLink;

    public $dataInizio;
    public $dataFine;
    public $color;
    public $url = null;
    public $title = null;

    public function init()
    {
        parent::init();
        $startDate = $this->getObjStartDateTime();
        $this->dataInizio = $startDate->format('Y-m-d H:i:s');
        $endDate = $this->getObjEndDateTime();
        $this->dataFine = $endDate->format('Y-m-d H:i:s');
        $this->color = 'red';
    }

    public function getObjStartDateTime()
    {
        if (!empty($this->start['dateTime'])) {
            $utcTimezone = new \DateTimeZone('UTC');
            $date = new \DateTime($this->start['dateTime'], $utcTimezone);

            $romeTimezone = new \DateTimeZone('Europe/Rome');
            $date->setTimezone($romeTimezone);
            return $date;
        }
        return '';
    }

    public function getObjEndDateTime()
    {
        if (!empty($this->end['dateTime'])) {
            $utcTimezone = new \DateTimeZone('UTC');
            $date = new \DateTime($this->end['dateTime'], $utcTimezone);

            $romeTimezone = new \DateTimeZone('Europe/Rome');
            $date->setTimezone($romeTimezone);
            return $date;

        }
        return '';
    }

    public function getUrlMeeting()
    {
        if (!empty($this->onlineMeeting['joinUrl'])) {
            return $this->onlineMeeting['joinUrl'];
        }
        return '';
    }

    public function getParticipants()
    {

    }

    public function getChannelId()
    {
        if (!empty($this->onlineMeeting['joinUrl'])) {
            $url = $this->onlineMeeting['joinUrl'];

            preg_match('/meetup-join\/(.*?)\//', $url, $matches);

            if (isset($matches[1])) {
                // Passo 2: Decodificare l'ID del thread
                $threadId = urldecode($matches[1]);
                return $threadId;

            }
        }
        return null;

    }
}