<?php

namespace d3yii2\d3labels\logic;

use Yii;
use d3system\compnents\ModelsList;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lDefinition;
use yii\web\NotFoundHttpException;
use d3system\widgets\ThBadge;

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

        $def->model_id = $this->getSystemModelId();

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

        if(!$def->save($def)){
            $errors = $def->getErrors();

            $errMsg = isset($errors['model_id'])
                ? Yii::t('d3labels', 'Label with this name exists already')
                : Yii::t('d3labels', 'Unable to create Label');

            throw new D3ActiveRecordException($def, $errMsg);
        }
    }

    /**
     * Get System Model ID from current model class
     * @return int
     */
    public function getSystemModelId(): int
    {
        $modelObj = new $this->class;
        $sysModels = new ModelsList();
        $modeId = $sysModels->getIdByTableName($modelObj);

        return $modeId;
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

    /**
     * Get available Colors for Dropdown
     * @return array
     */
    public static function getColors(): array
    {
        return [
            ThBadge::TYPE_SUCCESS => Yii::t('d3labels-colors', ThBadge::TYPE_SUCCESS),
            ThBadge::TYPE_INFO => Yii::t('d3labels-colors', ThBadge::TYPE_INFO),
            ThBadge::TYPE_WARNING => Yii::t('d3labels-colors', ThBadge::TYPE_WARNING),
            ThBadge::TYPE_DANGER => Yii::t('d3labels-colors', ThBadge::TYPE_DANGER),
            ThBadge::TYPE_DEFAULT => Yii::t('d3labels-colors', ThBadge::TYPE_DEFAULT),
            ThBadge::TYPE_PRIMARY => Yii::t('d3labels-colors', ThBadge::TYPE_PRIMARY),
            ThBadge::TYPE_LILAC => Yii::t('d3labels-colors', ThBadge::TYPE_LILAC),
            ThBadge::TYPE_INVERSE => Yii::t('d3labels-colors', ThBadge::TYPE_INVERSE),
            ThBadge::TYPE_TEALS => Yii::t('d3labels-colors', ThBadge::TYPE_TEALS),
        ];
    }

    /**
     * Get all label Definitions for system model
     * @param int|null $modelId
     * @return D3lDefinition[]
     */
    public function getAllByModel($modelId = null): array
    {
        if (!$modelId) {
            $modelId = $this->getSystemModelId();
        }

        $defs = D3lDefinition::findAll(['model_id' => $modelId]);

        return $defs;
    }
}