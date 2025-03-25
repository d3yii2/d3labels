<?php

use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\widgets\D3LabelCreate;
use eaBlankonThema\widget\ThAlertList;
use eaBlankonThema\widget\ThButton;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use d3yii2\d3labels\dictionaries\D3lIconDictionary;

/**
 * @var $model D3lDefinition
 * @var string $returnURLToken
 * @var string $labelsList
 * @var string $createButtonPlacement
 * @var bool $showCode
 */
$modulePath = Yii::$app->getModule('d3labels')->basePath;

$createButton = ThButton::widget([
    'label' => Yii::t('d3labels', 'Create Label'),
    'icon' => ThButton::ICON_PLUS,
    'type' => ThButton::TYPE_SUCCESS,
    'htmlOptions' => [
        'data-toggle' => 'collapse',
        'data-target' => '#collapse',
        'aria-expanded' => 'false',
        'aria-controls' => 'collapse',
        'class' => 'create-label-btn btn btn-success'
    ],
]);

if (D3LabelCreate::PLACEMENT_PAGE_BUTTONS === $createButtonPlacement) {
    Yii::$app->view->addPageButtons($createButton);
}
$icons = D3lIconDictionary::getIcons();

?>
<div class="row rounded shadow">
    <div class="col-md-8">
        <div class="row panel-heading">
            <div class="pull-left">
                <div class="pull-left">
                    <h3 class="panel-title">
                        <?= Yii::t('d3labels', 'Labels') ?>
                    </h3>
                </div>
            </div>
            <div class="row panel-body">
                <div class="col-md-6">
                    <div class="row">
                        <?= ThAlertList::widget() ?>
                    </div>

                    <?php
                    if (D3LabelCreate::PLACEMENT_TOP === $createButtonPlacement): ?>
                        <div class="row">
                            <?= $createButton ?>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div id="labels-list" class="row">
                        <?= $labelsList ?>
                    </div>

                    <div class="row collapse" id="collapse">
                        <div class="card card-body">
                            <?php $form = ActiveForm::begin([
                                'action' => Url::toRoute(['d3labelscreate']),
                                'fieldConfig' => [
                                    'template' => "{label}\n{input}\n{error}"
                                ],
                                'enableClientValidation' => true,
                                'options' => [
                                    'validateOnSubmit' => true,
                                ],
                            ]); ?>

                            <?= $form->field($model, 'collor')->dropDownList(D3Definition::getColors()) ?>
                            <?= $form->field($model, 'label')->textInput() ?>
                            <?php if($showCode): ?>
                                <?= $form->field($model, 'code')->textInput() ?>
                            <?php endif; ?>
                            <div class="form-group field-d3ldefinition-icon has-success">
                                <label class="control-label" for="d3ldefinition-icon"><?=Yii::t('d3labels', 'Icon')?></label>
                                <select id="d3ldefinition-icon" class="form-control fa" style="font-size: 25px !important; width:75px;" name="D3lDefinition[icon]" aria-invalid="false">
                                    <option value="">-</option>
                                    <?php foreach ($icons as $code => $unicode) { ?>
                                        <option value="<?=$code?>">&#x<?=$unicode?>;</option>
                                    <?php } ?>
                                </select>
                                <p class="help-block help-block-error"></p>
                            </div>

                            <?= ThButton::widget([
                                'label' => Yii::t('d3labels', 'Cancel and close'),
                                'icon' => ThButton::ICON_CHECK,
                                'type' => ThButton::TYPE_DANGER,
                                'htmlOptions' => [
                                    'data-toggle' => 'collapse',
                                    'data-target' => '#collapse',
                                    'aria-expanded' => 'false',
                                    'aria-controls' => 'collapse'
                                ],
                            ])
                            ?>

                            <?= ThButton::widget([
                                'label' => Yii::t('d3labels', 'Create Label'),
                                'icon' => ThButton::ICON_CHECK,
                                'type' => ThButton::TYPE_SUCCESS,
                                'submit' => true,
                                'htmlOptions' => [
                                    'name' => 'action',
                                    'value' => 'save',
                                ],
                            ])
                            ?>
                            <?php $form::end(); ?>
                        </div>
                    </div>
                    <div id="labels-edit" class="row" style="display: none;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $js = "$('#collapse').on('hidden.bs.collapse', function () {
    $('#labels-list').show();
});
$('#collapse').on('shown.bs.collapse', function () {
    $('#labels-list').hide();
});
$('.delete-item').on('click', function() {
    confirm('" . Yii::t('d3labels', 'Label will be deleted') . "');
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

