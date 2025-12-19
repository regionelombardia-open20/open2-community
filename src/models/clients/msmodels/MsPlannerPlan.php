<?php

namespace open20\amos\community\models\clients\msmodels;

/**
 * PlannerPlanItem represents a single item in the PlannerPlan.
 *
 * @var string $etag The OData etag.
 * @var string $createdDateTime The creation date and time.
 * @var string $owner The owner ID.
 * @var string $title The title of the plan.
 * @var string $id The ID of the plan.

 */

use yii\base\Model;

class MsPlannerPlan extends AbstractMsTeams
{

    /**
     * @var string The OData etag.
     */
    public $etag;

    /**
     * @var string The creation date and time.
     */
    public $createdDateTime;

    /**
     * @var string The owner ID.
     */
    public $owner;

    /**
     * @var string The title of the plan.
     */
    public $title;

    /**
     * @var string The ID of the plan.
     */
    public $id;


    public $createdBy;


    public $container;

}