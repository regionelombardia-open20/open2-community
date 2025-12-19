<?php

namespace open20\amos\community\widgets;

use yii\base\Widget;

class CommunityMenuPluginsWidget extends Widget
{
    public $model;
    public $limit = 4;

    public function run(){

        $subDashboardWidget = new \open20\amos\dashboard\widgets\SubDashboardWidget([
            'model' => $this->model,
            'widgets_type' => 'ICON',
        ]);

        $widgets = $subDashboardWidget->getWidgets(true);
        return $this->render('community_menu_plugins', [
            'widgets' => $widgets,
            'limit' => $this->limit,
            'model' => $this->model
        ]);
    }

}