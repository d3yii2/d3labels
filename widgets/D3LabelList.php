<?php

namespace d3yii2\d3labels\widgets;

use d3system\widgets\ThBadge;
use d3yii2\d3labels\logic\D3LabelList as LabelLogic;
use eaBlankonThema\widget\ThButton;use eaBlankonThema\widget\ThButtonDropDown;
use Yii;
use yii\helpers\Html;

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
 * @property \d3yii2\d3labels\logic\D3LabelList $d3LabelList
 * @property string $controllerRoute $controllerRoute
 */
class D3LabelList extends \yii\base\Widget
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

    private $d3LabelList;
    private $controllerRoute;

    /**
     * @return bool|void
     */
    public function init()
    {
        parent::init();

        $this->d3LabelList = new LabelLogic($this->model, $this->returnURLToken);

        if (!$this->title) {
            $this->title = Yii::t('d3labels', 'Labels');
        }

        if (!$this->controllerRoute) {
            $this->controllerRoute = Yii::$app->controller->id;
        }
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws \Exception
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
            ]
        );
    }

    /**
     * Get the Header content for Labels table
     * @return string
     * @throws \Exception
     */
    public function createTitle(): string
    {
        if (!$this->title) {
            return '';
        }

        $description = '';
        if ($this->titleDescription) {
            $description = '<p>' . $this->titleDescription . '</p>';
        }
        $titleHtmlOptions = $this->titleHtmlOptions;
        Html::addCssClass($titleHtmlOptions, 'panel-title');

        $collapseIcon = 'fa-angle-up';
        if ($this->collapsed) {
            $collapseIcon = 'fa-angle-down';
        }

        $nonAttachedLabels = $this->d3LabelList->getNonAttached();

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

        $content = '
        <div class="panel-heading panel-heading-table-simple no-padding">
            <div class="pull-left">' .
                Html::tag(
                    'h3',
                    ThButtonDropDown::widget([
                        'icon' => ThButton::ICON_PLUS,
                        'type' => ThButton::TYPE_SUCCESS,
                        'items' => $dropdownItems,
                        'size' => ThButtonDropDown::SIZE_XSMALL,
                        'options' => ['id' => 'd3label-attach-dropdown']
                    ]) . $this->title
                    , $titleHtmlOptions
                ) . '
            </div>
            <div class="pull-right" style="display: inline-block">
                <button class="btn btn-sm" data-action="collapse" data-toggle="tooltip" data-placement="top" data-title="Collapse" data-original-title="" title="">
                    <i class="fa ' . $collapseIcon . '"></i>
                </button>
            </div>
            <div class="clearfix"></div>
        </div>';

        return $content;
    }

    /**
     * Get the Labels table content
     * @return string
     * @throws \Exception
     */
    public function createTable(): string
    {
        $html = '
        <tbody>
        ';

        $available = $this->d3LabelList->getAvailable();
        $attached = $this->d3LabelList->getAttached();

        foreach ($attached as $definitionId => $row) {

            if (!isset($available[$definitionId])) {
                continue;
            }

            $label = $available[$definitionId];

            $label = ThBadge::widget(
                [
                    'type' => $label->collor,
                    'text' => $label->label,
                    'afterText' => ' <i class="fa fa-times"></i>',
                    'title' => Yii::t('d3labels', 'Remove'),
                    'faIcon' => $label->icon,
                    'showText' => $this->gridIconsWithText,
                    'url' => \yii\helpers\Url::to([
                        'd3labelsremove',
                        'labelId' => $row->id,
                        'modelId' => $this->d3LabelList->model->id,
                    ]),
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