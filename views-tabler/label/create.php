<?php

use d3yii2\d3labels\widgets\D3LabelCreate;
use ea\themeBs5\widget\ThAlertList;
use ea\themeBs5\widget\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$createButton = Html::button(Icon::tag('plus', ['class' => 'me-1']) . Yii::t('d3labels', 'Create Label'), [
    'class' => 'create-label-btn btn btn-success', 'data-bs-toggle' => 'collapse',
    'data-bs-target' => '#collapse', 'aria-expanded' => 'false', 'aria-controls' => 'collapse',
]);
if (D3LabelCreate::PLACEMENT_PAGE_BUTTONS === $createButtonPlacement) {
    Yii::$app->view->addPageButtons($createButton);
}
?>
<?= ThAlertList::widget() ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Yii::t('d3labels', 'Labels') ?></h3>
        <?php if (D3LabelCreate::PLACEMENT_TOP === $createButtonPlacement): ?>
            <div class="card-actions"><?= $createButton ?></div>
        <?php endif; ?>
    </div>
    <div id="labels-list" class="table-responsive"><?= $labelsList ?></div>
    <div class="collapse" id="collapse">
        <div class="card-body">
            <?= $this->render('_form', ['model' => $model, 'showCode' => $showCode]) ?>
        </div>
    </div>
    <div id="labels-edit" class="card-body" style="display: none;"></div>
</div>
<?php
    $js = "$('#collapse').on('hidden.bs.collapse', function () {
    $('#labels-list').show();
});
$('#collapse').on('shown.bs.collapse', function () {
    $('#labels-list').hide();
});
$('.edit-item').on('click', function () {
    $.ajax({
            url: '".Url::toRoute('d3labelsdefinitionedit')."',
            type: 'post',
            data: {definition: $(this).attr('data-link')},
            dataType: 'json',
            success: function(data) {
                if(data['content']) {
                    $('#labels-list').hide();
                    $('.create-label-btn').hide();
                    $('#labels-edit').html(data['content']).show();
                }
            },
            error: function(data) {
                $('#labels-list').show();
                $('.create-label-btn').show();
                console.log(data);
        }

    });
});

$(document).on('click', '#close-edit', function(){
    $('#labels-list').show();
    $('.create-label-btn').show();
    $('#labels-edit').html('');
});

";
    $this->registerJs($js);
    ?>
