<?php

namespace frontend\models\search;

use common\models\Orders;
use common\models\ActiveDataProviderPpu;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;


class OrdersSearch extends Orders
{
    public $department_ids;
    public $type_cards_ids;
    public $priority_ids;
    public $type_message_ids;
    public $creator_ids;
    public $status_ids;

    public function rules()
    {
        return [
            [['department_ids', 'type_cards_ids', 'priority_ids', 'type_message_ids', 'creator_ids', 'short_desc', 'status_ids'], 'safe'],
        ];
    }

    public function search($params)
    {
        $session = Yii::$app->session;

        if (!isset($params['OrdersSearch'])) {
            if ($session->has('OrdersSearch')){
                $params['OrdersSearch'] = $session['OrdersSearch'];
            }
        }
        else{
            $session->set('OrdersSearch', $params['OrdersSearch']);
        }

        if (!isset($params['sort'])) {
            if ($session->has('OrdersSearchSort')){
                $params['sort'] = $session['OrdersSearchSort'];
            }
        }
        else{
            $session->set('OrdersSearchSort', $params['sort']);
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
            $typeSort = SORT_DESC;
            $fieldSort = 'created_at';
        }

        $query = new Query();
        $query->addSelect([
            'orders.id',
            'orders.type_cards',
            'orders.type_performers',
            'orders.priority',
            'orders.short_desc',
            'orders.created_at',
            'orders.status_id',
            new Expression("concat(card_creator.secondname, ' ', card_creator.firstname, ' ', card_creator.thirdname) as creator_name"),
            new Expression("concat(department.code, ' ', department.short_name) as department_name"),
        ])->from('orders')
            ->leftJoin('user_profile as user_profile_creator', 'user_profile_creator.user_id = orders.creator_id')
            ->leftJoin('card as card_creator', 'card_creator.id = user_profile_creator.card_id')
            ->leftJoin('user_profile as user_profile_updater', 'user_profile_updater.user_id = orders.updater_id')
            ->leftJoin('card as card_updater', 'card_updater.id = user_profile_creator.card_id')
            ->leftJoin('department', 'department.id = orders.department_id')

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
                'created_at',
                'department_name',
                'short_desc',
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

        $query->andFilterWhere(['IN', 'orders.department_id', $this->department_ids]);
        $query->andFilterWhere(['IN', 'orders.type_cards', $this->type_cards_ids]);
        $query->andFilterWhere(['IN', 'orders.priority', $this->priority_ids]);
        $query->andFilterWhere(['IN', 'orders.creator_id', $this->creator_ids]);
        $query->andFilterWhere(['IN', 'orders.status_id', $this->status_ids]);
        $query->andFilterWhere(['like', 'orders.short_desc', $this->short_desc]);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels = ArrayHelper::merge($labels, [
            'department_name' => Yii::t('app', 'Подразделение'),
        ]);

        return $labels;
    }

}