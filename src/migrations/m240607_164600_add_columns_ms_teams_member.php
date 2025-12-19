<?php



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m240607_164600_add_columns_ms_teams_member extends \yii\db\Migration
{
    const COMMUNITY_MM = 'community_user_mm';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY_MM,
            'msgraph_membership_id',
            $this->text()->defaultValue(null)->after('role')->comment('Ms Membership team id')
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