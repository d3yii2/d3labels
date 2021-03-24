<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\models\D3lDefinition;
use eaBlankonThema\components\FlashHelper;
use Throwable;
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
     * @var array Labels used by the system and not allowed to delete
     */
    public $sysLabelsIdList = [];

    /**
     * @param int $modelId
     * @param int $definitionId
     * @return Yii\web\Response
     * @throws Throwable
     */
    public function run(int $modelId, int $definitionId): yii\web\Response
    {
        try {

            if (in_array($definitionId, $this->sysLabelsIdList, true)) {
                throw new Exception(Yii::t('d3labels', 'Can not delete system labels(A)'));
            }

            $model = D3lDefinition::findOne([
                'id' => $definitionId,
                'model_id' => $modelId,
                'sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId()
            ]);
            if(!$model->sys_company_id){
                throw new Exception(Yii::t('d3labels', 'Can not delete system labels(B)'));
            }
            if (!$model) {
                throw new Exception(Yii::t('d3labels', 'Label definition record not exists'));
            }

            $hasLabels = $model->d3lLabels;

            if ($hasLabels) {
                FlashHelper::addWarning(Yii::t('d3labels', 'Cannot delete.  Attached to records'));
            } elseif (!$model || !$model->delete()) {
                FlashHelper::addWarning(Yii::t('d3labels', 'Not exists or deleted already'));
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