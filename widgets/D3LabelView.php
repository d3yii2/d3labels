<?php

namespace d3yii2\d3labels\widgets;

use d3system\dictionaries\SysModelsDictionary;
use d3system\widgets\ThBadge;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\models\D3lLabel;
use Exception;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;

/**
 * Class D3LabelCreate
 * Use for displaying simple labels without adding, removing. Can use in DetailViev
 * @package d3yii2\d3labels\widgets
 * @property string $modelClass
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3LabelView extends Widget
{
    /** @var ActiveRecord */
    public $model;
    /**
     * @var string
     */
    public $modelClass;

    /** @var int */
    public $sysCompanyId;

    /**
     * @var array
     */
    private $attached = [];


    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $attachedDefIdList = D3lLabel::find()
            ->select('d3l_label.definition_id')
            ->innerJoin('d3l_definition', 'd3l_definition.id = d3l_label.definition_id')
            ->where([
                'model_record_id' => $this->model->id,
                'd3l_definition.model_id' => SysModelsDictionary::getIdByClassName(get_class($this->model))
            ])
            ->column();

        foreach (D3lDefinitionDictionary::rowlList(get_class($this->model), $this->sysCompanyId) as $defRow) {
            if (in_array($defRow['id'], $attachedDefIdList, true)) {
                $this->attached[] = $defRow;
            }
        }

    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $labels = [];
        foreach ($this->attached as $defRow) {

            $labels[] = ThBadge::widget(
                [
                    'type' => $defRow['collor'],
                    'text' => $defRow['label'],
                    //'afterText' => ' <i class="fa fa-times"></i>',
                    //'title' => Yii::t('d3labels', 'Remove'),
                    'faIcon' => $defRow['icon'],
                    'showText' => false,
                ]
            );
        }

        return implode(' ', $labels);
    }
}