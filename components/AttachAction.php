<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Label;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;

/**
 * Class AttachAction
 * Attach Label to Model
 * @package d3yii2\d3labels\components
 */
class AttachAction extends BaseAction
{
    /**
     * @param int $defId
     * @param int $modelId
     * @return yii\web\Response
     */
    public function run(int $defId, int $modelId): yii\web\Response
    {
        try {

            $this->loadModel($modelId);
            D3Label::attach($modelId, $defId);
            $msg = Yii::t('d3labels', 'Label attached');
            FlashHelper::addSuccess($msg);
        } catch (Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}