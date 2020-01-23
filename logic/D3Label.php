<?php

namespace d3yii2\d3labels\logic;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lLabel;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
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
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public static function attach(int $modelId, int $definitionId): bool
    {
        $definition = D3Definition::loadDefinition($definitionId);

        $label = $definition
            ->getD3lLabels()
            ->where(['model_record_id' => $modelId])
            ->one();
        if ($label) {
            // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
            return true;
        }

        $mapping = new D3lLabel();
        $mapping->model_record_id = $modelId;
        $mapping->definition_id = $definition->id;

        $mapping->saveOrException();

        return true;
    }

    /**
     * Attach a Label to the Model
     *
     * @param int $modelId
     * @param int $definitionId
     * @throws D3ActiveRecordException
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function detach(int $modelId, int $definitionId): void
    {
        $definition = D3Definition::loadDefinition($definitionId);

        $label = $definition
            ->getD3lLabels()
            ->where(['model_record_id' => $modelId])
            ->one();

        if ($label) {
            self::remove($label->id);
        }
    }

    /**
     * Remove the attached Label from Model
     * @param int $labelId
     * @return bool
     * @throws D3ActiveRecordException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function remove(int $labelId): bool
    {
        $label = D3lLabel::findOne($labelId);

        if (null === $label) {
            return false;
        }

        if (!$label->delete()) {
            throw new D3ActiveRecordException($label, Yii::t('d3labels', 'Cannot delete Label record'));
        }

        return true;
    }
}