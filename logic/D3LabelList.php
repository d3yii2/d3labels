<?php

namespace d3yii2\d3labels\logic;

use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
use d3yii2\d3labels\models\SysModels;

/**
 * Class D3DefinitionList
 * @package d3yii2\d3labels\logic
 */
class D3LabelList
{
    public $model;
    private $availableLabels = [];
    private $attachedLabels = [];

    /**
     * D3LabelList constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;

        $sysModel = SysModels::findOne(['class_name' => get_class($this->model)]);

        if (!$sysModel) {
            return false;
        }

        $definitions = D3lDefinition::findAll(['model_id' => $sysModel->id]);

        if (!empty($definitions)) {
            foreach ($definitions as $def) {
                $this->availableLabels[$def->id] = $def;
            }
        }

        $labels = D3lLabel::findAll(['model_record_id' => $this->model->id]);

        if (!empty($labels)) {
            foreach ($labels as $label) {
                $this->attachedLabels[$label->definition_id] = $label;
            }
        }
    }

    /**
     * @param array $labels
     * @return string
     * @throws \Exception
     */
    public static function getAsBadges(array $labels, string $action, int $modelId): string
    {
        $items = self::getBadgeItems($labels, $action, $modelId);

        $list = \eaBlankonThema\widget\ThBadgeList::widget(['items' => $items]);

        return $list;
    }

    /**
     * @param array $labels
     * @return array
     */
    public static function getBadgeItems(array $labels, string $action, int $modelId): array
    {
        $items = [];

        foreach ($labels as $label) {
            $item = ['type' => $label->collor, 'text' => $label->label, 'faIcon' => $label->icon];
            if ('' !== $action) {
                $item['url'] = \yii\helpers\Url::to([$action, 'defId' => $label->id, 'modelId' => $modelId]);
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @return array
     */
    public function getAvailableForDropdown(): array
    {
        $items = [];

        foreach ($this->availableLabels as $id => $label) {
            if (!isset($this->attachedLabels[$id])) {
                $items[$id] = $label->label;
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    public function getAvailable(): array
    {
        return $this->availableLabels;
    }

    /**
     * @return array
     */
    public function getAttached(): array
    {
        return $this->attachedLabels;
    }

    /**
     * @return array
     */
    public function getNonAttached(): array
    {
        $nonAttached = [];

        foreach ($this->availableLabels as $label) {
            if (!isset($this->attachedLabels[$label->id])) {
                $nonAttached[] = $label;
            }
        }

        return $nonAttached;
    }
}