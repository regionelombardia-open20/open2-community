<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\assets;

use yii\web\AssetBundle;

/**
 * Class AmosCommunityAsset
 * @package lispa\amos\community\assets
 */
class AmosCommunityAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lispa/amos-community/src/assets/web';

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
        if(!empty($moduleL))
        {
            $this->depends [] = 'lispa\amos\layout\assets\BaseAsset';
            $this->depends [] = 'lispa\amos\layout\assets\SpinnerWaitAsset';
        }
        else
        {
            $this->depends [] = 'lispa\amos\core\views\assets\AmosCoreAsset';
            $this->depends [] = 'lispa\amos\core\views\assets\SpinnerWaitAsset';
        }
        parent::init();
    }

}