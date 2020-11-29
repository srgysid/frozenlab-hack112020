<?php

use yii\db\Migration;

/**
 * Class m201128_164908_add_answer
 */
class m201128_164908_add_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('answers', [
            'id' => $this->primaryKey(),
            'orders_id' => $this->integer(),
            'status_id'=> $this->smallInteger(),
            'full_desc' => $this->text(),
            'creator_id' => $this->integer()->notNull(),
            'updater_id' => $this->integer()->notNull(),
            'created_at' => 'timestamp with time zone not null',
            'updated_at' => 'timestamp with time zone not null',
        ]);

        $this->addForeignKey("fk_answers_creator", 'answers', "creator_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_answers_creator_id', 'answers', 'creator_id');
        $this->addForeignKey("fk_answers_updater", 'answers', "updater_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_answers_updater_id', 'answers', 'updater_id');
        $this->addForeignKey("fk_answers_orders", 'answers', "orders_id", "orders", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_answers_orders_id', 'answers', 'orders_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('answers');
    }
}
