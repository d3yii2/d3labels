<?php

namespace d3yii2\d3labels\models;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\base\D3lDefinition as BaseD3lDefinition;
use Yii;

/**
 * This is the model class for table "d3l_definition".
 */
class D3lDefinition extends BaseD3lDefinition
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [
            'label',
            'required',
            'when' => static function ($model) {
                return true === empty($model->label) && true === empty($model->icon);
            },
            'whenClient' => "function (attribute, value) {
                return !$('#d3ldefinition-icon').val();
            }",
            'message' => Yii::t('d3labels', 'Label is required if no icon')
        ];

        return $rules;
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        if (!parent::save($runValidation, $attributeNames)) {
            $errors = $this->getErrors();

            $errMsg = isset($errors['model_id'])
                ? 'Label with this name exists already'
                : ($errors['label'][0] ?? 'Unable to create Label'
                );


            throw new D3ActiveRecordException($this, Yii::t('d3labels', $errMsg));
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        D3lDefinitionDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        D3lDefinitionDictionary::clearCache();
    }
}
