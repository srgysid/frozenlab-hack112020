<?php

use yii\db\Migration;

/**
 * Class m201128_105128_add_orders_column
 */
class m201128_105128_add_orders_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders', 'status_id', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('orders', 'status_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201128_105128_add_orders_column cannot be reverted.\n";

        return false;
    }
    */
}
