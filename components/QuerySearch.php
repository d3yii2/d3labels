<?php

namespace d3yii2\d3labels\components;


use d3yii2\d3labels\models\D3lLabel;
use yii\db\ActiveQuery;

class QuerySearch
{
    /**
     * @param ActiveQuery $query
     * @param string|null $fieldValue
     * @param string $modelIdField for example 'invoice.id
     */
    public static function addFilter(&$query, ?string $fieldValue, string $modelIdField): void
    {
        if (!$fieldValue) {
            return;
        }

        if (strpos($fieldValue, '!') === 0) {
            $query
                ->leftJoin(
                    D3lLabel::tableName(),
                    $modelIdField . ' = ' . D3lLabel::tableName() . '.model_record_id
                        AND ' . D3lLabel::tableName() . '.definition_id = :labelType',
                    [':labelType' => substr($fieldValue, 1)]
                )
                ->andWhere(D3lLabel::tableName() . '.id IS NULL')
                ->distinct();
        } else {
            $query
                ->leftJoin(
                    D3lLabel::tableName(),
                    $modelIdField . ' = ' . D3lLabel::tableName() . '.model_record_id')
                ->andWhere([D3lLabel::tableName() . '.definition_id' => $fieldValue])
                ->distinct();
        }
    }
}