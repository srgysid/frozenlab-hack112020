<?php

use yii\db\Migration;

/**
 * Class m201127_180703_add_events
 */
class m201127_180703_add_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('event',[
            'id' => $this->primaryKey(),
            'event_type_id' => $this->integer()->notNull(),
            'created_at' => 'timestamp with time zone not null',
            'user_id' => $this->integer()->notNull(),
            'data' => $this->json()
        ]);

        $this->addForeignKey("fk-event-user", 'event', "user_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx-event-user', 'event', 'user_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('event');
    }

}
