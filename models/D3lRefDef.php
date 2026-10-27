<?php

namespace d3yii2\d3labels\models;

use d3yii2\d3labels\dictionaries\D3lRefDefDictionary;
use d3yii2\d3labels\models\base\D3lRefDef as BaseD3lRefDef;

/**
 * This is the model class for table "d3l_ref_def".
 */
class D3lRefDef extends BaseD3lRefDef
{
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        D3lRefDefDictionary::clearCache();
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        D3lRefDefDictionary::clearCache();
    }

    public static function optsD3lRefDefDictionary(): array
    {
        return D3lRefDefDictionary::getList();
    }
}
