<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\models\base\D3lDefinition as BaseD3lDefinition;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3l_definition".
 */
class D3lDefinition extends BaseD3lDefinition
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
