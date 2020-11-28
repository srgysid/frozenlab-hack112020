<?php
namespace frontend\controllers;

use common\models\OrdersPerformer;
use frontend\models\search\OrdersSearch;
use common\models\Orders;
use common\models\OrdersFiles;
use common\models\Department;
use common\models\Card;
use common\models\TypeOrder;
use common\models\TypeMessage;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap4\ActiveForm;

use Mpdf\Mpdf;

use yii\helpers\BaseFileHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;


class OrdersController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'clear-filter', 'get-single-file'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user', 'rl_chief', 'rl_view_user'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'update-orders-doc-files', 'delete-orders-doc-file', 'download-file', 'type-order-list', 'change-department'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user', 'rl_chief'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $departments = ArrayHelper::map(Department::getDepartmentCodeName(), 'id', 'code_name');

        $type_cards = Orders::getTypeCards();
        $priority = Orders::getPriority();
        $status = Orders::getStatus();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departments' => $departments,
            'type_cards' => $type_cards,
            'priority' => $priority,
            'status' => $status,
        ]);
    }

    public function actionCreate()
    {
        $model = new Orders();
        $user = Yii::$app->user;
        $profile = $user->identity->userProfile;
        $model->creator_id = $user->id;
        $model->updater_id = $user->id;
        $model->department_id = $profile->department_id;
        $model->status_id = Orders::STATUS_CREATED;

        $uploadFiles = [];
        $filePath = '';

//        $cards = ArrayHelper::map(Card::getEmployeeCard(), 'id', 'name');
        $cards = ArrayHelper::map(Card::getDataByDepartment($model->department_id), 'id', 'name');
        $typeOrder = ArrayHelper::map(TypeOrder::find()->orderBy('name')->all(), 'id', 'name');
        $typeMessage = ArrayHelper::map(TypeMessage::getTypeMessageByTypeOrder(null), 'id', 'name');
        $departments = ArrayHelper::map(Department::getDepartmentCodeName(), 'id', 'full_code_name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->saveOrders()) {
            $this->createOrdersDocFiles($model->id);
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }
        $this->setCurrentUrl();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'cards' => $cards,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'typeOrder' => $typeOrder,
                'typeMessage' => $typeMessage,
                'departments' => $departments,
            ]);
        }
        else{
            return $this->render('_form', [
                'model' => $model,
                'cards' => $cards,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'typeOrder' => $typeOrder,
                'typeMessage' => $typeMessage,
                'departments' => $departments,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->setCurrentUrl();

        $user = Yii::$app->user;
        $profile = $user->identity->userProfile;
        $model->updater_id = $user->id;

        $model->performer_ids = OrdersPerformer::getOrdersPerformerByOrdersId($id);

        $modelTypeMessage = TypeMessage::findOne($model->type_message_id);
        $model->typeOrder_id = $modelTypeMessage->type_order_id;

        $uploadFiles = OrdersFiles::find()->where(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ORDERS])->orderBy('original_name')->all();
        $filePath = $model->getUploadPath();

//        $cards = ArrayHelper::map(Card::getEmployeeCard(), 'id', 'name');
        $cards = ArrayHelper::map(Card::getDataByDepartment($model->department_id), 'id', 'name');
        $typeOrder = ArrayHelper::map(TypeOrder::find()->orderBy('name')->all(), 'id', 'name');
        $typeMessage = ArrayHelper::map(TypeMessage::getTypeMessageByTypeOrder($model->typeOrder_id), 'id', 'name');
        $departments = ArrayHelper::map(Department::getDepartmentCodeName(), 'id', 'full_code_name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->saveOrders()) {
            $this->createOrdersDocFiles($model->id);
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'cards' => $cards,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'typeOrder' => $typeOrder,
                'typeMessage' => $typeMessage,
                'departments' => $departments,
            ]);
        }
        else {
            return $this->render('_form', [
                'model' => $model,
                'cards' => $cards,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'typeOrder' => $typeOrder,
                'typeMessage' => $typeMessage,
                'departments' => $departments,
            ]);
        }
    }

    public function actionChangeDepartment($id)
    {
        $model = $this->findModel($id);
        $this->setCurrentUrl();

        $user = Yii::$app->user;
        $profile = $user->identity->userProfile;
        $model->updater_id = $user->id;

        $departments = ArrayHelper::map(Department::getDepartmentCodeName(), 'id', 'full_code_name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('change_dep', [
                'model' => $model,
                'departments' => $departments,
            ]);
        }
        else {
            return $this->render('change_dep', [
                'model' => $model,
                'departments' => $departments,
            ]);
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $modelOrdersPerformers = OrdersPerformer::find()->where(['orders_id' => $id])->all();

        $uploadFiles = OrdersFiles::find()->where(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ORDERS])->orderBy('original_name')->all();
        $filePath = $model->getUploadPath();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('view', [
                'model' => $model,
                'modelOrdersPerformers' => $modelOrdersPerformers,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
            ]);
        }
        else {
            return $this->render('view', [
                'model' => $model,
                'modelOrdersPerformers' => $modelOrdersPerformers,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
            ]);
        }
    }

    private function findModel($id)
    {
        $model = Orders::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Распоряжение не найдено'));
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->setCurrentUrl();

        $ordersPerformer = OrdersPerformer::find()->where(['orders_id' => $id])->all();
        if ($ordersPerformer){
            OrdersPerformer::deleteAll(['orders_id' => $id]);
        }

        $tmpDir = $model->getUploadPath();
        BaseFileHelper::removeDirectory($tmpDir);
        OrdersFiles::deleteAll(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ORDERS]);

        $model->delete();
        $url = $this->getCurrentUrl();
        return $this->redirect($url);
    }

    public function actionClearFilter()
    {
        $session = Yii::$app->session;
        if ($session->has('OrdersSearch')) {
            $session->remove('OrdersSearch');
        }
        if ($session->has('OrdersSearchSort')) {
            $session->remove('OrdersSearchSort');
        }

        return $this->redirect('index');
    }

    public function setCurrentUrl()
    {
        $session = Yii::$app->session;
        $session->set('OrdersReferrer', Yii::$app->request->referrer);
    }

    public function getCurrentUrl()
    {
        $session = Yii::$app->session;
        if ($session['OrdersReferrer'])
            return $session['OrdersReferrer'];
        return 'index';
    }

