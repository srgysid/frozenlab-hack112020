<?php

use yii\db\Migration;

/**
 * Class m201127_163743_add_dictionary
 */
class m201127_163743_add_dictionary extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('type_order', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);

        $this->createTable('type_message', [
            'id' => $this->primaryKey(),
            'type_order_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
        ]);

        $this->addForeignKey("fk_type_message_type_order", 'type_message', "type_order_id", "type_order", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_type_message_type_order_id', 'type_message', 'type_order_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('type_message');
        $this->dropTable('type_order');
    }
}
