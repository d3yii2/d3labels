<?php

namespace d3yii2\d3labels\logic;

use d3system\compnents\ModelsList;
use d3system\exceptions\D3ActiveRecordException;
use d3system\widgets\ThBadgeList;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class D3LabelList
 * @package d3yii2\d3labels\logic
 * @property ActiveRecord $model
 * @property array $availableLabels
 * @property array $attachedLabels
 */
class D3LabelList
{
    public $model;
    private $availableLabels = [];
    private $attachedLabels = [];

    /**
     * D3LabelList constructor.
     * Read all available and attached Labels to class propeties
     * @param $model
     * @throws D3ActiveRecordException
     */
    public function __construct($model)
    {
        $this->model = $model;

        $modelList = new ModelsList();
        $sysModelId = $modelList->getIdByTableName($this->model);
        $definitions = D3lDefinition::findAll(['model_id' => $sysModelId]);

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
     * @param array $items
     * @param array $renderOptions
     * @return string
     * @throws Exception
     */
    public static function getAsBadges(array $items, array $renderOptions = []): string
    {
        return ThBadgeList::widget(['items' => $items, 'renderOptions' => $renderOptions]);
    }

    /**
     * Get the Labes as the Dropdown List
     * @param array $items
     * @param null $model
     * @param array $dropdownOptions
     * @return string
     */
    public static function getAsDropdown(array $items, $model = null, array $dropdownOptions): string
    {
        $dropdownOptions = array_merge([
            'class' => 'form-control limiter-max__150',
            'prompt' => Yii::t('d3labels', 'Filter by Label')
        ],
            $dropdownOptions
        );

        if ($model) {
            return Html::activeDropDownList(
                $model,
                'label_type',
                $items,
                $dropdownOptions
            );
        }

        return Html::dropDownList(
            'model_label_type',
            null,
            $items,
            $dropdownOptions
        );
    }

    /**
     * Get the d3system\widgets\ThBadge compatible items from the array of labels
     *
     * @param array $labels
     * @param string $action
     * @param int|null $modelId
     * @return array
     */
    public static function getBadgeItems(array $labels, string $action = '', int $modelId = null): array
    {
        $items = [];

        foreach ($labels as $label) {
            $item = self::labelToItem($label, $action, $modelId);

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Convert the Label data to d3system\widgets\ThBadge compatible item
     * @param $label
     * @param string $action
     * @param int|null $modelId
     * @return array
     */
    public static function labelToItem($label, string $action = '', int $modelId = null): array
    {
        $item = is_object($label)
            ? ['type' => $label->collor, 'text' => $label->label, 'faIcon' => $label->icon]
            : ['type' => $label['collor'], 'text' => $label['label'], 'faIcon' => $label['icon']];

        if ('' !== $action) {
            $labelId = is_object($label) ? $label->id : $label['id'];
            $item['url'] = Url::to([
                $action,
                'defId' => $labelId,
                'modelId' => $modelId,
            ]);
        }

        return $item;
    }

    /**
     * Get the Available Labels as the dropdown comptible data id => title
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
     * Get the Labels that are not attached to model
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