// ****************************************************
// ***************** Работа с файлами *****************
// ****************************************************

    public function createOrdersDocFiles($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelDocFiles = $this->findModel($id);

        if ($modelDocFiles->load(Yii::$app->request->post())) {
            $modelDocFiles->orderDocFiles = UploadedFile::getInstances($modelDocFiles, 'orderDocFiles');

            if ($modelDocFiles->orderDocFiles){
                $modelDocFiles->saveOrdersDocFiles($modelDocFiles->orderDocFiles);
            }
            return ['error' => null];
        }
        return ['error' => null];
    }

    public function actionDeleteOrdersDocFile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $file_id = Yii::$app->request->post('key');

        $file = OrdersFiles::findOne($file_id);

        if (!$file) {
            throw new HttpException(404, Yii::t('app', 'Файл не найден'));
        }

        $orders_id = $file->getSourceId();
        $uploadDir = $file->getUploadDir($orders_id, OrdersFiles::SOURCE_ORDERS);
        $filePath = $file->getPath($uploadDir);

        if (OrdersFiles::deleteUploadFile($filePath)) {
            $file->delete();
        }
        else {
            Yii::$app->getSession()->addFlash('error', Yii::t('app', 'Ошибка при удалении файла'));
        }
    }

    public function actionGetSingleFile($filePath, $fileName) {

        if (!file_exists($filePath)) {
            throw new HttpException(404, Yii::t('app','Файл не найден'));
        }
        \Yii::$app->response->sendFile($filePath, $fileName, ['inline' => true]);
    }

    public function actionDownloadFile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $file_id = Yii::$app->request->get('key');

        if ($file_id) {
            $file = OrdersFiles::findOne($file_id);
            $orders_id = $file->getSourceId();
            $uploadDir = $file->getUploadDir($orders_id, OrdersFiles::SOURCE_ORDERS);
            $filePath = $file->getPath($uploadDir);

            if (!file_exists($filePath)) {
                throw new HttpException(404, Yii::t('app','Файл не найден'));
            }
            else {
                \Yii::$app->response->sendFile($filePath, $file['original_name']);
            }
        }
        else {
            throw new HttpException(404, Yii::t('app','Ошибка загрузки'));
        }
    }

// ********************************************************
// ***************** END Работа с файлами *****************
// ********************************************************

    public function actionTypeOrderList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $type_order_id = $parents[0];
                if ($type_order_id) {
                    $out = TypeMessage::getTypeMessageByTypeOrder($type_order_id);
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

}