<?php

namespace d3yii2\d3labels\models\forms;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\logic\D3Note;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * form model for adding notes
 */
class Note extends Model
{

    /** attributes */
    public ?string $note = null;

    /** parameters  */
    public ?object $model = null;
    public ?int $userId = null;

    public function rules(): array
    {
        return [
            [['note'], 'required'],
            [['note'], 'string']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'note' => Yii::t('d3labels', 'Note')
        ];
    }

    /**
     * @throws Exception
     * @throws D3ActiveRecordException
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        D3Note::attach($this->model, $this->note, $this->userId);
        return true;
    }
}
