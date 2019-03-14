<?php

namespace d3yii2\d3labels\models;

use d3system\exceptions\D3ActiveRecordException;
use yii\base\Model;

/**
 * This is the model class for table "d3l_label".
 */
class D3lLabelForm extends Model
{
    public $modelClass;
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
     * @param $data
     * @param null $formName
     */
    public function load($data, $formName = null)
    {
        if (empty($data[self::formName()]['modelClass'])) {
            return false;
        }

        if (empty($data[$formName])) {
            return false;
        }

        if (!empty($data[self::formName()]['controllerModelId'])) {
            $this->controllerModelId = $data[self::formName()]['controllerModelId'];
        }

        $this->modelClass = $data[self::formName()]['modelClass'];

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
                $def = new \d3yii2\d3labels\logic\D3Definition($this->modelClass);
                $def->setModel($label);
                $def->save();
            }
        } catch (\Exception $err) {
            return false;
        }

        return true;
    }
}
