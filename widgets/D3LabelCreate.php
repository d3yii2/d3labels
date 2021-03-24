<?php

namespace d3yii2\d3labels\widgets;

use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
use Exception;
use Yii;
use yii\base\Widget;

/**
 * Class D3LabelCreate
 * @package d3yii2\d3labels\widgets
 * @property string $modelClass
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3LabelCreate extends Widget
{
    /**
     * @var string
     */
    public $modelClass;
    public $controller;

    /**
     * @var string
     */
    public $returnURLToken;
    public $createButtonPlacement = self::PLACEMENT_TOP;

    /** @var int */
    public $sysCompanyId;

    /** @var D3Definition */
    private $definition;

    public const PLACEMENT_PAGE_BUTTONS = 'button-bar';
    public const PLACEMENT_TOP = 'top';

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $this->definition = new D3Definition($this->modelClass);
    }


    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        $model = new D3lDefinition();
        $model->sys_company_id = Yii::$app->SysCmp->getActiveCompanyId();
        $model->model_id = $this->modelClass;

        return $this->renderFile(
            $modulePath . '/views/label/create.php',
            [
                'model' => $model,
                'controller' => $this->controller,
                'labelsList' => $this->renderFile(
                    $modulePath . '/views/label/_list.php',
                    [
                        'labels' => D3lDefinitionDictionary::rowlList($this->modelClass,$this->sysCompanyId),
                        'systemModelId' => $this->definition->getSystemModelId(),
                    ]
                ),
                'createButtonPlacement' => $this->createButtonPlacement,
            ]
        );
    }
}