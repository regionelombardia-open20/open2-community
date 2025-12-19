<?php

namespace open20\amos\community\commands\controllers;

use open20\amos\community\AmosCommunity;
use open20\amos\community\utilities\MsTeamsUtility;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;


/**
 * MsTeams controller
 * @package open20\amos\community\commands\controllers
 */
class MsTeamsController extends \yii\console\Controller
{
    /**
     * @var string[] $passedOptions
     */
    public $passedOptions = [

    ];

    /**
     * @param $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return ArrayHelper::merge(parent::options($actionID), $this->passedOptions);
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->module = AmosCommunity::instance();
        parent::init();
    }

    /**
     * @return void
     */
    public function actionRegisterWebHooks()
    {
        $myfile = fopen("cron_webhooks.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "###############################\n");
        fwrite($myfile, date('d/m/Y H:i:s') . "\n");
        MsTeamsUtility::registerWebHooks();
        fwrite($myfile, "----FINITO" . "\n");
        fwrite($myfile, "###############################" . "\n");

    }

}