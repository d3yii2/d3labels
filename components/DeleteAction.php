<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Label;
use eaBlankonThema\components\FlashHelper;
use Yii;

/**
 * Class DeleteAction
 * Deletes an existing Label attached to Model
 * @package d3yii2\d3labels\components
 */
class DeleteAction extends BaseAction
{

    /**
     * @param int $modelId
     * @param int $labelId
     * @return Yii\web\Response
     * @throws \Throwable
     */
    public function run(int $modelId, int $labelId): yii\web\Response
    {
        try {

            $this->loadModel($modelId);

            D3Label::remove($labelId);

            $msg = Yii::t('d3labels', 'Label removed sucessfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}