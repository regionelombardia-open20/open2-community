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
class m240528_165717_add_order_community_widgets extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('community_amos_widgets_mm', 'n_order', $this->integer()->after('personalized'));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('community_amos_widgets_mm', 'n_order');

        return true;
    }
}
