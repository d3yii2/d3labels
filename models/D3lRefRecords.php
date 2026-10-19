<?php

namespace d3yii2\d3labels\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\base\D3lRefRecords as BaseD3lRefRecords;

/**
 * This is the model class for table "d3l_ref_records".
 */
class D3lRefRecords extends BaseD3lRefRecords
{
    /**
     * @return self[]
     * @throws D3ActiveRecordException
     */
    public static function findByModel(object $model, ?string $code = null): array
    {
        return self::find()
            ->findByModel($model, $code)
            ->all();
    }
}
