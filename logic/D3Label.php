<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3labels\components\CreateAction;
use d3yii2\d3labels\components\DefinitionDeleteAction;
use d3yii2\d3labels\components\DefinitionEditAction;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3lLabel;

/**
 * Class D3Label
 * @package d3yii2\d3labels\logic
 */
class D3Label
{
    /**
     * Get all Labels
     * @return D3lLabel[]
     */
    public static function getAll(): array
    {
        return D3lLabel::findAll([]);
    }

    /**
     * Get all attached labels for the model by ID
     * @param int $modelId
     * @param string $className
     * @return D3lLabel[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function getAllByModel(int $modelId, string $className): array
    {
        return D3lLabel::find()
            ->innerJoin('d3l_definition','d3l_definition.id = d3l_label.definition_id')
            ->where([
                'model_record_id' => $modelId,
                'd3l_definition.model_id' => SysModelsDictionary::getIdByClassName($className)
            ])
            ->all();
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public static function attachByModelCode(object $model, string $labelCode, string $notes = ''): bool
    {
        $definitionId = D3lDefinitionDictionary::findByCodeModelObject($labelCode, $model );
        $definition = D3Definition::loadDefinition($definitionId);

        $label = $definition
            ->getD3lLabels()
            ->where(['model_record_id' => $model->id])
            ->one();
        if ($label) {
            // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
            return true;
        }

        $mapping = new D3lLabel();
        $mapping->model_record_id = $model->id;
        $mapping->definition_id = $definition->id;
        $mapping->notes = $notes;

        $mapping->saveOrException();

        return true;
    }

    /**
     * Attach the Label to Model
     *
     * @param int $modelId
     * @param int $definitionId
     * @param int|null $userId
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     */
    public static function attach(
        int $modelId,
        int $definitionId,
        int $userId = null,
        string $notes = ''
    ): bool
    {
        $definition = D3Definition::loadDefinition($definitionId);

        $activeQuery = $definition
            ->getD3lLabels()
            ->where(['model_record_id' => $modelId]);

        if ($userId) {
            $activeQuery->andWhere(['user_id' => $userId]);
        }
        $label = $activeQuery
            ->one();
        if ($label) {
            // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
            return true;
        }

        $mapping = new D3lLabel();
        $mapping->model_record_id = $modelId;
        $mapping->definition_id = $definition->id;
        $mapping->notes = $notes;

        $mapping->saveOrException();

        return true;
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public static function detachByModelCode(object $model, string $labelCode): void
    {
        $definitionId = D3lDefinitionDictionary::findByCodeModelObject($labelCode, $model);
        /**
         * @var D3lLabel $label
         */
        if($label = D3lLabel::findOne([
            'model_record_id' => $model->id,
            'definition_id' => $definitionId
        ])){
            $label->delete();
        }
    }

    /**
     * Attach a Label to the Model
     *
     * @param int $modelId
     * @param int $definitionId
     * @param int|null $userId
     * @throws \yii\db\StaleObjectException
     */
    public static function detach(int $modelId, int $definitionId, int $userId = null): void
    {
        if ($label = self::getAttachedLabel($modelId, $definitionId, $userId)) {
            $label->delete();
        }
    }

    /**
     * check if model has attachment
     *
     * @param int $modelId
     * @param int $defId
     * @param int|null $userId
     *
     * @return D3lLabel | null
     */
    public static function getAttachedLabel(int $modelId, int $defId, int $userId = null): ?D3lLabel
    {
        $activeQuery = D3lLabel::find()
            ->where([
                'model_record_id' => $modelId,
                'definition_id' => $defId
            ]);

        if ($userId) {
            $activeQuery
                ->andWhere(['user_id' => $userId]);
        }
        return $activeQuery
            ->one();
    }

    /**
     * @param object|\d3yii2\d3activity\components\ActivityRecord $model
     * @param string $labelCode
     * @param int|null $userId
     * @return \d3yii2\d3labels\models\D3lLabel|null
     */
    public static function getAttachedLabelByCode(object $model, string $labelCode, int $userId = null): ?D3lLabel
    {
        $codeId = D3lDefinitionDictionary::findByCodeModelObject($labelCode,$model);
        return self::getAttachedLabel($model->id,$codeId, $userId);
    }

    /**
     * @param string $modelClass
     * @param int $modelRecordId
     * @param string $labelCode
     * @param int|null $userId
     * @return \d3yii2\d3labels\models\D3lLabel|null
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function getAttachedLabelByClassCode(string $modelClass, int $modelRecordId, string $labelCode, int $userId = null): ?D3lLabel
    {
        $codeId = D3lDefinitionDictionary::findByCodeModel($labelCode,$modelClass);
        return self::getAttachedLabel($modelRecordId,$codeId, $userId);
    }
}
