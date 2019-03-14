<?php

namespace d3yii2\d3labels\widgets;

use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabelForm;
use Yii;

/**
 * Class D3LabelList
 * @package d3yii2\d3labels\widgets
 */
class D3LabelCreate extends \yii\base\Widget
{
    public $model;
    public $controller;
    public $returnURLToken;

    /**
     * @return string
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        $formModel = new D3lLabelForm();

        $formModel->modelClass = get_class($this->model);

        $formModel->controllerModelId = $this->model->id;

        $formModel->labels[] = new D3lDefinition();

        return $this->renderFile(
            $modulePath . '/views/label/_create.php',
            [
                'model' => $formModel,
                'controller' => $this->controller,
                'returnURLToken' => $this->returnURLToken,
            ]
        );
    }
}