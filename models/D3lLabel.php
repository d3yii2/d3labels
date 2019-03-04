<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\models\D3lDefinition;
use Yii;
use \d3yii2\d3labels\models\base\D3lLabel as BaseD3lLabel;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3l_label".
 */
class D3lLabel extends BaseD3lLabel
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public static function getAllByModelRecordIds(array $ids)
    {
        if(empty($ids)) {
            return [];
        }

        $models = (new \yii\db\Query())
            ->select('*')
            ->leftJoin(D3lDefinition::tableName(), self::tableName() . '.definition_id = ' . D3lDefinition::tableName() . '.id')
            ->from(self::tableName())
            ->where(['model_record_id' => $ids])
            ->all();

        return $models;
    }
}
