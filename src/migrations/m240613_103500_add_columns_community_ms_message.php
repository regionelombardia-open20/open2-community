<?php



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240613_103500_add_columns_community_ms_message extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_channel_message_nextlink',
            $this->text()->defaultValue(null)->after('msgraph_channel_id')->comment('Ms Membership team id')
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(
            self::COMMUNITY,
            'msgraph_channel_message_nextlink');

        return true;
    }
}