<?php



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240625_174600_add_columns_ms_teams_change extends \yii\db\Migration
{
    const COMMUNITY_MM = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY_MM,
            'msgraph_channel_nextlink',
            $this->text()->defaultValue(null)->after('msgraph_channel_message_nextlink')->comment('NextLink')
        );
        $this->addColumn(
            self::COMMUNITY_MM,
            'msgraph_team_nextlink',
            $this->text()->defaultValue(null)->after('msgraph_channel_message_nextlink')->comment('Ms Membership team id')
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(
            self::COMMUNITY_MM,
            'msgraph_membership_id');

        return true;
    }
}