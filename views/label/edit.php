<?php

use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
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
 * @var string $editAction
 */
$modulePath = Yii::$app->getModule('d3labels')->basePath;
$icons = D3lIconDictionary::getIcons();
?>
            <div class="row panel-body">
                <div class="col-md-6">
                    <div class="row">
                        <?= ThAlertList::widget() ?>
                    </div>
                    <div class="row">
                        <div class="card card-body">
                            <?php $form = ActiveForm::begin([
                                'action' => Url::toRoute([$editAction]),
                                'fieldConfig' => [
                                    'template' => "{label}\n{input}\n{error}"
                                ],
                                'enableClientValidation' => true,
                                'options' => [
                                    'validateOnSubmit' => true,
                                ],
                            ]); ?>

                            <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                            <?= $form->field($model, 'model_id')->hiddenInput()->label(false); ?>
                            <?= $form->field($model, 'collor')->dropDownList(D3Definition::getColors()) ?>
                            <?= $form->field($model, 'label')->textInput() ?>

                            <div class="form-group field-d3ldefinition-icon has-success">
                                <label class="control-label" for="d3ldefinition-icon"><?=Yii::t('d3labels', 'Icon')?></label>
                                <select id="d3ldefinition-icon" class="form-control fa" style="font-size: 25px !important; width:75px;" name="D3lDefinition[icon]" aria-invalid="false">
                                    <option value="">-</option>
                                    <?php foreach ($icons as $code => $unicode) { ?>
                                        <option <?= $code == $model->icon ? 'selected' : '' ?> value="<?=$code?>">&#x<?=$unicode?>;</option>
                                    <?php } ?>
                                </select>
                                <p class="help-block help-block-error"></p>
                            </div>

                            <?= ThButton::widget([
                                'label' => Yii::t('d3labels', 'Cancel and close'),
                                'icon' => ThButton::ICON_CHECK,
                                'type' => ThButton::TYPE_DANGER,
                                'id' => 'close-edit'
                            ])
                            ?>

                            <?= ThButton::widget([
                                'label' => Yii::t('d3labels', 'Save'),
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
                </div>
            </div>

