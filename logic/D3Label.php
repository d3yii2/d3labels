<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3UserAlertException;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3lLabel;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use Yii;

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
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public static function attachByModelCode(object $model, string $labelCode): bool
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

        $mapping->saveOrException();

        return true;
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
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function detach(int $modelId, int $definitionId): void
    {

        /**
         * @var D3lLabel $label
         */
        if($label = D3lLabel::findOne([
            'model_record_id' => $modelId,
            'definition_id' => $definitionId
        ])){
            $label->delete();
        }

    }

    /**
     * Check if Model has attached label by Code
     *
     * @param object $model
     * @param string $labelCode
     * @throws Throwable
     * @throws D3UserAlertException
     */

    public static function isLabelAttachByModelCode($model, $labelCode)
    {
        if($closedLabelId = D3lDefinitionDictionary::findByCodeModelObject($labelCode, $model)) {
            $attachedL = new D3LabelList($model, Yii::$app->SysCmp->getActiveCompanyId());
            foreach ($attachedL->getAttached() as $def_id => $label) {
                if($def_id == $closedLabelId) {
                    throw new D3UserAlertException(Yii::t('d3invoices', 'Changes are not allowed, invoice is "Closed"!'));
                }
            }
        }
    }

}