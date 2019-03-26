<?php

namespace d3yii2\d3labels\logic;

use Yii;
use d3system\compnents\ModelsList;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\models\D3lDefinition;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use d3system\widgets\ThBadge;

/**
 * Class D3Definition
 * @package d3yii2\d3labels\logic
 * @property string $class
 * @property string $label
 * @property string $icon
 * @property string $color
 * @property int $sysCompanyId
 * @property D3lDefinition
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

    private $definitionModel;

    /**
     * D3Definition constructor.
     * @param string $class
     * @throws \Exception
     */
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new Exception('Model Class not exists: ' . $class);
        }

        $this->class = $class;

        $this->definitionModel = new D3lDefinition();
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
     * Load form data into D3lDefinition model
     * @throws D3ActiveRecordException
     */
    public function loadFromForm()
    {
        if (!$this->definitionModel->load(Yii::$app->request->post(), 'D3lDefinition')) {
            throw new D3ActiveRecordException($this->definitionModel, 'Cannot load POST data');
        }

        $this->definitionModel->sys_company_id = $this->sysCompanyId;

        $this->definitionModel->model_id = $this->getSystemModelId();


        if ($this->color) {
            $this->definitionModel->collor = $this->color;
        }

        /*if ($this->icon) {
            $def->icon = $this->icon;
        }*/
    }

    /**
     * Save the label definitions
     * @throws D3ActiveRecordException
     */
    public function save(): void
    {
        $this->definitionModel->save();
    }


    /**
     * Get System Model ID from current model class
     *
     * @return int
     * @throws D3ActiveRecordException
     */
    public function getSystemModelId(): int
    {
        $modelObj = new $this->class;
        $sysModels = new ModelsList();
        return $sysModels->getIdByTableName($modelObj);
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
     *
     * @param null $modelId
     * @return array
     * @throws D3ActiveRecordException
     */
    public function getAllByModel($modelId = null): array
    {
        if (!$modelId) {
            $modelId = $this->getSystemModelId();
        }

        return D3lDefinition::findAll(['model_id' => $modelId]);

    }
}