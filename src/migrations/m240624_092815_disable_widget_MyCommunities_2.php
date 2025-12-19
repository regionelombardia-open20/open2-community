<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\migrations
 * @category   CategoryName
 */

use yii\db\Migration;

/**
 * Class m181012_162615_add_user_profile_area_field_1
 */
class m240624_092815_disable_widget_MyCommunities_2 extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        //$this->update('amos_widgets', ['sub_dashboard' => 0], ['classname' => 'open20\amos\chat\widgets\graphics\WidgetGraphicChatAssistance','module' => 'community']);
        $this->update('amos_widgets', ['status' => 0], ['classname' => 'openinnovation\landing\widgets\graphics\WidgetGraphicLiveChat','module' => 'community']);
        $this->update('amos_widgets', ['status' => 0], ['classname' => 'openinnovation\landing\widgets\graphics\WidgetGraphicCountdown','module' => 'community']);


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }
}
