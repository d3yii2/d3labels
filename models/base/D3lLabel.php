<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3labels\models\base;

use Yii;
use d3system\behaviors\D3DateTimeBehavior;

/**
 * This is the base-model class for table "d3l_label".
 *
 * @property integer $id
 * @property integer $definition_id
 * @property integer $model_record_id
 * @property integer $user_id
 * @property string $time
 * @property string $notes
 *
 * @property \d3yii2\d3labels\models\D3lDefinition $definition
 * @property string $aliasModel
 */
abstract class D3lLabel extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd3l_label';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
        ];
        $behaviors = array_merge(
            $behaviors,
            D3DateTimeBehavior::getConfig(['time'])
        );
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['definition_id', 'model_record_id'], 'required'],
            'smallint Unsigned' => [['definition_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['id','model_record_id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            'integer Signed' => [['user_id'],'integer' ,'min' => -2147483648 ,'max' => 2147483647],
            [['time'], 'safe'],
            [['notes'], 'string', 'max' => 255],
            [['definition_id'], 'exist', 'skipOnError' => true, 'targetClass' => \d3yii2\d3labels\models\D3lDefinition::className(), 'targetAttribute' => ['definition_id' => 'id']],
            'D3DateTimeBehavior' => [['time_local'],'safe']
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
            'user_id' => Yii::t('d3labels', 'User ID'),
            'time' => Yii::t('d3labels', 'Time'),
            'notes' => Yii::t('d3labels', 'Notes'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefinition()
    {
        return $this->hasOne(\d3yii2\d3labels\models\D3lDefinition::className(), ['id' => 'definition_id'])->inverseOf('d3lLabels');
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @throws Exception
     */
    public function saveOrException($runValidation = true, $attributeNames = null): void
    {
        if (!$this->save($runValidation, $attributeNames)) {
            throw new Exception(json_encode($this->getErrors()));
        }
    }
}
