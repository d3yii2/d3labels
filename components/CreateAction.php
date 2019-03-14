<?php

namespace d3yii2\d3labels\components;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lLabelForm;
use eaBlankonThema\components\FlashHelper;
use Yii;

/**
 * Class CreateAction
 * @package d3yii2\d3labels\components
 */
class CreateAction extends BaseAction
{
    public $model;

    /**
     * @return string
     */
    public function run()
    {
        try {

            $form = Yii::$app->request->post('D3lLabelForm');

            $controllerModelId = isset($form['controllerModelId']);

            $formModel = new D3lLabelForm();

            if ($controllerModelId) {
                $this->loadModel($form['controllerModelId']);
            }

            if (!$formModel->load(Yii::$app->request->post(), 'D3lDefinition')) {
                throw new D3ActiveRecordException($formModel, 'Cannot load POST data');
            }

            if (!$formModel->save()) {
                $msg = \Yii::t('d3labels', 'Unable to create Label');
                throw new D3ActiveRecordException($formModel, $msg);
            }

            $msg = Yii::t('d3labels', 'Labels created sucessfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}