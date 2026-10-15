<?php

use d3yii2\d3labels\dictionaries\D3lIconDictionary;
use d3yii2\d3labels\logic\D3Definition;
use ea\themeBs5\widget\Icon;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var d3yii2\d3labels\models\D3lDefinition $model */
$icons = [];
foreach (array_keys(D3lIconDictionary::getIcons()) as $code) {
    $icons[$code] = ucwords(str_replace('-', ' ', Icon::name($code)));
}
$editing = !$model->isNewRecord;
$form = ActiveForm::begin([
    'action' => [$editing ? 'd3labelsdefinitionedit' : 'd3labelscreate'],
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
]);
?>
<?= $form->errorSummary($model) ?>
<?php if ($editing): ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'model_id')->hiddenInput()->label(false) ?>
<?php endif; ?>
<?= $form->field($model, 'collor')->dropDownList(D3Definition::getColors()) ?>
<?= $form->field($model, 'label')->textInput() ?>
<?php if ($showCode): ?>
    <?= $form->field($model, 'code')->textInput() ?>
<?php endif; ?>
<?= $form->field($model, 'icon')->dropDownList($icons, ['prompt' => '-']) ?>
<div class="d-flex justify-content-end gap-2 mt-3">
    <?= Html::button(Yii::t('d3labels', 'Cancel and close'), $editing
        ? ['id' => 'close-edit', 'class' => 'btn btn-outline-secondary']
        : ['class' => 'btn btn-outline-secondary', 'data-bs-toggle' => 'collapse', 'data-bs-target' => '#collapse']) ?>
    <?= Html::submitButton(Icon::tag('check', ['class' => 'me-1'])
        . Yii::t('d3labels', $editing ? 'Save' : 'Create Label'),
        ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'save']) ?>
</div>
<?php ActiveForm::end(); ?>
