<?php

namespace d3yii2\d3labels\components;

use d3system\compnents\ModelsList;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lDefinition;
use eaBlankonThema\components\FlashHelper;
use Yii;

/**
 * Class CreateAction
 * @package d3yii2\d3labels\components
 * @property object $model
 */
class CreateAction extends BaseAction
{
    /**
     * @return Yii\web\Response
     */
    public function run(): yii\web\Response
    {
        try {

            $model = new D3lDefinition();
            $model->sys_company_id =  \Yii::$app->SysCmp->getActiveCompanyId();
            $sysModels = new ModelsList();
            $model->model_id = $sysModels->getIdByClassName($this->modelName);

            if (!$model->load(Yii::$app->request->post(), 'D3lDefinition')) {
                throw new D3ActiveRecordException($model, 'Cannot load POST data');
            }

            $model->save();

            $msg = Yii::t('d3labels', 'Labels created successfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}