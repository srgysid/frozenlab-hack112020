<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 30.09.2020
 * Time: 10:08
 */

namespace console\controllers;

use common\models\TablesImport;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;


//yii sync-tables/sync  для консоли

class SyncTablesController extends Controller
{
    public function actionSync()
    {
        set_time_limit(600);
        $model = new TablesImport();
        $model->importCard = Yii::$app->db_hd->createCommand('
          SELECT  a.id,
                  a.stabnum,
                  a.secondname,
                  a.firstname,
                  a.thirdname
          FROM emp_card a
        ')->queryAll();

        $model->importDepartment = Yii::$app->db_hd->createCommand('
          SELECT  b.id,
                  b.parent_id,
                  b.code,
                  b.name,
                  b.full_name,
                  b.begin,
                  b.end,
                  b.department_item_id
          FROM emp_department b
        ')->queryAll();

        $model->importStaffpos = Yii::$app->db_hd->createCommand('
          SELECT  c.id,
                  c.staffpos_item_id,
                  c.name,
                  c.description,
                  c.begin,
                  c.end
          FROM emp_staffpos c
        ')->queryAll();

        $model->importMovement = Yii::$app->db_hd->createCommand('
          SELECT  d.id,
                  d.staffpos_item_id,
                  d.department_item_id,
                  d.stabnum,
                  d.begin,
                  d.end
          FROM emp_movement d
        ')->queryAll();

        $model->importToTable();

        $this->stdout("Успешно: {$model->countSuccess}, ошибок: {$model->countError}.\n", Console::BG_GREEN);
    }
}