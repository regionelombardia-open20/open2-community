<?php

namespace open20\amos\community\models\clients\msmodels;

class MsPlannerPlanDetail extends AbstractMsTeams
{
    public $id;

    /**
     * @var array The users the plan is shared with.
     */
    public $sharedWith;

    /**
     * @var array The category descriptions.
     */
    public $categoryDescriptions;




}