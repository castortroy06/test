<?php

use yii\db\Migration;

class m250219_070219_requests_table_create extends Migration
{

  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        $this->createTable('requests', [
        'id' => $this->primaryKey(),
        'user_id' => $this->integer()->notNull(),
        'amount' => $this->integer()->notNull(),
        'term' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-requests-user_id', 'requests', 'user_id');
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        $this->dropForeignKey('fk-requests-user_id', 'requests');
        $this->dropTable('requests');
    }
}
