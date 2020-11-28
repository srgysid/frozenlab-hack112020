<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "department".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $full_name
 * @property string|null $begin
 * @property string|null $end
 * @property string|null $department_item_id
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['begin', 'end'], 'safe'],
            [['code'], 'string', 'max' => 14],
            [['short_code'], 'string', 'max' => 6],
            [['name', 'full_name'], 'string', 'max' => 900],
            [['short_name'], 'string', 'max' => 255],
            [['department_item_id'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'code' => 'Код',
            'name' => 'Наименование SAP',
            'full_name' => 'Полное наименование',
            'short_name' => 'Краткое наименование',
            'begin' => 'Begin',
            'end' => 'End',
            'department_item_id' => 'Department Item ID',
        ];
    }

    public static function getDepartmentShortCodeName()
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $params = [':today' => $dateEnd];
        $ret = Yii::$app->db->createCommand('
          SELECT  a.id,
                  concat(a.code,chr(32),a.short_name) as code_name,
                  a.code, 
                  a.name
          FROM department a
          WHERE a.code = a.short_code
          and  a.end > :today
          order by a.code
        ')->bindValues($params)->queryAll();
        return $ret;
    }

    public static function getDepartmentCodeName()
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $params = [':today' => $dateEnd];
        $ret = Yii::$app->db->createCommand('
          SELECT  a.id,
                  concat(a.code,chr(32),a.short_name) as code_name,
                  concat(a.code,chr(32),a.full_name) as full_code_name,
                  a.code, 
                  a.name
          FROM department a
          WHERE a.end > :today
          order by a.code
        ')->bindValues($params)->queryAll();
        return $ret;
    }


    public static function getDepartmentByCardId($card_id)
    {
        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');

        $out = new Query();
        $out->addSelect([
            'department.id as department_id',
            'department.parent_id as department_parent_id',
        ])->from('card')
            ->leftJoin('movement', 'movement.stabnum = card.stabnum')
            ->leftJoin('department', 'department.department_item_id = movement.department_item_id')
            ->leftJoin('staffpos', 'staffpos.staffpos_item_id = movement.staffpos_item_id')
            ->where(['card.id'=>$card_id])
            ->andWhere(['>', 'movement.end', $dateEnd])
            ->andWhere(['>', 'department.end', $dateEnd])
            ->andWhere(['>', 'staffpos.end', $dateEnd]);

        $currentDepartment = $out->one();
//        if ($currentDepartment['department_parent_id']) $signWhile = true;
//
//        while ($signWhile){
//            $outDep = new Query();
//            $outDep->addSelect([
//                'department.id as department_id',
//                'department.parent_id as department_parent_id',
//                'department.code as department_code',
//                'department.short_code as department_short_code',
//                'department.name as department_name',
//                'department.short_name as department_short_name',
//                'department.full_name as department_full_name',
//            ])->from('department')
//                ->where(['department.id' => $currentDepartment['department_parent_id']]);
//            $currentDepartment = $outDep->one();
//            if ($currentDepartment['department_code']===$currentDepartment['department_short_code']) $signWhile = false;
//        }

        return $currentDepartment;
    }

    public function getShortCodeShortName()
    {
//        return trim($this->short_code.' '.$this->short_name);
        return trim($this->code.' '.$this->short_name);
    }

//    public static function getDepartmentTree($department_id)
//    {
//        $today = new \DateTime();
//        $today->modify('+1 year');
//        $dateEnd = $today->format('Y-m-d');
//
//        $params = [':today' => $dateEnd, ':tmpId' => $department_id];
//        $ret = Yii::$app->db->createCommand('
//
//        WITH RECURSIVE temp(id, link, data, depth, path, cycle) AS (
//            SELECT g.id, g.link, g.data, 1,
//                    ARRAY[g.id],
//                    false
//            FROM graph g
//            UNION ALL
//            SELECT g.id, g.link, g.data, sg.depth + 1,
//                    path || g.id,
//                    g.id = ANY(path)
//            FROM graph g, search_graph sg
//            WHERE g.id = sg.link AND NOT cycle
//            )
//        SELECT * FROM temp;
//
//        ')->bindValues($params)->queryOne();
//
//        return $ret;
//    }

}
