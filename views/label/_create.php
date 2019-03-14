<?php

use eaBlankonThema\widget\ThButton;

?>
<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">
                <?= Yii::t('d3lietvediba', 'Create Label') ?>
            </h3>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body no-padding">
        <div class="row">
            <div class="col-md-8">
                <?php $form = \kartik\widgets\ActiveForm::begin(['action' => ['d3labelscreate']]); ?>
                <?= $form->field($model, 'modelClass')->textInput() ?>
                <?php
                foreach ($model->labels as $labelDefinitionModel): ?>
                    <div class="row">
                        <?= $form->field($model, 'controllerModelId')->hiddenInput()->label(false) ?>
                        <?= $form->field($labelDefinitionModel, '[0]collor')->textInput() ?>
                        <?= $form->field($labelDefinitionModel, '[0]label')->textInput() ?>
                        <?= $form->field($labelDefinitionModel, '[0]icon')->textInput() ?>
                        <?php if ($returnURLToken): ?>
                            <?= \yii\helpers\Html::hiddenInput('ru', $returnURLToken) ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="row">
                    <?= ThButton::widget([
                        'label' => Yii::t('email-api-client', 'Create'),
                        'icon' => ThButton::ICON_CHECK,
                        'type' => ThButton::TYPE_SUCCESS,
                        'submit' => true,
                        'htmlOptions' => [
                            'name' => 'action',
                            'value' => 'save',
                        ],
                    ]);
                    ?>
                </div>
                <?php $form::end(); ?>
            </div>
        </div>
    </div>
</div>

