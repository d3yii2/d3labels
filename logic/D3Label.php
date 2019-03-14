<?php

namespace d3yii2\d3labels\logic;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
use yii\web\NotFoundHttpException;

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
        $labels = D3lLabel::findAll();

        return $labels;
    }

    /**
     * Get all attached labels for the model by ID
     * @param int $modelId
     * @return D3lLabel[]
     */
    public static function getAllByModel(int $modelId): array
    {
        $labels = D3lLabel::findAll(['model_record_id' => $modelId]);

        return $labels;
    }

    /**
     * Attach the Label to Model
     * @param int $modelId
     * @param array $definitions
     * @throws \yii\db\Exception
     */
    public static function attach(int $modelId, D3lDefinition $definition)
    {
        $attached = false;

        foreach ($definition->d3lLabels as $label) {
            if ($label->definition_id == $definition->id && $label->model_record_id == $modelId) {
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
    }

    /**
     * Remove the attached Label from Model
     * @param int $labelId
     * @throws D3ActiveRecordException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function remove(int $labelId)
    {
        $label = D3lLabel::findOne($labelId);

        if (null === $label) {
            return false;
        }

        if (!$label->delete()) {
            throw new D3ActiveRecordException($label, Yii::t('d3labels', 'Cannot delete Label record'));
        }
    }
}