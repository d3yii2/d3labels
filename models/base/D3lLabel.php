<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3labels\models\base;

use Yii;
use d3system\behaviors\D3DateTimeBehavior;
use d3system\yii2\validators\D3TrimValidator;
use d3yii2\d3labels\models\D3lDefinition;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property D3lDefinition $definition
 * @property string $aliasModel
 */
abstract class D3lLabel extends ActiveRecord
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
    public function behaviors(): array
    {
        return D3DateTimeBehavior::getConfig(['time']);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'trimNumbers' => [['id','definition_id','model_record_id','user_id'],D3TrimValidator::class, 'trimOnlyStringValues' => true],
            'required' => [['definition_id', 'model_record_id'], 'required'],
            'smallint Unsigned' => [['definition_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['id','user_id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            'bigint Unsigned' => [['model_record_id'],'integer' ,'min' => 0 ,'max' => 1.844674407371E+19],
            [['user_id', 'time', 'notes'], 'default', 'value' => null],
            [['time'], 'safe'],
            [['notes'], 'string', 'max' => 255],
            [['definition_id'], 'exist', 'skipOnError' => true, 'targetClass' => D3lDefinition::class, 'targetAttribute' => ['definition_id' => 'id']],
            'D3DateTimeBehavior' => [['time_local'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
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
     * @return ActiveQuery
     */
    public function getDefinition(): ActiveQuery
    {
        return $this
            ->hasOne(D3lDefinition::class, ['id' => 'definition_id'])
            ->inverseOf('d3lLabels');
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
