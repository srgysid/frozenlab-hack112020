<?php

use yii\db\Migration;

/**
 * Class m201128_122219_update_status_id
 */
class m201128_122219_update_status_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('orders', ['status_id' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('orders', ['status_id' => null]);
    }

}
