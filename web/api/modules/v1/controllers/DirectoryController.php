<?php
namespace api\modules\v1\controllers;

use api\controllers\BaseApiController;
use api\modules\v1\models\search\DepartmentSearch;
use api\modules\v1\models\search\TypeMessageSearch;
use api\modules\v1\models\search\TypeOrderSearch;
use Yii;

class DirectoryController extends BaseApiController
{
    public function actionTypeOrder()
    {
        $searchModel = new TypeOrderSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionTypeMessage()
    {
        $searchModel = new TypeMessageSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionDepartment()
    {
        $searchModel = new DepartmentSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
}