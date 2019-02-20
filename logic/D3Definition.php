<?php

namespace d3yii2\d3labels\logic;

use d3system\compnents\ModelsList;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;

/**
 * Class Label
 * @package d3yii2\d3labels\logic
 */
class D3Definition
{
    private $modelClass;
    private $actionClass;
    private $label;
    private $sysCompanyId;

    /**
     * D3Definition constructor.
     * @param string $actionClass
     * @param array $label
     * @param int $sysCompany
     * @throws \Exception
     */
    public function __construct(string $modelClass, string $actionClass, array $label, int $sysCompany)
    {
        if (!class_exists($modelClass)) {
            throw new \Exception('Model Class not exists: ' . $modelClass);
        }

        if (!class_exists($actionClass)) {
            throw new \Exception('Action Class not exists: ' . $actionClass);
        }

        if (empty($label['title'])) {
            throw new \Exception('Missing label title');
        }

        $this->modelClass = $modelClass;
        $this->actionClass = $actionClass;
        $this->label = $label;
        $this->sysCompanyId = $sysCompany;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function save()
    {
        $def = new D3lDefinition();
        $def->sys_company_id = $this->sysCompanyId;

        $modelObj = new $this->modelClass();
        $sysModels = new ModelsList();
        $def->model_id = $sysModels->getIdByTableName($modelObj);
        $def->action_class = $this->actionClass;
        $def->label = $this->label['title'];

        if (!empty($this->label['collor'])) {
            $def->collor = $this->label['collor'];
        }

        if (!empty($this->label['icon'])) {
            $def->icon = $this->label['icon'];
        }

        $def->saveOrException($def);
    }
}