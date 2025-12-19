<?php



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240607_104600_add_columns_ms_teams extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_channel_id',
            $this->string()->defaultValue(null)->after('enable_bookmarks')->comment('Ms Channel id')
        );
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_team_id',
            $this->string()->defaultValue(null)->after('enable_bookmarks')->comment('Ms Team id')
        );

        $this->addColumn(
            self::COMMUNITY,
            'enable_msgraph_teams',
            $this->integer(1)->defaultValue(0)->after('enable_bookmarks')->comment('Enable Microsoft TEAMS')
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
            'msgraph_channel_id');
        $this->dropColumn(
            self::COMMUNITY,
            'msgraph_team_id');

        $this->dropColumn(
            self::COMMUNITY,
            'enable_msgraph_teams' );
        return true;
    }
}