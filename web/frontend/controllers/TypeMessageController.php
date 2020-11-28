<?php
namespace frontend\controllers;

use frontend\models\search\TypeMessageSearch;
use common\models\TypeMessage;
use common\models\TypeOrder;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\bootstrap4\ActiveForm;

class TypeMessageController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'clear-filter'],
                        'allow' => true,
                        'roles' => ['rl_admin','rl_key_user'],
                    ]
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
        $searchModel = new TypeMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $typeOrder = ArrayHelper::map(TypeOrder::find()->orderBy('name')->all(), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeOrder' => $typeOrder,
        ]);
    }

    public function actionCreate()
    {
        $model = new TypeMessage();
        $typeOrder = ArrayHelper::map(TypeOrder::find()->orderBy('name')->all(), 'id', 'name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
//            return $this->redirect(['index']);
        }
        $this->setCurrentUrl();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'typeOrder' => $typeOrder,
            ]);
        }
        else{
            return $this->render('_form', [
                'model' => $model,
                'typeOrder' => $typeOrder,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $typeOrder = ArrayHelper::map(TypeOrder::find()->orderBy('name')->all(), 'id', 'name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
//            return $this->redirect(['index']);
        }
        $this->setCurrentUrl();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'typeOrder' => $typeOrder,
            ]);
        }
        else{
            return $this->render('_form', [
                'model' => $model,
                'typeOrder' => $typeOrder,
            ]);
        }
    }

    private function findModel($id)
    {
        $model = TypeMessage::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Сообщение не найдено'));
        }

        return $model;
    }

    public function actionDelete($id)
    {
        $this->setCurrentUrl();
        $this->findModel($id)->delete();

        $url = $this->getCurrentUrl();
        return $this->redirect($url);
//        return $this->redirect(['index']);
    }

    public function actionClearFilter()
    {
        $session = Yii::$app->session;
        if ($session->has('TypeMessageSearch')) {
            $session->remove('TypeMessageSearch');
        }
        if ($session->has('TypeMessageSearchSort')) {
            $session->remove('TypeMessageSearchSort');
        }

        return $this->redirect('index');
    }

//    public function actionTypeOrderList()
//    {
//        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        $out = [];
//        if (isset($_POST['depdrop_parents'])) {
//            $parents = $_POST['depdrop_parents'];
//            if ($parents != null) {
//                $type_order_id = $parents[0];
//                if ($type_order_id) {
//                    $out = TypeMessage::getTypeMessageByTypeOrder($type_order_id);
//                }
//                return ['output' => $out, 'selected' => ''];
//            }
//        }
//        return ['output' => '', 'selected' => ''];
//    }

    public function setCurrentUrl()
    {
        $session = Yii::$app->session;
        $session->set('TypeMessageReferrer', Yii::$app->request->referrer);
    }

    public function getCurrentUrl()
    {
        $session = Yii::$app->session;
        if ($session['TypeMessageReferrer'])
            return $session['TypeMessageReferrer'];
        return 'index';
    }

}