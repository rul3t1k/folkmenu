<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Recipe;

/**
 * RecipeSearch represents the model behind the search form of `app\models\Recipe`.
 */
class RecipeSearch extends Recipe
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'category_id', 'prep_time_min', 'cook_time_min', 'calories'], 'integer'],
            [['title', 'description', 'difficulty', 'equipment', 'secret', 'photo', 'status', 'created_at', 'updated_at'], 'safe'],
            [['protein', 'fat', 'carbs'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Recipe::find();

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
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'prep_time_min' => $this->prep_time_min,
            'cook_time_min' => $this->cook_time_min,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'fat' => $this->fat,
            'carbs' => $this->carbs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'difficulty', $this->difficulty])
            ->andFilterWhere(['like', 'equipment', $this->equipment])
            ->andFilterWhere(['like', 'secret', $this->secret])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
