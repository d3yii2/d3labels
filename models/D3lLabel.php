<?php

namespace d3yii2\d3labels\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\base\D3lLabel as BaseD3lLabel;
use Yii;
use yii\db\ActiveQuery;
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
     * @param int|null $filterUserId
     * @return arra{
     *     id:int,
     *     definition_id: int,
     *     model_record_id: int,
     *     user_id: int|null,
     *     time: string|null,
     *     notes: string,
     *     sys_company_id: int,
     *     code: string|null,
     *     model_id: int,
     *     collor: string,
     *     action_class: string,
     *     action_method: string
     * }[]
     * @throws D3ActiveRecordException
     */
    public static function getAllByModelRecordIds(
        array $ids,
        string $modelClassName,
        int $filterUserId = null
    ): array
    {
        if (empty($ids)) {
            return [];
        }

        $query = (new Query())
            ->select('*')
            ->leftJoin(D3lDefinition::tableName(),
                self::tableName() . '.definition_id = ' . D3lDefinition::tableName() . '.id')
            ->from(self::tableName())
            ->where([
                self::tableName() . '.model_record_id' => $ids,
                D3lDefinition::tableName() . '.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ]);
        if ($filterUserId) {
            $query->andWhere([self::tableName() . '.user_id' => $filterUserId]);
        }
        return $query
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

    public function beforeSave($insert): bool
    {
        $this->user_id = Yii::$app->user->id ?? NULL;
        $this->time = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    /**
     * @throws D3ActiveRecordException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        D3lLabelHistory::newRecord($this, D3lLabelHistory::ACTION_ADDED);
    }

    /**
     * @throws D3ActiveRecordException
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        D3lLabelHistory::newRecord($this, D3lLabelHistory::ACTION_DROPED);
    }

    /**
     * get to model attached label list query
     * @throws D3ActiveRecordException
     */
    public static function findModelAttachedLabels(
        string $modelClassName,
        int $modelRecordId,
        int $userId = null
    ): ActiveQuery {
        return self::find()
            ->innerJoin(
                'd3l_definition',
                'd3l_definition.id = '.self::tableName().'.definition_id'
            )
            ->where([
                'model_record_id' => $modelRecordId,
                'd3l_definition.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->andFilterWhere([
                self::tableName() . '.user_id' => $userId
            ]);
    }
}
