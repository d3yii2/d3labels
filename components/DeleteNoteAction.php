<?php

namespace d3yii2\d3labels\components;

use cornernote\returnurl\ReturnUrl;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3Note;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;
use Yii\web\Response;

/**
 * Class AttachAction
 * Attach Label to Model
 * @package d3yii2\d3labels\components
 */
class DeleteNoteAction extends BaseAction
{

    /**
     * action settings
     */
    /** @var string[] define access fo label deleting ['closed' => ['roleName1','roleName2']  */
    public array $accessRoles = [];

    /**
     * @param int $modelId
     * @param int $noteId
     * @return Response|string
     * @throws NotFoundHttpException
     * @throws D3ActiveRecordException|Throwable
     */
    public function run(int $modelId, int $noteId): yii\web\Response
    {
        $this->loadModel($modelId);
        if (!$note = D3Note::findOne([
            'id' => $noteId,
            'model_record_id' => $modelId,
        ])) {
            FlashHelper::addInfo(Yii::t('d3labels', 'Can not find attached note'));
            return $this->controller->redirect(ReturnUrl::getUrl());
        }
        if ($this->accessRoles) {
            $hasAccess = false;
            foreach ($this->accessRoles as $roleName) {
                if (Yii::$app->user->can($roleName)) {
                    $hasAccess = true;
                    break;
                }
            }
            if (!$hasAccess) {
                FlashHelper::addDanger(Yii::t(
                    'd3labels',
                    'You do not have rights for removing note'
                ));
                return $this->controller->redirect(ReturnUrl::getUrl());
            }
        }
        try {
            $note->delete();
            FlashHelper::addSuccess(Yii::t('d3labels', 'Note removed successfully'));
        } catch (Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }
        return $this->controller->redirect(ReturnUrl::getUrl());
    }
}
