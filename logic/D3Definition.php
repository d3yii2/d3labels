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
    public function __construct(string $modelClass, array $label)
    {
        if (!class_exists($modelClass)) {
            throw new \Exception('Model Class not exists: ' . $modelClass);
        }

        if (empty($label['title'])) {
            throw new \Exception('Missing label title');
        }

        $this->modelClass = $modelClass;
        $this->label = $label;
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

    /**
     * @param int $id
     */
    public function setSysCompanyId(int $id)
    {
        $this->sysCompanyId = $id;
    }


    public function setGlobalSysComampanyId()
    {
        $this->sysCompanyId = null;
    }

    /**
     * @param string $icon
     */
    public function setLabelIcon(string $icon)
    {
        $this->label['icon'] = $icon;
    }

    /**
     * @param string $collor
     */
    public function setLabelColor(string $collor)
    {
        $this->label['collorl'] = $collor;
    }
}