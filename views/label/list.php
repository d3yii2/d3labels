<?php
use yii\helpers\Html;
?>

<div class="panel">
    <?= $title ?>
    <div class="panel-body"<?= $collapsedHtml ?>>
        <div class="table-responsive">
            <?=  Html::tag('table', $table, $tableOptions) ?>
        </div>
    </div>
</div>