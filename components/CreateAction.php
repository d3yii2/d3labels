<?php

namespace d3yii2\d3labels\components;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lLabelForm;
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
     * @throws \d3system\exceptions\D3Exception
     */
    public function run(): yii\web\Response
    {
        try {

            $form = Yii::$app->request->post('D3lLabelForm');

            $formModel = new D3lLabelForm();

            if (!$formModel->load(Yii::$app->request->post(), 'D3lDefinition')) {
                throw new D3ActiveRecordException($formModel, 'Cannot load POST data');
            }

            $formModel->save();

            $msg = Yii::t('d3labels', 'Labels created successfully');
            FlashHelper::addSuccess($msg);
        } catch (\Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}