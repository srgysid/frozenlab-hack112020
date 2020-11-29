<?php
namespace frontend\controllers;

use frontend\models\search\AnswersSearch;
use common\models\Answers;
use common\models\Orders;
use common\models\OrdersFiles;
use common\models\Department;
use common\models\Card;
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


class AnswersController extends Controller
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
                        'actions' => ['index', 'view', 'clear-filter', 'get-single-file', 'index-order'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user', 'rl_chief', 'rl_view_user'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'update-answers-doc-files', 'delete-answers-doc-file', 'download-file'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user', 'rl_view_user'],
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
        $searchModel = new AnswersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $status = Answers::getStatus();
        $cards = ArrayHelper::map(Card::getEmployeeCard(), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'cards' => $cards,
        ]);
    }

    public function actionCreate()
    {
        $model = new Answers();
        $user = Yii::$app->user;
        $model->creator_id = $user->id;
        $model->updater_id = $user->id;
        $model->status_id = Answers::STATUS_VIEW;

        $uploadFiles = [];
        $filePath = '';
//        $orders = ArrayHelper::map(Orders::find()->orderBy('created_at')->all(), 'id', 'short_desc');
        $orders = ArrayHelper::map(Orders::getOrdersList(), 'id', 'description');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createAnswersDocFiles($model->id);
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }
        $this->setCurrentUrl();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'orders' => $orders,
            ]);
        }
        else{
            return $this->render('_form', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'orders' => $orders,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->setCurrentUrl();

        $user = Yii::$app->user;
        $model->updater_id = $user->id;

        $uploadFiles = OrdersFiles::find()->where(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ANSWER])->orderBy('original_name')->all();
        $filePath = $model->getUploadPath();

        $orders = ArrayHelper::map(Orders::getOrdersList(), 'id', 'description');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->createAnswersDocFiles($model->id);
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'orders' => $orders,
            ]);
        }
        else {
            return $this->render('_form', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
                'orders' => $orders,
            ]);
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $uploadFiles = OrdersFiles::find()->where(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ANSWER])->orderBy('original_name')->all();
        $filePath = $model->getUploadPath();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('view', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
            ]);
        }
        else {
            return $this->render('view', [
                'model' => $model,
                'uploadFiles' => $uploadFiles,
                'filePath' => $filePath,
            ]);
        }
    }

    public function actionIndexOrder($orders_id)
    {
        $searchModel = new AnswersSearch();
        $dataProvider = $searchModel->searchByOrder(Yii::$app->request->queryParams, $orders_id);

        $cards = ArrayHelper::map(Card::getEmployeeCard(), 'id', 'name');
        $status = Answers::getStatus();
        return $this->render('index_order', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'cards' => $cards,
        ]);
    }

    private function findModel($id)
    {
        $model = Answers::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Распоряжение не найдено'));
        }
        return $model;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->setCurrentUrl();

        $tmpDir = $model->getUploadPath();
        BaseFileHelper::removeDirectory($tmpDir);
        OrdersFiles::deleteAll(['source_id' => $id, 'type_source' => OrdersFiles::SOURCE_ANSWER]);

        $model->delete();
        $url = $this->getCurrentUrl();
        return $this->redirect($url);
    }

    public function actionClearFilter()
    {
        $session = Yii::$app->session;
        if ($session->has('AnswersSearch')) {
            $session->remove('AnswersSearch');
        }
        if ($session->has('AnswersSearchSort')) {
            $session->remove('AnswersSearchSort');
        }

        return $this->redirect('index');
    }

    public function setCurrentUrl()
    {
        $session = Yii::$app->session;
        $session->set('AnswersReferrer', Yii::$app->request->referrer);
    }

    public function getCurrentUrl()
    {
        $session = Yii::$app->session;
        if ($session['AnswersReferrer'])
            return $session['AnswersReferrer'];
        return 'index';
    }

// ****************************************************
// ***************** Работа с файлами *****************
// ****************************************************

    public function createAnswersDocFiles($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelDocFiles = $this->findModel($id);

        if ($modelDocFiles->load(Yii::$app->request->post())) {
            $modelDocFiles->answerDocFiles = UploadedFile::getInstances($modelDocFiles, 'answerDocFiles');

            if ($modelDocFiles->answerDocFiles){
                $modelDocFiles->saveAnswersDocFiles($modelDocFiles->answerDocFiles);
            }
            return ['error' => null];
        }
        return ['error' => null];
    }

    public function actionDeleteAnswersDocFile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $file_id = Yii::$app->request->post('key');

        $file = OrdersFiles::findOne($file_id);

        if (!$file) {
            throw new HttpException(404, Yii::t('app', 'Файл не найден'));
        }

        $answers_id = $file->getSourceId();
        $uploadDir = $file->getUploadDir($answers_id, OrdersFiles::SOURCE_ANSWER);
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
            $answers_id = $file->getSourceId();
            $uploadDir = $file->getUploadDir($answers_id, OrdersFiles::SOURCE_ANSWER);
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

}