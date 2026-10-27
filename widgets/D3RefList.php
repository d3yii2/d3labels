<?php

namespace d3yii2\d3labels\widgets;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\widgets\D3Widget;
use d3yii2\d3labels\logic\D3LabelList as LabelLogic;
use d3yii2\d3labels\models\D3lRefRecords;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveRecord;

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
class D3RefList extends D3Widget
{

    public ?string $title = null;
    public ?array $codes = null;
    public ?ActiveRecord $model = null;

    /**
     * @return void
     * @throws D3ActiveRecordException
     */
    public function init(): void
    {
        parent::init();
        if (!$this->title) {
            $this->title = Yii::t('d3labels', 'Refernces');
        }
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws Exception|Throwable
     */
    public function run(): string
    {
        if (!$this->model) {
            throw new \yii\base\Exception('Model is not set');
        }
        $query = D3lRefRecords::find()
            ->select([
                'd3l_ref_records.ref_record_id',
                'ref_class_sys_model_id' => 'd3l_ref_def.class_sys_model_id',
            ])
            ->innerJoin(
                'd3l_ref_def',
                'd3l_ref_def.id = d3l_ref_records.def_id'
            )
            ->where([
                'd3l_ref_records.sys_model_id' => SysModelsDictionary::getIdByClassName(get_class($this->model)),
                'd3l_ref_records.record_id' => $this->model->primaryKey,
            ]);
        if ($this->codes) {
            $query->andWhere(['d3l_ref_def.code' => $this->codes]);
        }
        $sysClassList = SysModelsDictionary::getClassList();
        $modelRefs = [];
        foreach ($query->asArray()->all() as $record) {
            $refModelClass = $sysClassList[$record['ref_class_sys_model_id']];
            $modelRefClass = new $refModelClass($record['ref_record_id']);
            if ($modelRefClass->isRefRecord()) {
                $modelRefs[] = $modelRefClass;
            }
        }
        return $this->render(
            'ref-list',
            [
                'title' => $this->title,
                'modelRefs' => $modelRefs,
            ]
        );
    }

}
