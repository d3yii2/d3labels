<?php

namespace d3yii2\d3labels\widgets;

use d3system\widgets\ThBadge;
use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
use Yii;
use yii\helpers\Html;

/**
 * Class D3LabelCreate
 * @package d3yii2\d3labels\widgets
 * @property string $modelClass
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3LabelCreate extends \yii\base\Widget
{
    public $modelClass;
    public $controller;
    public $returnURLToken;

    /** @var D3Definition */
    private $definition;


    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $this->definition = new D3Definition($this->modelClass);
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        $model = new D3lDefinition();
        $model->sys_company_id =  \Yii::$app->SysCmp->getActiveCompanyId();
        $model->model_id = $this->modelClass;

        return $this->renderFile(
            $modulePath . '/views/label/_create.php',
            [
                'model' => $model,
                'controller' => $this->controller,
                'labelsList' => $this->renderFile(
                    $modulePath . '/views/label/list.php',
                    [
                        'labels' => $this->definition->getAllByModel(),
                        'systemModelId' => $this->definition->getSystemModelId(),
                    ]
                )
            ]
        );
    }
}