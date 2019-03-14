<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\logic\D3Label;
use eaBlankonThema\components\FlashHelper;
use Yii;

/**
 * Class AttachAction
 * Attach Label to Model
 * @package d3yii2\d3labels\components
 */
class AttachAction extends D3LabelAction
{
    /**
     * @param int $defId
     * @param int $modelId
     * @return string
     */
    public function run(int $defId, int $modelId)
    {
        try {

            $this->loadModel($modelId);

            $def = D3Definition::loadDefinition($defId);

            D3Label::attach($modelId, $def);

            $msg = Yii::t('d3labels', 'Label attached  sucessfully');
            $def = D3Definition::loadDefinition($defId);

            D3Label::attach($modelId, $def);

            $msg = Yii::t('d3labels', 'Label attached  sucessfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}