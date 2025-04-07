<?php

namespace d3yii2\d3labels\widgets;

use d3system\exceptions\D3ActiveRecordException;
use d3system\widgets\D3Widget;
use d3system\widgets\ThBadge;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\logic\D3LabelList as LabelLogic;
use d3yii2\d3labels\models\D3lLabel;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThButtonDropDown;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * displaying, attaching and removing labels to model
 *
 * Class D3LabelList
 * @package d3yii2\d3labels\widgets
 * @property object $model
 * @property string $title
 * @property string $titleDescription
 * @property array $titleHtmlOptions
 * @property bool $collapsed
 * @property array $tableOptions
 * @property bool $headerIconsWithText
 * @property bool $gridIconsWithText
 * @property string $returnURLToken
 * @property LabelLogic $_d3LabelList
 * @property string $_controllerRoute $controllerRoute
 */
class D3LabelList extends D3Widget
{
    /** @var ActiveRecord|object|null  */
    public ?object $model = null;
    public ?string $title = null;
    public ?string $titleDescription = null;
    public array $titleHtmlOptions = ['style' => 'padding-bottom:0'];
    public bool $collapsed = false;
    public array $tableOptions = [
        'class' => 'table table-striped table-success table-bordered'
    ];
    public bool $gridIconsWithText = false;
    public ?string $returnURLToken = null;
    public $returnURL;

    public bool $readOnly = false;

    /** @var int|null */
    public ?int $sysCompanyId = null;

    private $_controllerRoute;
    private array $attached = [];
    private array $noAttached = [];

    /**
     * @return void
     * @throws D3ActiveRecordException
     */
    public function init(): void
    {
        parent::init();

        $modelClassName = get_class($this->model);
        $attachedDefIdList = D3lLabel::findModelAttachedLabels(
                $modelClassName,
                $this->model->id
            )
            ->select('d3l_label.definition_id')
            ->column();

        foreach(D3lDefinitionDictionary::rowlList($modelClassName, $this->sysCompanyId) as $defRow){
            if(in_array($defRow['id'],$attachedDefIdList,true)){
                $this->attached[] = $defRow;
            }else{
                $this->noAttached[] = $defRow;
            }
        }

        if (!$this->title) {
            $this->title = Yii::t('d3labels', 'Labels');
        }

        if (!$this->_controllerRoute) {
            $this->_controllerRoute = Yii::$app->controller->id;
        }
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws Exception|Throwable
     */
    public function run(): string
    {
        /**
         * if module in config no defined, ignore widget
         */
        if(!$module =Yii::$app->getModule('d3labels')){
            return '';
        }

        return $this->renderFile(
            $module->basePath . '/views/label/_attached-list.php',
            [
                'title' => $this->createTitle(),
                'collapsedHtml' => '',
                'table' => $this->createTable(),
                'tableOptions' => $this->tableOptions,
                'collapseIcon' => $this->collapsed ? 'fa-angle-down' : 'fa-angle-up',
            ]
        );
    }

    /**
     * Get the Header content for Labels table
     * @return string
     * @throws Exception|Throwable
     */
    public function createTitle(): string
    {
        if (!$this->title) {
            return '';
        }

        $titleHtmlOptions = $this->titleHtmlOptions;
        Html::addCssClass($titleHtmlOptions, 'panel-title');
        if ($this->readOnly || !$this->noAttached) {
            return Html::tag('div', $this->title, $titleHtmlOptions);
        }

        $items = LabelLogic::getBadgeItems($this->noAttached, 'd3labelsattach', $this->model->id);
        $dropdownItems = [];

        foreach ($items as $item) {
            $url = $item['url'];
            unset($item['url']);
            $dropdownItems[] = [
                'label' => ThBadge::widget($item),
                'url' => $url,
            ];
        }

        return Html::tag(
            'div',
            ThButtonDropDown::widget([
                'icon' => ThButton::ICON_PLUS,
                'type' => ThButton::TYPE_SUCCESS,
                'items' => $dropdownItems,
                'size' => ThButtonDropDown::SIZE_XSMALL,
                'options' => ['id' => 'd3label-attach-dropdown'],
                'htmlOptions' => [
                    'title' => Yii::t('d3labels', 'Attach Label')
                ]
            ]) . $this->title,
            $titleHtmlOptions
        );
    }

    /**
     * Get the Labels table content
     * @return string
     * @throws Exception|Throwable
     */
    public function createTable(): string
    {
        $html = '
        <thead>
            <tr><th data-col-seq="0">' . Yii::t('d3labels', 'Attached Labels') . '</th></tr>    
        </thead>
        <tbody>
        ';

        foreach ($this->attached as $defRow) {

            $label = ThBadge::widget(
                [
                    'type' => $defRow['collor'],
                    'text' => $defRow['label'],
                    'afterText' => ' <i class="fa fa-times"></i>',
                    'title' => Yii::t('d3labels', 'Remove'),
                    'faIcon' => $defRow['icon'],
                    'showText' => $this->gridIconsWithText,
                    'url' => !$this->readOnly ? Url::to([
                        'd3labelsremove',
                        'labelId' => $defRow['id'],
                        'modelId' => $this->model->id,
                    ]) : null,
                ]
            );
            $html .= '
                <tr>
                    <td>' . $label . '</td>
                </tr>';
        }
        return $html . '</tbody>';
    }
}
