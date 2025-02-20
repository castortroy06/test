<?php

use yii\db\Migration;
use app\models\Requests;

class m250220_045224_requests_add_status_field extends Migration
{

  /**
   * {@inheritdoc}
   */
    public function safeUp()
    {
        $this->addColumn('requests', 'status', $this->tinyInteger()
        ->notNull()
        ->defaultValue(Requests::STATUS_NOT_APPROVED));
    }

  /**
   * {@inheritdoc}
   */
    public function safeDown()
    {
        $this->dropColumn('requests', 'status');
    }
}
