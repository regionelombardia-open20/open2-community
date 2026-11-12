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

namespace open20\amos\community\assets;

use yii\web\AssetBundle;
use open20\amos\core\widget\WidgetAbstract;

/**
 * Class AmosCommunityAsset
 * @package open20\amos\community\assets
 */
class AmosCommunityAsset extends AssetBundle
{
    public $sourcePath = '@vendor/open20/amos-community/src/assets/web';

    public $js = [
        'js/community.js'
    ];
    public $css = [
        'less/community.less',
    ];

    public $depends = [
    ];

    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');

        if(!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS){
            $this->css = ['less/community_fullsize.less','less/community_design_bi.less','less/scope_organizzazioni_design_bi.less', 'less/new_community_design_bi.less'];
        }

        if(!empty($moduleL))
        {
            $this->depends [] = 'open20\amos\layout\assets\BaseAsset';
            $this->depends [] = 'open20\amos\layout\assets\SpinnerWaitAsset';
        }
        else
        {
            $this->depends [] = 'open20\amos\core\views\assets\AmosCoreAsset';
            $this->depends [] = 'open20\amos\core\views\assets\SpinnerWaitAsset';
        }
        parent::init();
    }

}