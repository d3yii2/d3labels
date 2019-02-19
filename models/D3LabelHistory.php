<?php

namespace d3yii2\d3labels\models;

use Yii;
use \d3yii2\d3labels\models\base\D3LabelHistory as BaseD3LabelHistory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3_label_history".
 */
class D3LabelHistory extends BaseD3LabelHistory
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
}
