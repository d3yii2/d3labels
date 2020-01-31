<?php /** @noinspection PhpUndefinedClassInspection */

namespace d3yii2\d3labels\widgets;

use d3system\exceptions\D3ActiveRecordException;
use d3system\models\D3ActiveRecord;
use d3system\widgets\D3Widget;
use d3system\widgets\ThBadge;
use d3yii2\d3labels\logic\D3LabelList as LabelLogic;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThButtonDropDown;
use Exception;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
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
    public $model;
    public $title;
    public $titleDescription;
    public $titleHtmlOptions = ['style' => 'padding-bottom:0'];
    public $collapsed = false;
    public $tableOptions = [
        'class' => 'table table-striped table-success table-bordered'
    ];
    public $gridIconsWithText = false;
    public $returnURLToken;
    public $returnURL;

    public $readOnly = false;

    private $_d3LabelList;
    private $_controllerRoute;

    /**
     * @return bool|void
     * @throws D3ActiveRecordException
     */
    public function init()
    {
        parent::init();

        $this->_d3LabelList = new LabelLogic($this->model);

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
     * @throws Exception
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        return $this->renderFile(
            $modulePath . '/views/label/_attached-list.php',
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
     * @throws Exception
     */
    public function createTitle(): string
    {
        if (!$this->title) {
            return '';
        }

        $titleHtmlOptions = $this->titleHtmlOptions;
        Html::addCssClass($titleHtmlOptions, 'panel-title');

        $nonAttachedLabels = $this->_d3LabelList->getNonAttached();

        $dropdownItems = [];

        if ($nonAttachedLabels) {
            $items = LabelLogic::getBadgeItems($nonAttachedLabels, 'd3labelsattach', $this->model->id);

            $dropdownItems = [];

            foreach ($items as $item) {
                $url = $item['url'];
                unset($item['url']);
                $dropdownItems[] = [
                    'label' => ThBadge::widget($item),
                    'url' => $url,
                ];
            }
        }

        if ($this->readOnly) {
            return Html::tag('div', $this->title, $titleHtmlOptions);
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
     * @throws Exception
     */
    public function createTable(): string
    {
        $html = '
        <thead>
            <tr><th data-col-seq="0">' . Yii::t('d3labels', 'Attached Labels') . '</th></tr>    
        </thead>
        <tbody>
        ';

        $available = $this->_d3LabelList->getAvailable();
        $attached = $this->_d3LabelList->getAttached();

        foreach ($attached as $definitionId => $row) {
            if (!isset($available[$definitionId])) {
                continue;
            }

            $label = $available[$definitionId];

            /** @var D3ActiveRecord $model */
            $model = $this->_d3LabelList->model;

            if (!is_object($model)) {
                throw new \yii\base\Exception('Label Model not exists');
            }

            $label = ThBadge::widget(
                [
                    'type' => $label->collor,
                    'text' => $label->label,
                    'afterText' => ' <i class="fa fa-times"></i>',
                    'title' => Yii::t('d3labels', 'Remove'),
                    'faIcon' => $label->icon,
                    'showText' => $this->gridIconsWithText,
                    'url' => !$this->readOnly ? Url::to([
                        'd3labelsremove',
                        'labelId' => $row->id,
                        'modelId' => $model->id,
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
