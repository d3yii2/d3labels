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
class DeleteAction extends D3LabelAction
{

    /**
     * @param int $modelId
     * @param int $labelId
     * @return mixed
     * @throws \Throwable
     */
    public function run(int $modelId, int $labelId)
    {
        try {

            $this->loadModel($modelId);

            D3Label::remove($labelId);

            $msg = Yii::t('d3labels', 'Label removed sucessfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->loadView();
    }
}