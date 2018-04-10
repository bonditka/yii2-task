<?php

namespace bonditka\task\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TaskSearch represents the model behind the search form about `bonditka\task\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_user', 'create_time', 'update_user', 'update_time'], 'integer'],
            [['module', 'task', 'description', 'datetime', 'run_status', 'param'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'datetime' => $this->datetime,
            'create_user' => $this->create_user,
            'create_time' => $this->create_time,
            'update_user' => $this->update_user,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'module', $this->module])
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'run_status', $this->run_status])
            ->andFilterWhere(['like', 'param', $this->param]);

        return $dataProvider;
    }
}
