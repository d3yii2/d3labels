<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\models\base\D3Note as BaseD3Note;
use Yii;

/**
 * This is the model class for table "d3l_notes".
 */
class D3Note extends BaseD3Note
{
    public ?string $userName = null;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules['userNameString'] = ['userName', 'string'];
        return $rules;
    }

    public function attributeLabels(): array
    {
        return array_merge(
            parent::attributeLabels(),
            ['userName' => Yii::t('d3labels','User Name')]
        );
    }
}
