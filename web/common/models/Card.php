<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;


/**
 * This is the model class for table "card".
 *
 * @property int $id
 * @property string|null $stabnum
 * @property string|null $firstname
 * @property string|null $secondname
 * @property string|null $thirdname
 *
 * @property UserProfile[] $userProfiles
 */
class Card extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stabnum'], 'string', 'max' => 8],
            [['firstname', 'secondname', 'thirdname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stabnum' => 'Табельный номер',
            'firstname' => 'Имя',
            'secondname' => 'Фамилия',
            'thirdname' => 'Отчество',
        ];
    }

    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['card_id' => 'id']);
    }

    public static function getEmployeeCard()
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $out = new Query();
        $out->addSelect([
            'card.id as id',
            new Expression("CONCAT(card.secondname, ' ', card.firstname, ' ', card.thirdname) as name"),
//            'card.stabnum',
//            'movement.id as movement_id',
//            'movement.end',
//            'department.id as department_id',
//            'department.code as department_code',
//            'department.short_code as department_short_code',
//            'department.end as department_end',
        ])->from('card')
            ->leftJoin('movement', 'movement.stabnum = card.stabnum')
            ->leftJoin('department', 'department.department_item_id = movement.department_item_id')
            ->leftJoin('staffpos', 'staffpos.staffpos_item_id = movement.staffpos_item_id')
            ->where(['>', 'movement.end', $dateEnd])
            ->andWhere(['>', 'department.end', $dateEnd])
            ->andWhere(['>', 'staffpos.end', $dateEnd])
            ->orderBy('name');
        return $out->all();
    }

    public static function getDataByCard($stubnum)
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $out = new Query();
        $out->addSelect([
            'staffpos.description as staffpos_description',
            'department.name as department_name',
            'department.full_name as department_full_name',
            new Expression("CONCAT(department.code, ' ', department.full_name) as department_code_full_name"),
        ])->from('movement')
            ->leftJoin('department', 'department.department_item_id = movement.department_item_id')
            ->leftJoin('staffpos', 'staffpos.staffpos_item_id = movement.staffpos_item_id')
            ->where(['like', 'movement.stabnum', $stubnum])
            ->andWhere(['>', 'movement.end', $dateEnd])
            ->andWhere(['>', 'department.end', $dateEnd])
            ->andWhere(['>', 'staffpos.end', $dateEnd]);
        return $out->one();
    }

    public static function getDataByDepartment($department_id)
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $arrId[] = $department_id;
        $departmentModels = Department::find()->where(['parent_id' => $department_id])->all();
        foreach ($departmentModels as $departmentModel) {
            $arrId[] = $departmentModel->id;
        }

        $out = new Query();
        $out->addSelect([
            'card.id as id',
            new Expression("CONCAT(card.secondname, ' ', card.firstname, ' ', card.thirdname) as name"),
        ])->from('card')
            ->leftJoin('movement', 'movement.stabnum = card.stabnum')
            ->leftJoin('department', 'department.department_item_id = movement.department_item_id')
            ->leftJoin('staffpos', 'staffpos.staffpos_item_id = movement.staffpos_item_id')
            ->where(['>', 'movement.end', $dateEnd])
            ->andWhere(['in', 'department.id', $arrId])
            ->andWhere(['>', 'department.end', $dateEnd])
            ->andWhere(['>', 'staffpos.end', $dateEnd])
            ->orderBy('name');

        return $out->all();
    }

    public function getFullName()
    {
        return trim($this->secondname.' '.$this->firstname.' '.$this->thirdname);
    }

    public function getShortName()
    {
        return trim($this->secondname.' '.substr($this->firstname,0,2).'. '.substr($this->thirdname,0,2).'.');
    }

}
