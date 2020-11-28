<?php


namespace api\modules\v1\controllers;


use api\controllers\BaseApiController;
use api\modules\v1\models\search\OrdersSearch;
use common\models\Card;
use common\models\Department;
use common\models\Orders;
use common\models\TypeMessage;
use common\models\TypeOrder;
use Yii;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class OrdersController extends BaseApiController
{
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionView($id)
    {
        $order = OrdersSearch::view($id);
        if (!$order) {
            throw new NotFoundHttpException('Объект не найден');
        }

        return $order;
    }

    public function actionPerformers()
    {
        $user = Yii::$app->user;
        $department_id = $user->identity->userProfile->department_id;
        return Card::getDataByDepartment($department_id);
    }

    public function actionCreate()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;

        $order = new Orders();
        $order_data = Json::decode($request->post()['json_data']);
        $order->load($order_data, '');


        $profile = $user->identity->userProfile;
        $order->creator_id = $user->id;
        $order->updater_id = $user->id;
        $order->department_id = $profile->department_id;
        $order->status_id = Orders::STATUS_CREATED;
        $order->orderDocFiles = UploadedFile::getInstances($order, 'orderDocFiles');

        if ($order->saveOrders()) {
            if ($order->orderDocFiles){
                $order->saveOrdersDocFiles($order->orderDocFiles);
            }
            return true;
        } else {
            return $this->responseErrors($order->firstErrors);
        }
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;

        $order = Orders::findOne(['id' => $id]);
        if (!$order) throw new NotFoundHttpException('Не найдено');

        $order->load($request->post(), '');

        if ($order->saveOrders()) {
            return true;
        } else {
            return $this->responseErrors($order->firstErrors);
        }
    }
}