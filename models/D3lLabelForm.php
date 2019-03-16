<?php

namespace d3yii2\d3labels\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\logic\D3Definition;
use yii\base\Model;

/**
 * This is the model class for table "d3l_label".
 */
class D3lLabelForm extends Model
{
    public $modelClass;

    /** @var D3lDefinition[]  */
    public $labels = [];
    public $controllerModelId;

    public function rules()
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
     * @param null $formName
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function load($data, $formName = null)
    {
        if (empty($data[$this->formName()]['modelClass'])) {
            return false;
        }

        if (empty($data[$formName])) {
            return false;
        }

        if (!empty($data[$this->formName()]['controllerModelId'])) {
            $this->controllerModelId = $data[$this->formName()]['controllerModelId'];
        }

        $this->modelClass = $data[$this->formName()]['modelClass'];

        $labels = $data[$formName];

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
    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->validate()) {
            return false;
        }

        try {
            foreach ($this->labels as $label) {
                $def = new D3Definition($this->modelClass);
                $def->setModel($label);
                $def->save();
            }
        } catch (\Exception $err) {
            return false;
        }

        return true;
    }
}
