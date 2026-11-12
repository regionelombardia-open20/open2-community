<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
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
use open20\amos\core\helpers\Html;

/** @var \open20\amos\community\utilities\EmailUtil $util */

if(!empty($profile)) {
    $this->params['profile'] = $profile;
}
?>
<div>
    <div style="box-sizing:border-box;">
        <div style="padding:5px 10px;background-color: #F2F2F2;">
            <h1 style="color:#297A38;text-align:center;font-size:1.5em;margin:0;"><?= AmosCommunity::t('amoscommunity', '#registration_rejected_mail_title') ?></h1>
        </div>
        <div
            style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff; margin-top: 20px;">
            <h2 style="font-size:2em;line-height: 1;"><?= $util->managerName . " " . AmosCommunity::t('amoscommunity', '#registration_rejected_mail_text_1') . $util->contextLabel ?></h2>

            <div style="display: flex; padding: 10px;">
                <?php if ($util->isCommunityContext): ?>
                    <div
                        style="width: 50px; height: 50px; -webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                        <?= \open20\amos\community\widgets\CommunityCardWidget::widget([
                            'model' => $util->community,
                            'onlyLogo' => true,
                            'absoluteUrl' => true,
                            'inEmail' => true
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php
                $divOptions = $util->isCommunityContext ? ['style' => 'margin: 0 0 0 20px;'] : [];
                echo Html::tag('div', '<p style="font-weight: 900">' . $util->community->name . '</p>
                <p>' . $util->community->getDescription(true) . '</p>', $divOptions)
                ?>
            </div>
        </div>
    </div>
</div>