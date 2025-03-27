<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\models\D3lDefinition;
use eaBlankonThema\components\FlashHelper;
use Exception;
use RuntimeException;
use Yii;
use yii\helpers\Json;

/**
 * Class EditAction
 * @package d3yii2\d3labels\components
 * @property object $model
 */
class DefinitionEditAction extends BaseAction
{
    public bool $showCode = false;

    /**
     * @throws \yii\db\Exception
     */
    public function run()
    {
        if(Yii::$app->request->isAjax) {
            $request = Json::decode(Yii::$app->request->post()['definition'], false);
            $modulePath = Yii::$app->getModule('d3labels')->basePath;

            $model = D3lDefinition::findOne([
                'id' => $request->definitionId,
                'model_id' => $request->modelId,
                'sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId()
            ]);
            return Json::encode([
                'content' => $this
                    ->controller
                    ->renderFile(
                        $modulePath . '/views/label/edit.php',
                        [
                            'model' => $model,
                            'showCode' => $this->showCode,
                        ]
                    )
            ]);
        }
        if ($request = Yii::$app->request->post()['D3lDefinition']) {
            $model = D3lDefinition::findOne([
                'id' => $request['id'],
                'model_id' => $request['model_id'],
                'sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId()
            ]);
            if (!$model) {
                throw new RuntimeException(Yii::t('d3labels', 'Label definition record not exists'));
            }
            try {

                $model->collor = $request['collor'];
                $model->label = $request['label'];
                $model->icon = $request['icon'];
                $model->code = $request['code']??null;
                $model->save();

                FlashHelper::addSuccess(Yii::t('d3labels', 'Edit saved'));
            } catch (Exception $e) {
                Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                $model->addError('_exception', $e->getMessage());
            }
            return $this->redirect();
        }
        return '';
    }
}