<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community
 * @category   CategoryName
 */

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m220711_093600_create_table_community_links
 */
class m240605_161000_create_table_community_msgraph_tokens extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%community_msgraph_tokens}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('user id'),
            'msgraph_user_id' => $this->string()->comment('ms graph user id'),
            'access_token' => $this->text()->comment('access token'),
            'refresh_token' => $this->text()->comment('refresh token'),
            'expire_date' => $this->string()->comment('expire date'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function beforeTableCreation()
    {
        parent::beforeTableCreation();
        $this->setAddCreatedUpdatedFields(true);
    }

    /**
     * @inheritdoc
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey('fk_community_msgraph_tokens_user_id', $this->tableName, 'user_id', 'user', 'id');
    }
}
