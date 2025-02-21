<?php

namespace app\controllers;

use app\models\Requests;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class ProcessorController extends Controller
{

    private const DEFAULT_DELAY = 5;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $behaviors['verbs'] = [
        'class' => VerbFilter::className(),
        'actions' => [
        'index' => ['GET'],
        ],
        ];
        return $behaviors;
    }

  /**
   * @inheritdoc
   */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update'], $actions['create'], $actions['view'], $actions['options']);
        return $actions;
    }

    public function actionIndex()
    {
        $delay = (int) Yii::$app->request->get('delay', self::DEFAULT_DELAY);
        $time = time();
        $last_processed_time = \Yii::$app->cache->get('processor_last_run_time') ?: 0;
        \Yii::$app->cache->set('processor_last_run_time', $time, 0);

        $subQuery = (new Query())
        ->select('user_id')
        ->from('requests')
        ->where(['status' => Requests::STATUS_APPROVED]);

      // Declining requests where already exists users with approved requests.
        $updatedQuantity = Yii::$app->db->createCommand()->update(
            'requests',
            ['status' => Requests::STATUS_DECLINED],
            [
            'and',
            ['in', 'user_id', $subQuery],
            ['=', 'status', Requests::STATUS_NOT_APPROVED],
            ['<', 'created_at', $time],
            ['>=', 'created_at', $last_processed_time],
            ]
        )->execute();

      // Processing not approved requests.
        $requests = Requests::find()->select(['id'])->where([
        'status' => Requests::STATUS_NOT_APPROVED,
        ])->andWhere(['<', 'created_at', $time])
        ->andWhere([
        '>=',
        'created_at',
        $last_processed_time,
        ])->all();

      // approve logic
        $approvedIds = [];
        $declinedIds = [];
        foreach ($requests as $request) {
            $randomNumber = mt_rand(1, 100);
            if ($randomNumber <= 10 && !isset($approvedIds[$request->user_id])) {
                $approvedIds[$request->user_id] = $request->id;
            } else {
                $declinedIds[] = $request->id;
            }
        }

      // Approve requests with 10% probability.
        $updatedRows = Yii::$app->db->createCommand()->update(
            'requests',
            ['status' => Requests::STATUS_APPROVED],
            [
            'and',
            ['in', 'id', $approvedIds],
            ['<', 'created_at', $time],
            ['>=', 'created_at', $last_processed_time],
            ]
        )->execute();
        $updatedQuantity += $updatedRows;

      // Decline requests with 90% probability.
        $updatedRows = Yii::$app->db->createCommand()->update(
            'requests',
            ['status' => Requests::STATUS_DECLINED],
            [
            'and',
            ['in', 'id', $declinedIds],
            ['<', 'created_at', $time],
            ['>=', 'created_at', $last_processed_time],
            ]
        )->execute();
        $updatedQuantity += $updatedRows;

      // sleep for each updated request.
        if ($updatedQuantity) {
            for ($i = 0; $i < $updatedQuantity; $i++) {
                sleep($delay);
            }
        }

        return [
        'result' => true,
        ];
    }
}
