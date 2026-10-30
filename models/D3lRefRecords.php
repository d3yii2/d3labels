<?php

namespace d3yii2\d3labels\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\exceptions\D3ActiveRecordException2;
use d3yii2\d3labels\dictionaries\D3lRefDefDictionary;
use d3yii2\d3labels\models\base\D3lRefRecords as BaseD3lRefRecords;
use yii\db\ActiveRecord;
use yii\db\Exception;

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

    /**
     * @param string $defCode
     * @param ActiveRecord $model
     * @param ActiveRecord $refModel
     * @throws D3ActiveRecordException
     * @throws D3ActiveRecordException2
     * @throws Exception
     */
    public static function createRef(
        string $defCode,
        ActiveRecord $model,
        ActiveRecord $refModel
    ): void {
        $params = [
            'sys_model_id' => SysModelsDictionary::getIdByClassName($model::class),
            'record_id' => $model->id,
            'def_id' => D3lRefDefDictionary::getIdByCode($defCode),
            'ref_record_id' => $refModel->id,
        ];

        if (self::findOne($params)) {
            return;
        }

        $model = new self($params);
        if (!$model->save()) {
            throw new D3ActiveRecordException2($model);
        }
    }

    public static function createRefByClassName(
        string $defCode,
        string $modelClassName,
        int $modelId,
        ActiveRecord $refModel
    ): void {
        $params = [
            'sys_model_id' => SysModelsDictionary::getIdByClassName($modelClassName),
            'record_id' => $modelId,
            'def_id' => D3lRefDefDictionary::getIdByCode($defCode),
            'ref_record_id' => $refModel->id,
        ];

        if (self::findOne($params)) {
            return;
        }

        $model = new self($params);
        if (!$model->save()) {
            throw new D3ActiveRecordException2($model);
        }
    }


    /**
     * @param string $defCode
     * @param ActiveRecord $model
     * @return array
     * @throws D3ActiveRecordException
     */
    public static function findAllRef(
        string $defCode,
        ActiveRecord $model
    ): array {
        $params = [
            'sys_model_id' => SysModelsDictionary::getIdByClassName($model::class),
            'record_id' => $model->id,
            'def_id' => D3lRefDefDictionary::getIdByCode($defCode)
        ];

        return self::findAll($params);
    }
}
