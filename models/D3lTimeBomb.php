<?php

namespace d3yii2\d3labels\models;

use Yii;
use \d3yii2\d3labels\models\base\D3lTimeBomb as BaseD3lTimeBomb;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3l_time_bomb".
 */
class D3lTimeBomb extends BaseD3lTimeBomb
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
