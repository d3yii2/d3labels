<?php

namespace d3yii2\d3labels\logic;

use d3system\compnents\ModelsList;
use d3yii2\d3labels\models\D3lDefinition;
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
    private $model;
    private $attachToModelClass;
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
    public function __construct(string $attachToModelClass, $label = null)
    {
        if (!class_exists($attachToModelClass)) {
            throw new \Exception('Model Class not exists: ' . $attachToModelClass);
        }

        $this->attachToModelClass = $attachToModelClass;
        $this->label = $label;
    }

    /**
     * Find the existing definition or throw the Exception if not found
     * @param int $defId
     * @return D3lDefinition
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
     * @throws \yii\db\Exception
     */
    public function save()
    {
        $def = $this->model ? $this->model : new D3lDefinition();

        $def->sys_company_id = $this->sysCompanyId;

        $modelObj = new $this->attachToModelClass();
        $sysModels = new ModelsList();
        $def->model_id = $sysModels->getIdByTableName($modelObj);
        $def->action_class = $this->actionClass;

        if (!$this->model) {
            $def->label = $this->label['title'];

            if (!empty($this->label['collor'])) {
                $def->collor = $this->label['collor'];
            }

            if (!empty($this->label['icon'])) {
                $def->icon = $this->label['icon'];
            }
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

    /**
     * Set the System company ID to global (null)
     */
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

    /**
     * @param D3lDefinition $model
     */
    public function setModel(D3lDefinition $model)
    {
        $this->model = $model;
    }
}