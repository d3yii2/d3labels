<?php

namespace d3yii2\d3labels\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use d3yii2\d3labels\models\D3lLabel;

/**
* D3lLabelSearch represents the model behind the search form about `d3yii2\d3labels\models\D3lLabel`.
*/
class D3lLabelSearch extends D3lLabel
{
/**
* @inheritdoc
*/
public function rules()
{
return [
[['id', 'definition_id', 'model_record_id'], 'integer'],
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
$query = D3lLabel::find();

$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

$this->load($params);

if (!$this->validate()) {
// uncomment the following line if you do not want to any records when validation fails
// $query->where('0=1');
return $dataProvider;
}

$query
            ->andFilterWhere([
                'id' => $this->id,
                'definition_id' => $this->definition_id,
                'model_record_id' => $this->model_record_id,
            ])
;
return $dataProvider;
}
}