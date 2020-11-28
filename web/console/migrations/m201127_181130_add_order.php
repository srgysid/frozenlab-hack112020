<?php

use yii\db\Migration;

/**
 * Class m201127_181130_add_order
 */
class m201127_181130_add_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'type_cards' => $this->smallInteger(),
            'type_performers' => $this->smallInteger(),
            'priority' => $this->smallInteger(),
            'short_desc' => $this->string(255),
            'required_date' => 'timestamp with time zone',
            'fact_date' => 'timestamp with time zone',
            'reaction' => $this->smallInteger(),
            'type_message_id' => $this->integer(),
            'full_desc' => $this->text(),
            'department_id' => $this->integer(),
            'closer_id' => $this->integer(),
            'creator_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer()->notNull(),
            'created_at' => 'timestamp with time zone not null',
            'updated_at' => 'timestamp with time zone not null',
            'closed_at' => 'timestamp with time zone',
        ]);
        $this->addForeignKey("fk_orders_creator", 'orders', "creator_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_creator_id', 'orders', 'creator_id');
        $this->addForeignKey("fk_orders_updater", 'orders', "updater_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_updater_id', 'orders', 'updater_id');
        $this->addForeignKey("fk_orders_closer", 'orders', "closer_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_closer_id', 'orders', 'closer_id');
        $this->addForeignKey("fk_orders_department", 'orders', "department_id", "department", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_department_id', 'orders', 'department_id');

        $this->addForeignKey("fk_orders_type_message", 'orders', "type_message_id", "type_message", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_type_message_id', 'orders', 'type_message_id');

        $this->createTable('orders_performer', [
            'id' => $this->primaryKey(),
            'orders_id' => $this->integer(),
            'card_id' => $this->integer(),
        ]);
        $this->addForeignKey("fk_orders_performer_orders", 'orders_performer', "orders_id", "orders", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_performer_orders_id', 'orders_performer', 'orders_id');
        $this->addForeignKey("fk_orders_performer_card", 'orders_performer', "card_id", "card", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_orders_performer_card_id', 'orders_performer', 'card_id');

        $this->createTable('orders_files', [
            'id' => $this->primaryKey(),
            'type_source' => $this->integer()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'created_at' => 'timestamp with time zone not null',
            'original_name' => $this->string(255),
            'file_type' => $this->string(255),
            'file_ext' => $this->string(255),
            'file_size' => $this->integer(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('orders_files');
        $this->dropTable('orders_performer');
        $this->dropTable('orders');
    }

}
