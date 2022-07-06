<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\models\base\D3lLabelHistory as BaseD3lLabelHistory;
use d3system\exceptions\D3ActiveRecordException;

/**
 * This is the model class for table "d3l_label_history".
 */
class D3lLabelHistory extends BaseD3lLabelHistory
{
    public static function newRecord(D3lLabel $label, string $action):void
    {
        $model = new D3lLabelHistory();
        $model->definition_id = $label->definition_id;
        $model->model_id = $label->definition->model_id;
        $model->model_record_id = $label->model_record_id;
        $model->action = $action;
        $model->user_id = $label->user_id;
        $model->notes = $label->notes;
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }
    }
}
