<?php

namespace d3yii2\d3labels\logic;

use d3system\exceptions\D3ActiveRecordException;
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
     * @return D3lLabel[]
     */
    public static function getAllByModel(int $modelId): array
    {
        return D3lLabel::findAll(['model_record_id' => $modelId]);
    }

    /**
     * Attach the Label to Model
     *
     * @param int $modelId
     * @param int $definitionId
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public static function attach(int $modelId, int $definitionId): bool
    {
        $attached = false;
        $definition = D3Definition::loadDefinition($definitionId);

        foreach ($definition->d3lLabels as $label) {
            if ($label->definition_id === $definition->id && $label->model_record_id === $modelId) {
                $attached = true;
                break;
            }
        }

        // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
        if ($attached) {
            return true;
        }

        $mapping = new D3lLabel();
        $mapping->model_record_id = $modelId;
        $mapping->definition_id = $definition->id;

        $mapping->saveOrException();

        return true;
    }

    /**
     * Remove the attached Label from Model
     * @param int $labelId
     * @return bool
     * @throws D3ActiveRecordException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function remove(int $labelId): bool
    {
        $label = D3lLabel::findOne($labelId);

        if (null === $label) {
            return false;
        }

        if (!$label->delete()) {
            throw new D3ActiveRecordException($label, \Yii::t('d3labels', 'Cannot delete Label record'));
        }

        return true;
    }
}