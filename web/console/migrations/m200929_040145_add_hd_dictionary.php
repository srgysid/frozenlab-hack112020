<?php

use yii\db\Migration;

/**
 * Class m200929_040145_add_hd_dictionary
 */
class m200929_040145_add_hd_dictionary extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('card', [
            'id' => $this->primaryKey(),
            'stabnum' => $this->string(8),
            'firstname' => $this->string(50),
            'secondname' => $this->string(50),
            'thirdname' => $this->string(50),
        ]);

        $this->createTable('department', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'code' => $this->string(14),
            'short_code' => $this->string(6),
            'name' => $this->string(1024),
            'short_name' => $this->string(255),
            'full_name' => $this->string(1024),
            'begin' => $this->date(),
            'end' => $this->date(),
            'department_item_id' => $this->string(8),
        ]);

        $this->createTable('staffpos', [
            'id' => $this->primaryKey(),
            'staffpos_item_id' => $this->string(8),
            'name' => $this->string(40),
            'description' => $this->string(255),
            'begin' => $this->date(),
            'end' => $this->date(),

        ]);

        $this->createTable('movement', [
            'id' => $this->primaryKey(),
            'stabnum' => $this->string(8),
            'staffpos_item_id' => $this->string(8),
            'department_item_id' => $this->string(8),
            'begin' => $this->date(),
            'end' => $this->date(),
        ]);

        $this->addColumn('user_profile', 'card_id', $this->integer());
        $this->addForeignKey("fk_user_profile_card", 'user_profile', "card_id", "card", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_user_profile_card_id', 'user_profile', 'card_id');

        $this->addColumn('user_profile', 'department_id', $this->integer());
        $this->addForeignKey("fk_user_profile_department", 'user_profile', "department_id", "department", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_user_profile_department_id', 'user_profile', 'department_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_profile', 'card_id');

        $this->dropTable('movement');
        $this->dropTable('staffpos');
        $this->dropTable('department');
        $this->dropTable('card');
    }
}
