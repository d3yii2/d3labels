<?php

namespace d3yii2\d3labels\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\logic\D3Definition;
use yii\base\Model;

/**
 * This is the model class for table "d3l_label".
 * @property array $labels
 * @property int $controllerModelId
 */
class D3lLabelForm extends Model
{
    public $modelClass;

    /** @var D3lDefinition[]  */
    public $labels = [];
    public $controllerModelId;

    const FORM_NAME = 'D3lLabelForm';
    const DEFINITION_MODEL_NAME = 'D3lDefinition';

    public function rules(): array
    {
        return [
            [['controllerModelId'], 'integer'],
            [['modelClass'], 'string'],
            [['modelClass', 'labels'], 'required'],
            [['modelClass', 'labels'], 'safe'],
        ];
    }

    /**
     * @param array $data
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function load($data, $formName = null): bool
    {
        if (empty($data[self::FORM_NAME]['modelClass'])) {
            return false;
        }

        if (empty($data[self::DEFINITION_MODEL_NAME])) {
            return false;
        }

        $this->modelClass = $data[self::FORM_NAME]['modelClass'];

        $labels = $data[self::DEFINITION_MODEL_NAME];

        foreach ($labels as $label) {
            $def = new D3lDefinition();
            $def->setAttributes($label);
            $this->labels[] = $def;
        }

        return true;
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws D3ActiveRecordException
     */
    public function save($runValidation = true, $attributeNames = null): bool
    {
        if (!$this->validate()) {
            return false;
        }

        foreach ($this->labels as $label) {
            $def = new D3Definition($this->modelClass);
            $def->setModel($label);
            $def->save();
        }

        return true;
    }
}
