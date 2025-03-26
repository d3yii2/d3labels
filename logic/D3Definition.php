<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\widgets\ThBadge;
use d3yii2\d3labels\components\CreateAction;
use d3yii2\d3labels\components\DefinitionDeleteAction;
use d3yii2\d3labels\components\DefinitionEditAction;
use d3yii2\d3labels\models\D3lDefinition;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

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
        $this->definitionModel->label = $label;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->definitionModel->icon = $icon;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->definitionModel->collor = $color;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->definitionModel->code = $code;
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
     */
    public function loadFromForm(): void
    {
        $formAttrs = ['label', 'icon', 'collor'];

        $post = Yii::$app->request->post('D3lDefinition');

        foreach ($formAttrs as $attr) {
            $this->definitionModel->{$attr} = $post[$attr] ?? null;
        }
    }

    /**
     * Save the label definitions
     * @throws D3ActiveRecordException
     */
    public function save(): void
    {
        $this->definitionModel->model_id = $this->getSystemModelId();
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
        return SysModelsDictionary::getIdByClassName($this->class);
    }

    /**
     * @param int $id
     */
    public function setCompanyId(int $id): void
    {
        $this->definitionModel->sys_company_id = $id;
    }

    public function setModel(D3lDefinition $label): void
    {
        $this->setLabel($label->label);
        $this->setIcon($label->icon);
        $this->setColor($label->collor);
        $this->setCode($label->code);
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
     * @param string $modelClass
     * @param int|null $sysCompanyId
     * @return array
     *
     */
    public static function getControllerActionConfig(string $modelClass, ?int $sysCompanyId = null): array
    {
        return [
            self::getCreateActionName($modelClass) => [
                'class' => CreateAction::class,
                'modelName' => $modelClass,
                'sysCompanyId' => $sysCompanyId,
            ],
            self::getRemoveActionName($modelClass) => [
                'class' => DefinitionDeleteAction::class,
                'modelName' => $modelClass,
                'sysLabelsIdList' => [2]
            ],
            self::getEditActionName($modelClass) => [
                'class' => DefinitionEditAction::class,
            ],
        ];
    }

    /**
     * @param string $modelClass
     * @return array
     *
     */
    public static function getControllerActionNames(string $modelClass): array
    {
        return [
            self::getCreateActionName($modelClass),
            self::getRemoveActionName($modelClass),
            self::getEditActionName($modelClass),
        ];
    }

    /**
     * @param string $modelClass
     * @return string
     */
    public static function getCreateActionName(string $modelClass): string
    {

        return self::getActionName($modelClass, 'definitioncreate');
    }

    /**
     * @param string $modelClass
     * @return string
     */
    public static function getEditActionName(string $modelClass): string
    {

        return self::getActionName($modelClass, 'definitionedit');
    }

    /**
     * @param string $modelClass
     * @return string
     */
    public static function getRemoveActionName(string $modelClass): string
    {

        return self::getActionName($modelClass, 'definitionremove');
    }

    /**
     * @param string $modelClass
     * @param string $type
     * @return string
     */
    public function getActionName(string $modelClass, string $type): string
    {
        $classParts = explode('\\', $modelClass);
        $classShortName = strtolower(end($classParts));

        return 'd3labels' . $classShortName . $type;
    }

}
