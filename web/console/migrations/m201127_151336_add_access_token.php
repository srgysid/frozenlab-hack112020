<?php

use yii\db\Migration;

/**
 * Class m201127_151336_add_access_token
 */
class m201127_151336_add_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'access_token', $this->string(64));
        $this->createIndex('udx-user-access_token', 'user', 'access_token', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'access_token');
    }
}
