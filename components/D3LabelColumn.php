<?php

namespace d3yii2\d3labels\components;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\logic\D3LabelList;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
use Exception;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class D3LabelColumn
 * @package d3yii2\d3labels\components
 * @property object $model
 * @property array $badgeRenderOptions
 * @property array $dataProviderIds
 * @property array $recordsWithLabels
 */
class D3LabelColumn extends DataColumn
{
    public const COLUMN_CLASS = 'badge-column';

    /**
     * search model
     */
    public $model;

    /**
     * @var string|null search model class name
     */
    public ?string $modelClass = null;
    public array $badgeRenderOptions = [];

    /**
     * @var bool show icon without label name
     */
    public bool $showOnlyIcon = false;

    public array $filterListboxOptions = [];

    /**
     * @var string|null filter prompt text
     */
    public ?string $filterPrompt = null;


    /** @var string  */
    public $attachLink;

    /**
     * @var bool if tue, additionaly allow filter records, where not assigned label
     */
    public $filterNotAssignedLabel = false;

    /** @var int */
    public $sysCompanyId;

    public $dataProvider;

    /** @var array list of toggle labels  */
    public $toggleLabelCodes;

    /**
     * @var int|null show only user labels
     */
    public ?int $showLabelsForUserId = null;

    private array $dataProviderIds = [];
    private array $recordsWithLabels = [];

    private array $toggleLabelsDef = [];

    public string $primaryKey = 'id';

    /**
     * Set the initial properties on class init
     * @throws D3ActiveRecordException
     */
    public function init(): void
    {
        $this->initLabels();

        parent::init();
    }

    /**
     * Read all the records containing attached labels into $this->recordsWithLabels array
     *
     * @throws D3ActiveRecordException
     */
    private function initLabels(): void
    {
        if($this->dataProvider){
            $dataProvider = clone $this->dataProvider;
        }else {
            $dataProvider = clone $this->grid->dataProvider;
        }
        //$dataProvider->sort = false;
        $rows = $dataProvider->getModels();
        $this->dataProviderIds = ArrayHelper::getColumn($rows, $this->primaryKey);

        $recordsWithLabels = D3lLabel::getAllByModelRecordIds(
            $this->dataProviderIds,
            $this->modelClass,
            $this->showLabelsForUserId
        );

        foreach ($recordsWithLabels as $labelModel) {
            if (!isset($this->recordsWithLabels[$labelModel['model_record_id']])) {
                $this->recordsWithLabels[$labelModel['model_record_id']] = [];
            }

            $this->recordsWithLabels[$labelModel['model_record_id']][$labelModel['definition_id']] = $labelModel;
        }
        if ($this->toggleLabelCodes) {
            $this->toggleLabelsDef = ArrayHelper::index(
                D3lDefinition::find()
                    ->where(['code' => $this->toggleLabelCodes])
                    ->asArray()
                    ->all(),
                'id'
            );
        }

        // need to toggle class provided from label definition
        if ($this->attachLink) {
            $columnClass = self::COLUMN_CLASS;
            Yii::$app->view->registerJs("
                   $('.$columnClass').on('click', function() {
                            var modelId = $(this).parents('tr').attr('data-key');
                            var url = '$this->attachLink' + '&modelId=' + modelId;
                            
                            $.ajax({
                              url: url,
                              context: this
                            }).done(function() {
                              $( this ).toggleClass('badge-default').toggleClass( 'badge-info' );
                            });
                            
                   }); 
                ");
        }
    }

    /**
     * Render the labels inside grid data cell
     * @param $model
     * @param $key
     * @param $index
     * @return string
     * @throws Exception
     */
    public function renderDataCellContent($model, $key, $index): string
    {
        if ($this->toggleLabelsDef) {
            $labelItems = [];
            foreach ($this->toggleLabelsDef as $label) {
                if (!isset($this->recordsWithLabels[$model->{$this->primaryKey}][((int)$label['id'])])) {
                    $label['collor'] = 'default';
                }
                $labelItems[] = D3LabelList::labelToItem($label, '', null, $this->showOnlyIcon);
            }
        } else {
            if (empty($this->recordsWithLabels[$model->id])) {
                return '';
            }
            $labelItems = D3LabelList::getBadgeItems($this->recordsWithLabels[$model->id]);
        }
        return D3LabelList::getAsBadges($labelItems, $this->badgeRenderOptions, ['class' => self::COLUMN_CLASS]);
    }

    public function renderForExcel($model): string
    {
        if (empty($this->recordsWithLabels[$model->id])) {
            return '';
        }
        $labelItems = D3LabelList::getBadgeItems($this->recordsWithLabels[$model->id]);
        return implode(', ', ArrayHelper::getColumn($labelItems,'text'));
    }

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     * @throws D3ActiveRecordException
     */
    protected function renderFilterCellContent(): string
    {
        if ($this->filter === false) {
            return '';
        }
        $items = D3lDefinitionDictionary::getList($this->sysCompanyId, $this->modelClass);

        if($this->filterNotAssignedLabel){
            $list = $items;
            foreach($list as $listKey => $listLabel){
                $items['!' . $listKey] = '! ' . $listLabel;
            }
        }

        if ($this->showOnlyIcon) {
            $this->filterListboxOptions['encode'] = false;
        }
        return D3LabelList::getAsDropdown(
            $items,
            $this->filterListboxOptions,
            $this->model,
            $this->filterPrompt
        );
    }
}
