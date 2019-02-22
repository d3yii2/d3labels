<?php

namespace d3yii2\d3labels\logic;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lLabel;
use yii\web\NotFoundHttpException;

/**
 * Class Label
 * @package d3yii2\d3labels\logic
 */
class D3Label
{
    /**
     * @return D3lLabel[]
     */
    public static function getAll()
    {
        $labels = D3lLabel::findAll();

        return $labels;
    }

    /**
     * @param int $modelId
     * @return D3lLabel[]
     */
    public static function getAllByModel(int $modelId)
    {
        $labels = D3lLabel::findAll(['model_record_id' => $modelId]);

        return $labels;
    }

    /**
     * @param int $modelId
     * @param array $definitions
     * @throws \yii\db\Exception
     */
    public static function attach(int $modelId, array $definitions)
    {
        foreach ($definitions as $def) {
            $mapping = new D3lLabel();
            $mapping->model_record_id = $modelId;
            $mapping->definition_id = $def->id;

            $mapping->saveOrException();
        }
    }

    /**
     * @param int $labelId
     * @param int $modelRecordId
     * @return D3lLabel
     * @throws NotFoundHttpException
     */
    public static function loadLabel(int $id): D3lLabel
    {
        $label = D3lLabel::findOne($id);

        if (!$label) {
            throw new NotFoundHttpException('Label not found');
        }

        return $label;
    }
}