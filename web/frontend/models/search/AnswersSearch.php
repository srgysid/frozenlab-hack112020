<?php

namespace frontend\models\search;

use common\models\Answers;
use common\models\ActiveDataProviderPpu;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;


class AnswersSearch extends Answers
{
    public $creator_ids;
    public $status_ids;

    public $orders_short_desc;
    public $orders_creator_name;
    public $creator_name;

    public function rules()
    {
        return [
            [['creator_ids', 'full_desc', 'status_ids', 'orders_short_desc', 'orders_creator_name', 'creator_name'], 'safe'],
        ];
    }

    public function search($params)
    {
        $session = Yii::$app->session;

        if (!isset($params['AnswersSearch'])) {
            if ($session->has('AnswersSearch')){
                $params['AnswersSearch'] = $session['AnswersSearch'];
            }
        }
        else{
            $session->set('AnswersSearch', $params['AnswersSearch']);
        }

        if (!isset($params['sort'])) {
            if ($session->has('AnswersSearchSort')){
                $params['sort'] = $session['AnswersSearchSort'];
            }
        }
        else{
            $session->set('AnswersSearchSort', $params['sort']);
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

        $user = Yii::$app->user;

        $query = new Query();
        $query->addSelect([
            'answers.id',
            'answers.orders_id',
            'answers.status_id',
            'answers.full_desc',
            'answers.created_at',
            new Expression("concat(card_creator.secondname, ' ', card_creator.firstname, ' ', card_creator.thirdname) as creator_name"),
            new Expression("concat(orders.short_desc, ' от ', CAST(orders.created_at as date) , ' ', CAST(orders.created_at as time)) as orders_short_desc"),
            new Expression("concat(card_orders.secondname, ' ', card_orders.firstname, ' ', card_orders.thirdname) as orders_creator_name"),
        ])->from('answers')
            ->where(['answers.creator_id' => $user->id])
            ->leftJoin('user_profile as user_profile_creator', 'user_profile_creator.user_id = answers.creator_id')
            ->leftJoin('card as card_creator', 'card_creator.id = user_profile_creator.card_id')
            ->leftJoin('user_profile as user_profile_updater', 'user_profile_updater.user_id = answers.updater_id')
            ->leftJoin('card as card_updater', 'card_updater.id = user_profile_creator.card_id')
            ->leftJoin('orders', 'orders.id = answers.orders_id')
            ->leftJoin('user_profile as user_profile_orders', 'user_profile_orders.user_id = orders.creator_id')
            ->leftJoin('card as card_orders', 'card_orders.id = user_profile_orders.card_id')
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
                'full_desc',
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

        $query->andFilterWhere(['IN', 'orders.creator_id', $this->creator_ids]);
        $query->andFilterWhere(['IN', 'answers.status_id', $this->status_ids]);
        $query->andFilterWhere(['like', 'answers.full_desc', $this->full_desc]);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels = ArrayHelper::merge($labels, [
            'orders_short_desc' => Yii::t('app', 'Описание распоряжения'),
            'orders_creator_name' => Yii::t('app', 'Автор распоряжения'),
            'creator_name' => Yii::t('app', 'Исполнитель'),
        ]);

        return $labels;
    }

    public function searchByOrder($params, $orders_id)
    {
        $query = new Query();
        $query->addSelect([
            'answers.id',
            'answers.orders_id',
            'answers.status_id',
            'answers.full_desc',
            'answers.created_at',
            new Expression("concat(card.secondname, ' ', card.firstname, ' ', card.thirdname) as creator_name"),
        ])->from('answers')
            ->where(['answers.orders_id' => $orders_id])
            ->leftJoin('user_profile', 'user_profile.user_id = answers.creator_id')
            ->leftJoin('card', 'card.id = user_profile.card_id')
        ;

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProviderPpu([
            'query' => $query,
        ]);

        $dataProvider->key = 'id';

        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'id',
                'created_at',
                'full_desc',
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

        $query->andFilterWhere(['IN', 'orders.creator_id', $this->creator_ids]);
        $query->andFilterWhere(['IN', 'answers.status_id', $this->status_ids]);
        $query->andFilterWhere(['like', 'answers.full_desc', $this->full_desc]);

        return $dataProvider;
    }

}