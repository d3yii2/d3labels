<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3labels\models\base;

use Yii;
use yii\db\Exception;

/**
 * This is the base-model class for table "d3l_label".
 *
 * @property string $id
 * @property integer $definition_id
 * @property string $model_record_id
 *
 * @property \d3yii2\d3labels\models\D3lDefinition $definition
 * @property string $aliasModel
 */
abstract class D3lLabel extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'd3l_label';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['definition_id', 'model_record_id'], 'required'],
            [['definition_id', 'model_record_id'], 'integer'],
            [['definition_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d3yii2\d3labels\models\D3lDefinition::className(), 'targetAttribute' => ['definition_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d3labels', 'ID'),
            'definition_id' => Yii::t('d3labels', 'Definition ID'),
            'model_record_id' => Yii::t('d3labels', 'Model Record ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefinition()
    {
        return $this->hasOne(\d3yii2\d3labels\models\D3lDefinition::className(), ['id' => 'definition_id']);
    }




    public function saveOrException($runValidation = true, $attributeNames = null)
    {
        if(!parent::save($runValidation, $attributeNames)){
            throw new Exception(json_encode($this->getErrors()));
        }
    }
}
