<?php

use eaBlankonThema\widget\ThButton;

$modulePath = Yii::$app->getModule('d3labels')->basePath;
?>
<div class="row rounded shadow">
    <div class="col-md-8">
        <div class="row panel-heading">
            <div class="pull-left">
                <h3 class="panel-title">
                    <?= Yii::t('d3lietvediba', 'Create Label') ?>
                </h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row panel-body no-padding">
            <div class="col-md-6">
                <div class="row">
                    <?= \eaBlankonThema\widget\ThAlertList::widget() ?>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <?php $form = \kartik\widgets\ActiveForm::begin(['action' => ['d3labelscreate']]); ?>
                        <?= $form->field($model, 'modelClass')->textInput() ?>
                        <?php
                        foreach ($model->labels as $labelDefinitionModel): ?>
                            <?= $form->field($model, 'controllerModelId')->hiddenInput()->label(false) ?>
                            <?= $form->field($labelDefinitionModel, '[0]collor')->dropDownList(\d3yii2\d3labels\logic\D3Definition::getColors()) ?>
                            <?= $form->field($labelDefinitionModel, '[0]label')->textInput() ?>
                            <?= $form->field($labelDefinitionModel, '[0]icon')->textInput() ?>
                            <?php if ($returnURLToken): ?>
                                <?= \yii\helpers\Html::hiddenInput('ru', $returnURLToken) ?>
                            <?php endif; ?>
                        <?php
                        endforeach; ?>

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
                        <?php $form::end(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?= $labelsList ?>
            </div>
        </div>
    </div>
</div>

