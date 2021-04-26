<?php


namespace d3yii2\d3labels\logic;


use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;

/**
 * use for maintenance tasks
 *
 * Class D3LabelMaintenance
 * @package d3yii2\d3labels\logic
 */
class D3LabelMaintenance
{

    /**
     * remove model labeled records and label definition
     * @param string $labelCode
     * @param string $modelClass
     * @return int
     * @throws \Throwable
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\db\StaleObjectException
     */
    public static function removeLabel(string $labelCode, string $modelClass): int
    {
        $defId =D3lDefinitionDictionary::findByCodeModel($labelCode, $modelClass);
        $removedLabelsFromModelCount = D3lLabel::deleteAll(['definition_id' => $defId]);
        D3lDefinition::findOne($defId)->delete();
        return $removedLabelsFromModelCount;
    }
}