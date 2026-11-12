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


$checkedByDefault = false;
if(!empty($this->params['checkedByDefault']) && $this->params['checkedByDefault'] == true ){
    $checkedByDefault = true;
}
?>
<?php $object = \Yii::createObject($model['classname']);?>
<?php if($object->isVisible()) { ?>

    <div class="py-2">
        <div class="form-check">
            <input id="<?=\yii\helpers\StringHelper::basename($model['classname']);?>" type="checkbox" name="amosWidgetsIds[]" value="<?=$model['id'];?>" <?= (empty($this->params['widgetSelected']) && $checkedByDefault) ? 'checked' : (in_array($model['id'], $this->params['widgetSelected'])? 'checked' : '') ?> />
            <label for="<?=\yii\helpers\StringHelper::basename($model['classname']);?>"><?= \Yii::createObject($model['classname'])->getDescription(); ?></label>
        </div>
        
    </div>
<?php } ?>
