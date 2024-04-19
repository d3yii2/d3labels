<?php

namespace d3yii2\d3labels\logic;

use cewood\cwatlikumi\models\CwatPack;
use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3Note as NoteModel;
use yii\db\ActiveRecord;

/**
 * Class D3Note
 * @package d3yii2\d3labels\logic
 */
class D3Note
{
    /**
     * Get all attached notes for the model by ID
     * @param ActiveRecord $model
     * @return array
     */
    public static function getAllByModel(ActiveRecord $model, ?string $modelClass = null): array
    {
        return NoteModel::find()
            ->where([
                'model_id' => self::getSysModelId($model, $modelClass),
                'model_record_id' => $model->id,
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
    public static function attach(ActiveRecord $model, string $content, int $userId): bool
    {
        $sysModelId = self::getSysModelId($model);
            
        $note = NoteModel::find()
            ->where(['model_id' => $sysModelId, 'model_record_id' => $model->id, 'user_id' => $userId])
            ->one();

        if ($note) {
            // Ignorē ja piesaistīta, lai neizraisītu exception pie lapas pārlādes
            return true;
        }

        $mapping = new NoteModel();
        $mapping->model_id = $sysModelId;
        $mapping->model_record_id = $model->id;
        $mapping->notes = $content;
        $mapping->user_id = $userId;

        $mapping->saveOrException();

        return true;
    }

    /**
     * Detach a Notes from the Model
     * @param ActiveRecord $model
     * @param int|null $userId
     */
    public static function detach(ActiveRecord $model, ?int $userId = null): void
    {
        if ($notes = self::getAttachedNotes($model, $userId)) {
            
            foreach ($notes as $note) {
                $note->delete();
            }
        }
    }

    /**
     * Get all notes attached to model
     * @param ActiveRecord $model
     * @param int|null $userId
     * @return array
     */
    public static function getAttachedNotes(ActiveRecord $model, ?int $userId = null, ?string $modelClass = null): array
    {
        
        $activeQuery = NoteModel::find()
            ->where([
                'model_id' => self::getSysModelId($model, $modelClass),
                'model_record_id' => $model->id,
            ]);

        if ($userId) {
            $activeQuery
                ->andWhere(['user_id' => $userId]);
        }
        return $activeQuery
            ->all();
    }

    /**
     * @param ActiveRecord $model
     * @return null|int
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    private static function getSysModelId(ActiveRecord $model, ?string $modelClass = null): ?int
    {
        return SysModelsDictionary::getIdByClassName($modelClass ?? get_class($model));
    }
}