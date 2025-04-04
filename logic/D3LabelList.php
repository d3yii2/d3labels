<?php

namespace d3yii2\d3labels\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\widgets\ThBadgeList;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3lLabel;
use Exception;
use Throwable;
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
    private array $availableLabels = [];
    private array $attachedLabels = [];

    /**
     * D3LabelList constructor.
     * Read all available and attached Labels to class propeties
     * @param $model
     * @param int $sysCompanyId
     * @throws D3ActiveRecordException
     */
    public function __construct($model, int $sysCompanyId)
    {
        $this->model = $model;
        $sysModelId = SysModelsDictionary::getIdByClassName(get_class($this->model));
        //$definitions = D3lDefinition::findAll(['model_id' => $sysModelId]);
        foreach (D3lDefinitionDictionary::getList($sysCompanyId,$sysModelId) as $defId => $defLabel) {
            $this->availableLabels[$defId] = $defLabel;
        }


        $labels = D3lLabel::findAll([
            'model_record_id' => $this->model->id]);

        if (!empty($labels)) {
            foreach ($labels as $label) {
                $this->attachedLabels[$label->definition_id] = $label;
            }
        }
    }

    /**
     * @param array $items
     * @param array $renderOptions
     * @param array $htmlOptions
     * @return string
     * @throws Exception|Throwable
     */
    public static function getAsBadges(array $items, array $renderOptions = [], array $htmlOptions = []): string
    {
        return ThBadgeList::widget(['items' => $items, 'renderOptions' => $renderOptions, 'htmlOptions' => $htmlOptions ]);
    }

    /**
     * Get the Labes as the Dropdown List
     * @param array $items
     * @param array $dropdownOptions
     * @param null $model
     * @param string|null $prompt
     * @return string
     */
    public static function getAsDropdown(
        array $items,
        array $dropdownOptions,
        $model = null,
        string $prompt = null
    ): string {

        $dropdownOptions = array_merge([
            'class' => 'form-control limiter-max__150',
            'prompt' => $prompt ?? Yii::t('d3labels', 'Filter by Label')
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
            $items[] = self::labelToItem($label, $action, $modelId);
        }

        return $items;
    }

    /**
     * Convert the Label data to d3system\widgets\ThBadge compatible item
     * @param $label
     * @param string $action
     * @param int|null $modelId
     * @param bool $showOnlyIcon
     * @return array
     */
    public static function labelToItem(
        $label,
        string $action = '',
        int $modelId = null,
        bool $showOnlyIcon = false
    ): array {
        $item = is_object($label)
            ? ['type' => $label->collor, 'text' => $showOnlyIcon?'':$label->label, 'faIcon' => $label->icon]
            : ['type' => $label['collor'], 'text' => $showOnlyIcon?'':$label['label'], 'faIcon' => $label['icon']];

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

        foreach ($this->availableLabels as $id => $label) {
            if (!isset($this->attachedLabels[$id])) {
                $nonAttached[$id] = $label;
            }
        }
        return $nonAttached;
    }
}
