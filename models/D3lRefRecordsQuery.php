<?php

namespace d3yii2\d3labels\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\yii2\db\D3ActiveQuery;
use yii\db\Query;

/**
 * This is the ActiveQuery class for [[D3lRefRecords]].
 *
 * @see D3lRefRecords
 *
 * @method D3lRefRecords[] all($db = null)
 * @method D3lRefRecords|null one($db = null)
 */
class D3lRefRecordsQuery extends D3ActiveQuery
{
    /**
     * @param object $model
     * @param string|null $code
     * @return Query
     * @throws D3ActiveRecordException
     */
    public function findByModel(object $model, ?string $code = null): Query
    {
        $query = $this
            ->andWhere([
                'd3l_ref_records.record_id' => $model->id,
                'd3l_ref_records.sys_model_id' => SysModelsDictionary::getIdByClassName(get_class($model)),
            ]);

        if ($code) {
            $query
                ->innerJoin(
                    'd3l_ref_def',
                    'd3l_ref_records.def_id = d3l_ref_def.id'
                )
                ->andWhere(['d3l_ref_def.code' => $code]);
        }
        return $query;
    }

    /**
     * @param object $refModel
     * @param string|null $code
     * @return Query
     * @throws D3ActiveRecordException
     */
    public function findByRefModel(object $refModel, ?string $code = null): Query
    {
        return $this
            ->innerJoin(
                'd3l_ref_def',
                'd3l_ref_records.def_id = d3l_ref_def.id'
            )
            ->andWhere([
                'd3l_ref_records.ref_record_id' => $refModel->id,
                'd3l_ref_def.sys_model_id' => SysModelsDictionary::getIdByClassName(get_class($refModel)),
            ])
            ->andFilterWhere(['d3l_ref_def.code' => $code]);
    }
}
