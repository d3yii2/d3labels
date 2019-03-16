<?php

namespace d3yii2\d3labels\logic;

use d3system\compnents\ModelsList;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lDefinition;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

/**
 * Class D3Definition
 * @package d3yii2\d3labels\logic
 * @property object $model
 * @property string $attachToModelClass
 * @property string $actionClass
 * @property array $label;
 * @property int $sysCompanyId
 */
class D3Definition
{
    private $class;


    /** @var null string */
    private $label;

    /** @var null string */
    private $icon;

    /** @var null string */
    private $color;


    private $sysCompanyId;

    /**
     * D3Definition constructor.
     * @param string $class
     * @throws \Exception
     */
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new \Exception('Model Class not exists: ' . $class);
        }

        $this->class = $class;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * Find the existing definition or throw the Exception if not found
     * @param int $id
     * @return D3lDefinition
     * @throws NotFoundHttpException
     */
    public static function loadDefinition(int $id): D3lDefinition
    {
        $def = D3lDefinition::findOne($id);

        if (!$def) {
            throw new NotFoundHttpException('Definition not found');
        }

        return $def;
    }

    /**
     * Save the label definitions
     * @throws D3ActiveRecordException
     */
    public function save(): void
    {
        $def = new D3lDefinition();

        $def->sys_company_id = $this->sysCompanyId;

        $modelObj = new $this->class;
        $sysModels = new ModelsList();
        $def->model_id = $sysModels->getIdByTableName($modelObj);
        //$def->action_class = $this->actionClass;


        $def->label = $this->label;

        if ($this->color) {
            $def->collor = $this->color;
        }

        if ($this->icon) {
            $def->icon = $this->icon;
        }

        if($this->sysCompanyId){
            $def->sys_company_id = $this->sysCompanyId;
        }
        echo VarDumper::dumpAsString($def->getAttributes());
        if(!$def->save($def)){
            throw new D3ActiveRecordException($def);
        }
    }

    /**
     * @param int $id
     */
    public function setSysCompanyId(int $id): void
    {
        $this->sysCompanyId = $id;
    }

    public function setModel(D3lDefinition $label): void
    {
        $this->setLabel($label->label);
        $this->setIcon($label->icon);
        $this->setColor($label->collor);
    }


}