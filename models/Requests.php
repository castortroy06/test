<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "requests".
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property int $term
 */
class Requests extends \yii\db\ActiveRecord
{

    public const STATUS_NOT_APPROVED = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_DECLINED = 2;

  /**
   * {@inheritdoc}
   */
    public static function tableName()
    {
        return 'requests';
    }

  /**
   * {@inheritdoc}
   */
    public function rules()
    {
        return [
        [['user_id', 'amount', 'term'], 'required'],
        [['user_id', 'amount', 'term'], 'default', 'value' => null],
        [['user_id', 'amount', 'term', 'status'], 'integer'],
        [['status'], 'default', 'value' => self::STATUS_NOT_APPROVED],
        ];
    }

  /**
   * {@inheritdoc}
   */
    public function fields()
    {
        return [
        'id',
        ];
    }

  /**
   * {@inheritdoc}
   */
    public function attributeLabels()
    {
        return [
        'id' => 'ID',
        'user_id' => 'User ID',
        'amount' => 'Amount',
        'term' => 'Term',
        'status' => 'Status'
        ];
    }
}
