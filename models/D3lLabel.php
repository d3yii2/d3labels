<?php

namespace d3yii2\d3labels\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\base\D3lLabel as BaseD3lLabel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "d3l_label".
 */
class D3lLabel extends BaseD3lLabel
{

    /**
     * @param array $ids
     * @param string $modelClassName
     * @return int[]
     * @throws D3ActiveRecordException
     */
    public static function getAllByModelRecordIds(array $ids,string $modelClassName): array
    {
        if (empty($ids)) {
            return [];
        }

        return (new Query())
            ->select('*')
            ->leftJoin(D3lDefinition::tableName(),
                self::tableName() . '.definition_id = ' . D3lDefinition::tableName() . '.id')
            ->from(self::tableName())
            ->where([
                self::tableName().'.model_record_id' => $ids,
                D3lDefinition::tableName().'.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->all();
    }

    /**
     * @param int $modelId
     * @return array
     * @deprecated  use D3lDefinitionDictionary::getForListBox()
     */
    public static function forListBox(int $modelId): array
    {
        $models = D3lDefinition::find()
            ->select([
                'id',
                'label'
            ])
            ->where(['model_id' => $modelId])
            ->asArray()
            ->all();

        return ArrayHelper::map($models, 'id', 'label');

    }



}
