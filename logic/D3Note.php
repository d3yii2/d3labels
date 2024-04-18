<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3Note as NoteModel;

/**
 * Class D3Note
 * @package d3yii2\d3labels\logic
 */
class D3Note
{
    /**
     * Get all Labels
     * @return D3lLabel[]
     */
    public static function getAll(): array
    {
        return NoteModel::findAll([]);
    }

    /**
     * Get all attached notes for the model by ID
     * @param int $modelId
     * @return NoteModel[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function getAllByModel(int $modelId): array
    {
        return NoteModel::find()
            ->where([
                'model_record_id' => $modelId,
            ])
            ->all();
    }

    /**
     * Attach the Note to Model
     *
     * @param int $modelId
     * @param int $modelRecordId
     * @param string $content
     * @param int $userId
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     */
    public static function attach(int $modelId, int $modelRecordId, string $content, int $userId): bool
    {
        $note = NoteModel::find()
            ->where(['model_id' => $modelId, 'model_record_id' => $modelRecordId, 'user_id' => $userId])
            ->one();

        if ($note) {
            // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
            return true;
        }

        $mapping = new NoteModel();
        $mapping->model_id = $modelId;
        $mapping->model_record_id = $modelRecordId;
        $mapping->notes = $content;
        $mapping->user_id = $userId;

        $mapping->saveOrException();

        return true;
    }

    /**
     * Detach a Note from the Model
     *
     * @param int $modelId
     * @param int|null $userId
     * @throws \yii\db\StaleObjectException
     */
    public static function detach(int $modelId, int $userId = null): void
    {
        if ($note = self::getAttachedNote($modelId, $userId)) {
            $note->delete();
        }
    }

    /**
     * Get all notes attached to model
     *
     * @param int $modeRecordId
     * @param int|null $userId
     *
     * @return array
     */
    public static function getAttachedNotes(int $modelRecordId, int $userId = null): array
    {
        $activeQuery = NoteModel::find()
            ->where([
                'model_record_id' => $modelRecordId,
            ]);

        if ($userId) {
            $activeQuery
                ->andWhere(['user_id' => $userId]);
        }
        return $activeQuery
            ->all();
    }
}