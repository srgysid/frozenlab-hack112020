<?php
namespace api\modules\v1\controllers;

use api\controllers\BaseApiController;
use api\modules\v1\models\LoginForm;
use common\models\Event;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

class LoginController extends BaseApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors = ArrayHelper::merge([
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'optional' => ['login'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['POST'],
                    'logout' => ['POST'],
                ],
            ]
        ], $behaviors);

        return $behaviors;
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();
        $loginForm->load(Yii::$app->request->post(), '');
        if ($loginForm->validate()) {
            $loginForm->registerLoginEvent();
            return [
                'access_token' => $loginForm->login()
            ];
        }

        return $this->responseErrors($loginForm->errors);
    }

    public function actionLogout()
    {
        $user = Yii::$app->user;
        if (!$user->isGuest) {
            $event = new Event([
                'event_type_id' => Event::EVENT_LOGOUT,
                'user_id' => $user->id,
            ]);
            $event->save();
            return true;
        }

        throw new ForbiddenHttpException('Access denied');
    }
}