<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\models\base\D3lLabel as BaseD3lLabel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3l_label".
 */
class D3lLabel extends BaseD3lLabel
{

    /**
     * @param array $ids
     * @return array
     */
    public static function getAllByModelRecordIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return (new Query())
            ->select('*')
            ->leftJoin(D3lDefinition::tableName(),
                self::tableName() . '.definition_id = ' . D3lDefinition::tableName() . '.id')
            ->from(self::tableName())
            ->where(['model_record_id' => $ids])
            ->all();
    }

    /**
     * @param string $modelId
     * @return array
     */
    public static function forListBox(string $modelId): array
    {
        $models = (new Query())
            ->select(D3lDefinition::tableName() . '.id, ' . D3lDefinition::tableName() . '.label')
            ->leftJoin(D3lDefinition::tableName(),
                self::tableName() . '.definition_id = ' . D3lDefinition::tableName() . '.id')
            ->from(self::tableName())
            ->where(['model_id' => $modelId])
            ->groupBy(self::tableName() . '.definition_id')
            ->all();

        return ArrayHelper::map($models, 'id', 'label');

    }

}
