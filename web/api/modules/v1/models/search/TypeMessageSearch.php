<?php

namespace api\modules\v1\models\search;

use common\models\TypeMessage;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class TypeMessageSearch extends TypeMessage
{
    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['type_order_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = new Query();
        $query->addSelect([
            'type_message.id',
            'type_message.type_order_id',
            'type_message.name',
        ])->from('type_message')
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

        $query->andFilterWhere(['=', 'type_message.type_order_id', $this->type_order_id]);
        $query->andFilterWhere(['ilike', 'type_message.name', $this->name]);

        return $dataProvider;
    }

}