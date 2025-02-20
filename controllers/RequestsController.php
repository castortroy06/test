<?php

namespace app\controllers;

use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\Response;
use app\models\Requests;

class RequestsController extends ActiveController
{

    public $modelClass = 'app\models\Requests';

  /**
   * @inheritdoc
   */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        $behaviors['corsFilter'] = [
        'class' => Cors::className(),
        ];

        $behaviors['verbs'] = [
        'class' => VerbFilter::className(),
        'actions' => [
        'create' => ['POST'],
        ],
        ];

        return $behaviors;
    }

  /**
   * @inheritdoc
   */
    public function actions()
    {
        return [];
    }

  /**
   * @inheritdoc
   */
    public function actionCreate()
    {
        $model = new $this->modelClass();
        $params = \Yii::$app->getRequest()->getBodyParams();

        if (
            is_numeric($params['user_id'])
            && Requests::find()->where([
            'status' => Requests::STATUS_APPROVED,
            'user_id' => $params['user_id'],
            ])->exists()
        ) {
            \Yii::$app->getResponse()
            ->setStatusCode(400, 'Request already approved for this user.');
        } else {
            $model->load($params, '');
            $model->status = Requests::STATUS_NOT_APPROVED;

            if ($model->save()) {
                $data = $model->toArray();
                $data['result'] = true;
                return $data;
            } elseif ($model->hasErrors()) {
                \Yii::$app->getResponse()
                ->setStatusCode(400, 'Validation error');
            }
        }
        return ['result' => false];
    }
}
