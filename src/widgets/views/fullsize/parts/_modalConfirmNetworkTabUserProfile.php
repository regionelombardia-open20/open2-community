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

use open20\amos\core\module\BaseAmosModule;
use open20\amos\community\AmosCommunity;

$js = <<< JS
    $(document).on('click', '.linkToCommunityJquery', function(event){ // Click su un link community
        event.preventDefault();
        var link = $(this).attr('href');
        $('#modalConfirmLinkCommunity').modal('show');
        $('#confirmOkModalButton').attr('href', link);
    });
JS;

$this->registerJs($js);
?>

<div id="modalConfirmLinkCommunity" class="modal bootstrap-dialog krajee-amos-modal type-warning fade size-normal in" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="bootstrap-dialog-title"><?= BaseAmosModule::t('amoscommunity', 'Confirm'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="display:none">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= AmosCommunity::t('amoscommunity','#confirm_exit_without_saving'); ?></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ban-circle"> </span>&nbsp;
                    <?= BaseAmosModule::t('amoscommunity', 'Annulla'); ?>
                </a>
                <a id="confirmOkModalButton" class="btn btn-primary">
                    <span class="glyphicon glyphicon-ok"> </span>&nbsp;
                    <?= BaseAmosModule::t('amoscommunity', 'Ok'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
