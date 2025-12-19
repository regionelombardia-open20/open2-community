<?php



use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m230712_155300_add_widget_icon_bookmarks_permissions */
class m241001_154600_add_columns_ms_teams_driver_folder_id extends \yii\db\Migration
{
    const COMMUNITY = 'community';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::COMMUNITY,
            'msgraph_folder_id',
            $this->text()->defaultValue(null)->after('msgraph_channel_id')->comment('Driver folder id')
        );


        $this->addColumn(
            self::COMMUNITY,
            'msgraph_drive_id',
            $this->text()->defaultValue(null)->after('msgraph_channel_id')->comment('Driver folder id')
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
            'msgraph_drive_id'
        );
        $this->dropColumn(
            self::COMMUNITY,
            'msgraph_folder_id'
        );
        return true;
    }
}