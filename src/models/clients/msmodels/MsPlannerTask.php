<?php

namespace open20\amos\community\models\clients\msmodels;

use open20\amos\community\models\clients\MsGraphBuilder;
use open20\amos\community\models\CommunityMsgraphTokens;
use open20\amos\core\user\User;

/**
 * MsPlannerTask represents the data structure for a Microsoft Planner task.
 *
 * @var string $etag The OData etag.
 * @var string $planId The ID of the plan.
 * @var string $bucketId The ID of the bucket.
 * @var string $title The title of the task.
 * @var string $orderHint The order hint.
 * @var string $assigneePriority The assignee priority.
 * @var int $percentComplete The percent complete.
 * @var string $startDateTime The start date and time.
 * @var string $createdDateTime The creation date and time.
 * @var string $dueDateTime The due date and time.
 * @var bool $hasDescription Whether the task has a description.
 * @var string $previewType The preview type.
 * @var string $completedDateTime The completion date and time.
 * @var string $completedBy The user who completed the task.
 * @var int $referenceCount The reference count.
 * @var int $checklistItemCount The checklist item count.
 * @var int $activeChecklistItemCount The active checklist item count.
 * @var string $conversationThreadId The conversation thread ID.
 * @var int $priority The priority.
 * @var string $id The ID of the task.
 * @var  $createdBy  user who created the task.
 * @var array $appliedCategories The applied categories.
 * @var array $assignments The assignments.
 */
class  MsPlannerTask extends AbstractMsTeams
{

    public $etag;
    public $planId;
    public $bucketId;
    public $title;
    public $orderHint;
    public $assigneePriority;
    public $percentComplete;
    public $startDateTime;
    public $createdDateTime;
    public $dueDateTime;
    public $hasDescription;
    public $previewType;
    public $completedDateTime;
    public $completedBy;
    public $referenceCount;
    public $checklistItemCount;
    public $activeChecklistItemCount;
    public $conversationThreadId;
    public $priority;
    public $createdBy;
    public $appliedCategories;
    public $assignments;

    public $id;

    public static $categories = [];
    public $dataInizio;
    public $dataFine;
    public $color;
    public $url = null;

    public function init()
    {
        parent::init();
        $startDate = $this->getObjDueDateTime();
        $this->dataInizio = $startDate->format('Y-m-d H:i:s');
        $date = clone $startDate;
        $endDate  = $date->sub(new \DateInterval('PT1H'));
        $this->dataFine = $endDate->format('Y-m-d H:i:s');
        $this->color = '#fff';
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getAssignedUsers()
    {
        $users = [];
        $assignments = $this->assignments;
        if (!empty($assignments)) {
            foreach ($assignments as $userId => $assignment) {
//                pr($userId);
                if($userId) {
                    $msToken = CommunityMsgraphTokens::find()->andWhere(['msgraph_user_id' => $userId])->one();
                    if ($msToken) {
                        $user = User::findOne($msToken->user_id);
                        if ($user) {
//                            pr($msToken->user_id);
                            $users [] = $user;
                        }
                    }
                }
            }
        }
        return $users;
    }

    public function getCategories()
    {
        if (empty(self::$categories)) {
            $msBuilder = new MsGraphBuilder();
            $planDetail = $msBuilder->getPlannerPlanDetail($this->planId);
            self::$categories = $planDetail->categoryDescriptions;
        }
        $categories = [];
        foreach ($this->appliedCategories as $id => $val) {
            if (!empty(self::$categories[$id])) {
                $categories [] = self::$categories[$id];
            }
        }
        return $categories;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function formattedDueDateTime()
    {
        if (!empty($this->dueDateTime)) {
            $date = new \DateTime($this->dueDateTime);
            return $date->format('d/m/Y');
        }
        return '';
    }

    public function getObjDueDateTime()
    {
        if (!empty($this->dueDateTime)) {
            $date = new \DateTime($this->dueDateTime);
            return $date;
        }
        return '';
    }

    public function getObjStartDateTime()
    {
        if (!empty($this->startDateTime)) {
            $date = new \DateTime($this->startDateTime);
            return $date;
        }
        return '';
    }
}