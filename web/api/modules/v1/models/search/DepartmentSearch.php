<?php

namespace api\modules\v1\models\search;

use common\models\Department;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class DepartmentSearch extends Department
{
    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['name', 'code'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = new Query();
        $query->addSelect([
            'department.id',
            'department.parent_id',
            'department.code',
            'department.short_code',
            'department.name',
            'department.short_name',
        ])->from('department')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->key = 'id';

        $dataProvider->setSort([
            'defaultOrder' => ['code' => SORT_ASC],
            'attributes' => [
                'id',
                'code',
                'name',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->andWhere('1=0');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['ilike', 'department.name', $this->name]);
        $query->andFilterWhere(['ilike', 'department.code', $this->code]);

        return $dataProvider;
    }

}