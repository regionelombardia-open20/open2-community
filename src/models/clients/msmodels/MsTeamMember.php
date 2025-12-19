<?php
/**
 * @property integer $id
 * @property array $roles
 * @property string $displayName
 * @property string $visibleHistoryStartDateTime
 * @property string $userId
 * @property string $email
 * @property string $tenantId
 */

namespace open20\amos\community\models\clients\msmodels;

use yii\base\Model;
use open20\amos\community\models\clients\msmodels\AbstractMsTeams;

class MsTeamMember extends AbstractMsTeams
{
    const ROLE_MEMBER = 'member';
    const ROLE_OWNER = 'owner';

    const ROLE_GUEST = 'guest';

    public $id;

    public $roles;
    public $displayName;
    public $visibleHistoryStartDateTime;

    public $userId;

    public $email;
    public $tenantId;




    public function init()
    {
        parent::init();
    }


}