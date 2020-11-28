<?php
namespace frontend\controllers;

use frontend\models\search\DepartmentSearch;
use common\models\Department;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap4\ActiveForm;

class DepartmentController extends Controller
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
//                    [
//                        'actions' => ['index', 'update', 'clear-filter'],
//                        'allow' => true,
//                        'roles' => ['rl_admin', 'rl_key_user'],
//                    ],
                    [
                        'actions' => ['index', 'view', 'clear-filter'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user', 'rl_key_dep_user', 'rl_admin_sec', 'rl_view_user'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['rl_admin', 'rl_key_user'],
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
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = $this->getCurrentUrl();
            return $this->redirect($url);
        }

        $this->setCurrentUrl();

        if (Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
        else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    private function findModel($id)
    {
        $model = Department::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Направление ППУ не найдено'));
        }
        return $model;
    }

    public function actionClearFilter()
    {
        $session = Yii::$app->session;
        if ($session->has('DepartmentSearch')) {
            $session->remove('DepartmentSearch');
        }
        if ($session->has('DepartmentSearchSort')) {
            $session->remove('DepartmentSearchSort');
        }

        return $this->redirect('index');
    }

    public function setCurrentUrl()
    {
        $session = Yii::$app->session;
        $session->set('DepartmentReferrer', Yii::$app->request->referrer);
    }

    public function getCurrentUrl()
    {
        $session = Yii::$app->session;
        if ($session['DepartmentReferrer'])
            return $session['DepartmentReferrer'];
        return 'index';
    }

}