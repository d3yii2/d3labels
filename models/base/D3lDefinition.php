<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3labels\models\base;

use Yii;
use yii\db\Exception;

/**
 * This is the base-model class for table "d3l_definition".
 *
 * @property integer $id
 * @property integer $sys_company_id
 * @property integer $model_id
 * @property string $label
 * @property string $collor
 * @property string $icon
 * @property string $action_class
 * @property string $action_method
 *
 * @property \d3yii2\d3labels\models\D3LabelHistory[] $d3LabelHistories
 * @property \d3yii2\d3labels\models\SysModels $model
 * @property \d3yii2\d3labels\models\D3lLabel[] $d3lLabels
 * @property \d3yii2\d3labels\models\D3lTimeBomb[] $d3lTimeBombs
 * @property string $aliasModel
 */
abstract class D3lDefinition extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'd3l_definition';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sys_company_id', 'model_id'], 'required'],
            [['sys_company_id', 'model_id'], 'integer'],
            [['action_class'], 'string'],
            [['label', 'icon'], 'string', 'max' => 20],
            [['collor'], 'string', 'max' => 6],
            [['action_method'], 'string', 'max' => 256],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d3yii2\d3labels\models\SysModels::className(), 'targetAttribute' => ['model_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('d3labels', 'ID'),
            'sys_company_id' => Yii::t('d3labels', 'Sys Company ID'),
            'model_id' => Yii::t('d3labels', 'Model ID'),
            'label' => Yii::t('d3labels', 'Label'),
            'collor' => Yii::t('d3labels', 'Collor'),
            'icon' => Yii::t('d3labels', 'Icon'),
            'action_class' => Yii::t('d3labels', 'Action Class'),
            'action_method' => Yii::t('d3labels', 'Action Method'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'model_id' => Yii::t('d3labels', 'Model'),
            'label' => Yii::t('d3labels', 'Label'),
            'collor' => Yii::t('d3labels', 'Collor'),
            'icon' => Yii::t('d3labels', 'Icon'),
            'action_class' => Yii::t('d3labels', 'Action Class'),
            'action_method' => Yii::t('d3labels', 'Action Method'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD3LabelHistories()
    {
        return $this->hasMany(\d3yii2\d3labels\models\D3LabelHistory::className(), ['definition_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(\d3yii2\d3labels\models\SysModels::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD3lLabels()
    {
        return $this->hasMany(\d3yii2\d3labels\models\D3lLabel::className(), ['definition_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD3lTimeBombs()
    {
        return $this->hasMany(\d3yii2\d3labels\models\D3lTimeBomb::className(), ['definition_id' => 'id']);
    }


    /**
     * @return \d3yii2\d3labels\models\D3LabelHistory     */
    public function newD3LabelHistories()
    {
        if ($this->getIsNewRecord()){
            throw new Exception('Can not create new related record for new record!');
        }
        $model = new \d3yii2\d3labels\models\D3LabelHistory();
        $model->definition_id = $this->id;
        return $model;
    }

    /**
     * @return \d3yii2\d3labels\models\D3lLabel     */
    public function newD3lLabels()
    {
        if ($this->getIsNewRecord()){
            throw new Exception('Can not create new related record for new record!');
        }
        $model = new \d3yii2\d3labels\models\D3lLabel();
        $model->definition_id = $this->id;
        return $model;
    }

    /**
     * @return \d3yii2\d3labels\models\D3lTimeBomb     */
    public function newD3lTimeBombs()
    {
        if ($this->getIsNewRecord()){
            throw new Exception('Can not create new related record for new record!');
        }
        $model = new \d3yii2\d3labels\models\D3lTimeBomb();
        $model->definition_id = $this->id;
        return $model;
    }



    public function saveOrException($runValidation = true, $attributeNames = null)
    {
        if(!parent::save($runValidation, $attributeNames)){
            throw new Exception(json_encode($this->getErrors()));
        }
    }
}
