<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\migrations
 * @category   CategoryName
 */

/**
 * Class m200702_123817_add_column_redirect
 *
 */
class m240530_175717_enable_widgets_graphics extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets', [
            'sub_dashboard' => 1
        ], ['classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsComments', 'module' => 'community']);

        $this->insert('amos_widgets', [
            'sub_dashboard' => 1,
            'classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsLinks',
            'module' => 'community',
            'default_order' => 1,
            'type'=> 'GRAPHIC'
            ]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', [
            'sub_dashboard' => 0
        ], ['classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsComment', 'module' => 'community']);

        $this->delete('amos_widgets', [
            'sub_dashboard' => 1,
            'classname' => 'open20\amos\community\widgets\graphics\WidgetGraphicsLinks',
            'module' => 'community',
            'default_order' => 1,
            'type'=> 'GRAPHIC'
        ]);
        return true;
    }
}
