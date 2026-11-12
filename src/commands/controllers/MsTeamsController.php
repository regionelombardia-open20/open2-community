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