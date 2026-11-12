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

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\module\BaseAmosModule;
use open20\amos\community\models\Bookmarks;
use open20\amos\community\rules\UpdateBookmarksRule;

?>

<div id="container-links">
    <?php
    if (count($data) > 0) {
        $i = 0;
        foreach ($data as $element) {
            if (isset($limit)) {
                if ($i >= $limit) {
                    break;
                }
                $i++;
            }
            $link = $element->link;
            $titolo = $element->titolo;
            $utente = $element->userNames;
            $dataPubblicazione = Yii::$app->getFormatter()->asDate($element->data_pubblicazione, 'dd/MM/yyyy');
            $testoPubblicazione = BaseAmosModule::t('amoscommunity', 'il {date}', ['date' => $dataPubblicazione]);
            if ($element->status === Bookmarks::BOOKMARKS_STATUS_PUBLISHED) {
                $testo = BaseAmosModule::t('amoscommunity', '#bookmarkPublished');
            }
            elseif ($element->status === Bookmarks::BOOKMARKS_STATUS_TOVALIDATE) {
                $testo = BaseAmosModule::t('amoscommunity', '#bookmarkToValidate');
            }
            else {
                $testo = BaseAmosModule::t('amoscommunity', '#bookmarkDraft');
            } ?>
            <div class="item-link-list">
                <div class="item-link-header">
                    <p class="m-b-0"><strong><a href="<?= $link ?>" data-toggle="tooltip" aria-label="<?= BaseAmosModule::t('amoscommunity', 'Vai al bookmark {titolo}',['titolo' => $titolo])?>" title="<?= $link ?>" target="_blank"><?= $titolo ?></a></strong></p>


                    <?php
                    echo ContextMenuWidget::widget([
                        'title' => BaseAmosModule::t('community', 'Menu contestuale del bookmark {titolo}?',['titolo' => $titolo]),
                        'model' => $element,
                        'actionModify' => "/community/bookmarks/update?id=" . $element->id,
                        'actionDelete' => "/community/bookmarks/delete?id=" . $element->id . "&community=" . $model->id,
                        'labelDeleteConfirm' => BaseAmosModule::t('community', 'Sei sicuro di voler cancellare questo bookmark?')
                    ]); ?>
                </div>


                <small><strong><?= $testo ?></strong> <?= $utente ?> <?= $testoPubblicazione ?></small>
            </div>
    <?php
        }
    } else {
        echo BaseAmosModule::t('community', 'Nessun bookmark presente');
    } ?>
</div>