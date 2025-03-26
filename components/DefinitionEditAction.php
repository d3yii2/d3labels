<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;
use yii\helpers\Json;

/**
 * Class EditAction
 * @package d3yii2\d3labels\components
 * @property object $model
 */
class DefinitionEditAction extends BaseAction
{
    public function run()
    {
        if(Yii::$app->request->isAjax) {
            $request = Json::decode(Yii::$app->request->post()['definition'], false);
            $modulePath = Yii::$app->getModule('d3labels')->basePath;

            $model = D3lDefinition::find()
                ->where([
                    D3lDefinition::tableName() . '.id' => $request->definitionId,
                    D3lDefinition::tableName() . '.model_id' => $request->modelId,
                    D3lDefinition::tableName() . '.sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId()
            ])
                ->joinWith('model')
                ->one();
            
            $attachedModelClass = $model->model->class_name;
            
            return Json::encode(
                [
                    'content' => $this->controller->renderFile(
                        $modulePath . '/views/label/edit.php',
                        [
                            'model' => $model,
                            'editAction' => D3Definition::getEditActionName($attachedModelClass),
                        ]
                    )
                ]);
        } else if ($request = Yii::$app->request->post()['D3lDefinition']) {
            try {
                $model = D3lDefinition::findOne([
                    'id' => $request['id'],
                    'model_id' => $request['model_id'],
                    'sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId()
                ]);

                $model->collor = $request['collor'];
                $model->label = $request['label'];
                $model->icon = $request['icon'];
                $model->save();

                FlashHelper::addSuccess(Yii::t('d3labels', 'Edit saved'));
            } catch (Exception $e) {
                Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                $model->addError('_exception', $e->getMessage());
            }

            return $this->redirect();
        }
    }
}
