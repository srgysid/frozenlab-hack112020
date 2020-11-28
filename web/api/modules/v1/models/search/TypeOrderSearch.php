<?php

namespace api\modules\v1\models\search;

use common\models\TypeOrder;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class TypeOrderSearch extends TypeOrder
{
    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = new Query();
        $query->addSelect([
            'type_order.id',
            'type_order.name',
        ])->from('type_order')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->key = 'id';

        $dataProvider->setSort([
            'defaultOrder' => ['name' => SORT_ASC],
            'attributes' => [
                'id',
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

        $query->andFilterWhere(['ilike', 'type_order.name', $this->name]);

        return $dataProvider;
    }

}