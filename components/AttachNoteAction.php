<?php

namespace d3yii2\d3labels\components;

use cornernote\returnurl\ReturnUrl;
use d3yii2\d3labels\models\forms\Note;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;
use yii\web\NotFoundHttpException;
use Yii\web\Response;

/**
 * Class AttachAction
 * Attach Label to Model
 * @package d3yii2\d3labels\components
 */
class AttachNoteAction extends BaseAction
{

    /**
     * action settings
     */
    /** @var int|null show comments of userId */
    public ?int $userId = null;

    /** @var string|null form view path */
    public ?string $view = '@d3yii2/d3labels/views/note/create';

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function run(int $id)
    {
        $this->loadModel($id);
        $model = new Note();
        $model->model = $this->model;
        $model->userId = $this->userId;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $transaction->commit();
                $msg = Yii::t('d3labels', 'Note attached');
                FlashHelper::addSuccess($msg);
                return $this->controller->redirect(ReturnUrl::getUrl());
            }
        } catch (Exception $err) {
            $transaction->rollBack();
            FlashHelper::addDanger($err->getMessage());
        }
        return $this->controller->render($this->view, ['model' => $model]);
    }
}
