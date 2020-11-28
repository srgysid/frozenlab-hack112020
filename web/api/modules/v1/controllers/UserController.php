<?php
namespace api\modules\v1\controllers;

use api\controllers\BaseApiController;
use api\modules\v1\models\search\TypeGroupSearch;
use api\modules\v1\models\search\UserSearch;
use common\models\UserFcm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class UserController extends BaseApiController
{
//    public function actionIndex()
//    {
//        $user = Yii::$app->user;
//        if (!$user->identity['is_admin']) {
//            throw new ForbiddenHttpException('Access denied');
//        }
//        $searchModel = new UserSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        if ($searchModel->errors) {
//            return $this->responseErrors($searchModel->errors);
//        }
//        return $dataProvider;
//    }

    public function actionProfile()
    {
        $user = Yii::$app->user;
        $profile = $user->identity->userProfile;

        $user_attributes = array_intersect_key($user->identity->attributes, array_flip([
            'email',
        ]));

        $profile_attributes = array_intersect_key($profile->attributes, array_flip([
            'first_name', 'second_name', 'third_name', 'phone',
        ]));

        return ArrayHelper::merge($user_attributes, $profile_attributes);
    }

    /**
     * Register FCM token
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionFcm()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;

        $old_token = $request->post('old_token', null);
        $new_token = $request->post('new_token', null);
        $app_id = $request->post('app_id', null);

        if (!$app_id) {
            throw new BadRequestHttpException('app_id не указан');
        }

        if (!in_array($app_id, [UserFcm::APP_ID_USER])) {
            throw new BadRequestHttpException('Указанного app_id не существует');
        }

        return UserFcm::replaceFcm($user->id, $app_id, $old_token, $new_token);
    }
}