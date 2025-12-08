<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var string $title
 * @var string $collapseIcon
 * @var string $collapsedHtml
 * @var string $table
 * @var array $tableOptions
 */

$css = <<<CSS
.panel .panel-heading>.pull-right {
    margin-top: 5px;
}
CSS;

$this->registerCss($css, ['position' => View::POS_HEAD]);

?>
<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <?= $title ?>
        </div>
        <div class="pull-right" style="display: inline-block">
            <button class="btn btn-sm" data-action="collapse" data-toggle="tooltip" data-placement="top"
                    data-title="Collapse" data-original-title="" title="">
                <i class="fa <?= $collapseIcon ?>"></i>
            </button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body"<?= $collapsedHtml ?>>
        <div class="table-responsive">
            <?= Html::tag('table', $table, $tableOptions) ?>
        </div>
    </div>
</div>
