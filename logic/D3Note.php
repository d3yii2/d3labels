<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3Note as NoteModel;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Class D3Note
 * @package d3yii2\d3labels\logic
 */
class D3Note
{

    /**
     * Get all attached notes for the model by ID
     * @param ActiveRecord $model
     * @param string|null $modelClass
     * @return array
     * @throws D3ActiveRecordException
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
     * @param ActiveRecord $model
     * @param string $content
     * @param int|null $userId
     * @return bool
     * @throws D3ActiveRecordException
     * @throws Exception
     */
    public static function attach(ActiveRecord $model, string $content, int $userId = null): bool
    {
        $mapping = new NoteModel();
        $mapping->model_id = self::getSysModelId($model);
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
     * @throws D3ActiveRecordException
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
     * @param string|null $modelClass
     * @return array
     * @throws D3ActiveRecordException
     */
    public static function getAttachedNotes(ActiveRecord $model, ?int $userId = null, ?string $modelClass = null): array
    {
        
        $activeQuery = NoteModel::find()
            ->select([
                'd3l_notes.*',
                'userName' => 'user.username',
            ])
            ->leftJoin(
                'user',
                'user.id = d3l_notes.user_id'
            )
            ->where([
                'd3l_notes.model_id' => self::getSysModelId($model, $modelClass),
                'd3l_notes.model_record_id' => $model->id,
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
     * @param string|null $modelClass
     * @return null|int
     * @throws D3ActiveRecordException
     */
    private static function getSysModelId(ActiveRecord $model, ?string $modelClass = null): ?int
    {
        return SysModelsDictionary::getIdByClassName($modelClass ?? get_class($model));
    }
}