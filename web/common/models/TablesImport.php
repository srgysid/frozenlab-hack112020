<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class TablesImport extends Model
{
    public $importCard;
    public $importDepartment;
    public $importStaffpos;
    public $importMovement;
    public $importSuccess;
    public $importError;
    public $errorsLog;
    public $countError;
    public $countSuccess;

    public function rules()
    {
        return [
            [['importCard', 'importDepartment', 'importStaffpos', 'importMovement', 'importSuccess', 'importError'], 'save'],
            ['errorsLog', 'string'],
            [['countError', 'countSuccess'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'importCard' => Yii::t('app', 'Массив карточек'),
            'importDepartment' => Yii::t('app', 'Массив подразделений'),
            'importStaffpos' => Yii::t('app', 'Массив профессий'),
            'importMovement' => Yii::t('app', 'Массив рабочих мест'),
            'errorsLog' => Yii::t('app', 'Log ошибок'),
        ];
    }

    public function importToTable()
    {
        $this->countError = 0;
        $this->countSuccess = 0;
        $this->importSuccess['card'] = 0;
        $this->importError['card'] = 0;
        $this->importSuccess['department'] = 0;
        $this->importError['department'] = 0;
        $this->importSuccess['staffpos'] = 0;
        $this->importError['staffpos'] = 0;
        $this->importSuccess['movement'] = 0;
        $this->importError['movement'] = 0;

        if (($this->importCard)&&(is_array($this->importCard))){
            foreach ($this->importCard as $indexRow => $rowCard){
                $modelCard = Card::findOne($rowCard['id']);
                if ($modelCard) {
                    $modelCard->stabnum = $rowCard['stabnum'];
                    $modelCard->firstname = $rowCard['firstname'];
                    $modelCard->secondname = $rowCard['secondname'];
                    $modelCard->thirdname = $rowCard['thirdname'];
                }
                else {
                    $modelCard = new Card();
                    $modelCard->id = $rowCard['id'];
                    $modelCard->stabnum = $rowCard['stabnum'];
                    $modelCard->firstname = $rowCard['firstname'];
                    $modelCard->secondname = $rowCard['secondname'];
                    $modelCard->thirdname = $rowCard['thirdname'];
                }
                if ($modelCard->save()) $this->importSuccess['card']++;
                else $this->importError['card']++;
            }
            $this->countError += $this->importError['card'];
            $this->countSuccess += $this->importSuccess['card'];
        }

        if (($this->importDepartment)&&(is_array($this->importDepartment))){
            foreach ($this->importDepartment as $indexRow => $rowDepartment){
                $tmp_code = substr($rowDepartment['code'], 2);
                $modelDepartment = Department::findOne($rowDepartment['id']);
                if ($modelDepartment) {
                    $modelDepartment->parent_id = $rowDepartment['parent_id'];
                    $modelDepartment->code = $tmp_code;
                    if ((substr($tmp_code, 0, 2)=='01') && (strlen($tmp_code)>4)) $modelDepartment->short_code = substr($tmp_code, 0, 5);
                    else $modelDepartment->short_code = substr($tmp_code, 0, 2);
                    $modelDepartment->name = $rowDepartment['name'];
                    if ($modelDepartment->full_name) $modelDepartment->full_name = $rowDepartment['full_name'];
                    if (!$modelDepartment->short_name) $modelDepartment->short_name = $rowDepartment['name'];
//                    $modelDepartment->full_name = $rowDepartment['full_name'];
                    $modelDepartment->begin = $rowDepartment['begin'];
                    $modelDepartment->end = $rowDepartment['end'];
                    $modelDepartment->department_item_id = $rowDepartment['department_item_id'];
                }
                else {
                    $modelDepartment = new Department();
                    $modelDepartment->id = $rowDepartment['id'];
                    $modelDepartment->parent_id = $rowDepartment['parent_id'];
                    $modelDepartment->code = $tmp_code;
                    if ((substr($tmp_code, 0, 2)=='01') && (strlen($tmp_code)>4)) $modelDepartment->short_code = substr($tmp_code, 0, 5);
                    else $modelDepartment->short_code = substr($tmp_code, 0, 2);
                    $modelDepartment->name = $rowDepartment['name'];
                    $modelDepartment->short_name = $rowDepartment['name'];
                    $modelDepartment->full_name = $rowDepartment['full_name'];
                    $modelDepartment->begin = $rowDepartment['begin'];
                    $modelDepartment->end = $rowDepartment['end'];
                    $modelDepartment->department_item_id = $rowDepartment['department_item_id'];
                }
                if ($modelDepartment->save()) $this->importSuccess['department']++;
                else $this->importError['department']++;
            }
            $this->countError += $this->importError['department'];
            $this->countSuccess += $this->importSuccess['department'];
        }

        if (($this->importStaffpos)&&(is_array($this->importStaffpos))){
            foreach ($this->importStaffpos as $indexRow => $rowStaffpos){
                $modelStaffpos = Staffpos::findOne($rowStaffpos['id']);
                if ($modelStaffpos) {
                    $modelStaffpos->staffpos_item_id = $rowStaffpos['staffpos_item_id'];
                    $modelStaffpos->description = $rowStaffpos['description'];
                    $modelStaffpos->begin = $rowStaffpos['begin'];
                    $modelStaffpos->end = $rowStaffpos['end'];
                }
                else {
                    $modelStaffpos = new Staffpos();
                    $modelStaffpos->id = $rowStaffpos['id'];
                    $modelStaffpos->staffpos_item_id = $rowStaffpos['staffpos_item_id'];
                    $modelStaffpos->description = $rowStaffpos['description'];
                    $modelStaffpos->begin = $rowStaffpos['begin'];
                    $modelStaffpos->end = $rowStaffpos['end'];
                }
                if ($modelStaffpos->save()) $this->importSuccess['staffpos']++;
                else $this->importError['staffpos']++;
            }
            $this->countError += $this->importError['staffpos'];
            $this->countSuccess += $this->importSuccess['staffpos'];
        }

        if (($this->importMovement)&&(is_array($this->importMovement))){
            foreach ($this->importMovement as $indexRow => $rowMovement){
                $modelMovement = Movement::findOne($rowMovement['id']);
                if ($modelMovement) {
                    $modelMovement->stabnum = $rowMovement['stabnum'];
                    $modelMovement->staffpos_item_id = $rowMovement['staffpos_item_id'];
                    $modelMovement->department_item_id = $rowMovement['department_item_id'];
                    $modelMovement->begin = $rowMovement['begin'];
                    $modelMovement->end = $rowMovement['end'];
                }
                else {
                    $modelMovement = new Movement();
                    $modelMovement->id = $rowMovement['id'];
                    $modelMovement->stabnum = $rowMovement['stabnum'];
                    $modelMovement->staffpos_item_id = $rowMovement['staffpos_item_id'];
                    $modelMovement->department_item_id = $rowMovement['department_item_id'];
                    $modelMovement->begin = $rowMovement['begin'];
                    $modelMovement->end = $rowMovement['end'];
                }
                if ($modelMovement->save()) $this->importSuccess['movement']++;
                else $this->importError['movement']++;
            }
            $this->countError += $this->importError['movement'];
            $this->countSuccess += $this->importSuccess['movement'];
        }
    }
}