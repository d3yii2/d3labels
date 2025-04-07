<?php

namespace d3yii2\d3labels\components;


use d3yii2\d3labels\models\D3lLabel;
use yii\db\ActiveQuery;

class QuerySearch
{
    /**
     * @param ActiveQuery $query
     * @param string|null $fieldValue
     * @param string $modelIdField for example 'invoice.id'
     * @param int|null $userId - filter userId labels
     */
    public static function addFilter(
        $query,
        ?string $fieldValue,
        string $modelIdField,
        ?int $userId = null
    ): void {
        if (!$fieldValue) {
            return;
        }
        $dsn = D3lLabel::getDb()->dsn;
        preg_match('/dbname=([^;]+)/', $dsn, $matches);
        $d3lLabelTableName = $matches[1] . '.' . D3lLabel::tableName();
        if (strpos($fieldValue, '!') === 0) {
            $query
                ->leftJoin(
                    $d3lLabelTableName,
                    $modelIdField . ' = ' . $d3lLabelTableName . '.model_record_id
                        AND ' . $d3lLabelTableName . '.definition_id = :labelType',
                    [':labelType' => substr($fieldValue, 1)]
                )
                ->andWhere($d3lLabelTableName . '.id IS NULL')
                ->distinct();
        } else {
            $query
                ->leftJoin(
                    $d3lLabelTableName,
                    $modelIdField . ' = ' . $d3lLabelTableName . '.model_record_id')
                ->andWhere([$d3lLabelTableName . '.definition_id' => $fieldValue])
                ->andFilterWhere(['d3l_label.user_id' => $userId])
                ->distinct();
        }
    }
}