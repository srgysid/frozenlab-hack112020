<?php

namespace frontend\models\search;

use common\models\Department;
use common\models\ActiveDataProviderPpu;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class DepartmentSearch extends Department
{
    public function rules()
    {
        return [
            [['code','name','short_name','full_name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $session = Yii::$app->session;

        if (!isset($params['DepartmentSearch'])) {
            if ($session->has('DepartmentSearch')){
                $params['DepartmentSearch'] = $session['DepartmentSearch'];
            }
        }
        else{
            $session->set('DepartmentSearch', $params['DepartmentSearch']);
        }

        if (!isset($params['sort'])) {
            if ($session->has('DepartmentSearchSort')){
                $params['sort'] = $session['DepartmentSearchSort'];
            }
        }
        else{
            $session->set('DepartmentSearchSort', $params['sort']);
        }

        if (isset($params["sort"])) {
            $pos = stripos($params["sort"], '-');
            if ($pos !== false) {
                $typeSort = SORT_DESC;
                $fieldSort = substr($params["sort"], 1);
            } else {
                $typeSort = SORT_ASC;
                $fieldSort = $params["sort"];
            }
        }
        else {
            $typeSort = SORT_ASC;
            $fieldSort = 'code';
        }

        $today = new \DateTime();
        $today->modify('+1 year');
        $dateEnd = $today->format('Y-m-d');
        $subParams = [':today' => $dateEnd];

//        $subQuery = Yii::$app->db->createCommand('
//          SELECT  a.id
//          FROM department a
//          WHERE a.code = a.short_code
//          and  a.end > :today
//        ')->bindValues($subParams)->queryAll();
        $subQuery = Yii::$app->db->createCommand('
          SELECT  a.id
          FROM department a
          WHERE a.end > :today
        ')->bindValues($subParams)->queryAll();

        $subQuery = ArrayHelper::getColumn($subQuery,'id');

        $query = new Query();
        $query->addSelect([
            'department.id',
            'department.code',
            'department.name',
            'department.short_name',
            'department.full_name',
        ])->from('department')
            ->where(['department.id'=>$subQuery])
        ;

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProviderPpu([
            'query' => $query,
        ]);

        $dataProvider->key = 'id';

        $dataProvider->setSort([
            'defaultOrder' => [$fieldSort => $typeSort],
            'attributes' => [
                'id',
                'code',
                'name',
                'short_name',
                'full_name',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'department.code', $this->code]);
        $query->andFilterWhere(['like', 'department.name', $this->name]);
        $query->andFilterWhere(['like', 'department.short_name', $this->short_name]);
        $query->andFilterWhere(['like', 'department.full_name', $this->full_name]);

        return $dataProvider;
    }

}