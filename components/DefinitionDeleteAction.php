<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\logic\D3Label;
use d3yii2\d3labels\models\D3lDefinition;
use eaBlankonThema\components\FlashHelper;
use Yii;
use yii\base\Exception;

/**
 * DefinitionDeleteAction
 * Deletes an existing Label attached to Model
 * @package d3yii2\d3labels\components
 */
class DefinitionDeleteAction extends BaseAction
{

    /**
     * @return yii\web\Response
     */
    public function run(int $modelId, int $definitionId): yii\web\Response
    {
        try {
            $model = D3lDefinition::findOne(['id' => $definitionId, 'model_id' => $modelId]);

            if (!$model) {
                throw new Exception(Yii::t('d3labels','Label definition record not exists'));
            }

            $hasLabels = $model->d3lLabels;

            if ($hasLabels) {
                FlashHelper::addWarning(Yii::t('d3labels','Cannot delete.  Attached to records'));
            } elseif (!$model || !$model->delete()) {
                FlashHelper::addWarning(Yii::t('d3labels','Not exists or deleted already'));
            } else {

                $msg = Yii::t('d3labels', 'Deleted sucessfully');
                FlashHelper::addSuccess($msg);
            }
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}