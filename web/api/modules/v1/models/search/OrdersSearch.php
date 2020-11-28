<?php

namespace api\modules\v1\models\search;

use common\models\Orders;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class OrdersSearch extends Orders
{
    const FLOW_TYPE_ID_INCOMING = 1;
    const FLOW_TYPE_ID_OUTGOING = 2;

    public $flow_type_id;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['status_id', 'flow_type_id'], 'integer'],
        ];
    }

    public function search($params)
    {
        $user = Yii::$app->user;

        $query = new Query();
        $query->addSelect([
            'orders.id',
            'orders.type_cards',
            'orders.type_performers',
            'orders.required_date',
            'orders.fact_date',
            'orders.created_at',
            'orders.priority',
            'type_message.name as type_message_name',
            'type_order.name as type_order_name',
        ])->from('orders')
            ->leftJoin('type_message', 'type_message.id = orders.type_message_id')
            ->leftJoin('type_order', 'type_order.id = type_message.type_order_id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->key = 'id';

        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'created_at',
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->andWhere('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere(['=', 'status_id', $this->status_id]);

        $card_id = $user->identity->userProfile->card_id;

        switch ($this->flow_type_id) {
            case self::FLOW_TYPE_ID_INCOMING:
                // входящие распоряжения (в которых сотрудник исполнитель)
                $query->andWhere(new Expression("
                EXISTS (
                    select 1 
                    from orders_performer 
                    where orders_performer.orders_id = orders.id
                          and orders_performer.card_id = :card_id
                )", ['card_id' => $card_id]));
                break;
            case self::FLOW_TYPE_ID_OUTGOING:
                // исходящие (в которых сотрудник создатель)
                $query->andWhere(['=', 'orders.creator_id', $user->id]);
                break;

        }
//        var_dump($query->createCommand()->rawSql); exit;
        return $dataProvider;
    }

    public static function view($orders_id)
    {
        $query = new Query();
        $query->addSelect([
            'orders.id',
            'orders.type_cards',
            'orders.type_performers',
            'orders.required_date',
            'orders.fact_date',
            'orders.created_at',
            'orders.short_desc',
            'orders.full_desc',
            'orders.closed_at',
            'orders.reaction',
            'orders.priority',
            'department.code as department_code',
            'department.short_code as department_short_code',
            'department.name as department_name',
            'department.short_name as department_short_name',
        ])->from('orders')
            ->leftJoin('type_message', 'type_message.id = orders.type_message_id')
            ->leftJoin('type_order', 'type_order.id = type_message.type_order_id')
            ->leftJoin('department', 'department.id = orders.department_id')
        ;

        $query->andWhere(['=', 'orders.id', $orders_id]);

        return $query->one();
    }
}