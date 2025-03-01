<?php

use cornernote\returnurl\ReturnUrl;
use d3system\yii2\web\D3SystemView;
use d3yii2\d3labels\models\D3Note;
use eaArgonTheme\widget\ThButton;
use eaArgonTheme\widget\ThReturnButton;
use eaArgonTheme\widget\ActiveForm;

/**
 * @var $model D3Note
 * @var D3SystemView $this
 */

$this->title = Yii::t('d3labels', 'Add a note');
Yii::$app->view->addPageButtons(ThReturnButton::widget(['backUrl' => ReturnUrl::getUrl()]));

?>
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}"
                ],
            ]); ?>
                <div class="card-body">
                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                </div>
                <div class="card-footer">
                    <?= ThButton::widget([
                        'label' => Yii::t('d3labels', 'Add'),
                        'icon' => ThButton::ICON_CHECK,
                        'type' => ThButton::TYPE_PRIMARY,
                        'submit' => true,
                        'htmlOptions' => [
                            'name' => 'action',
                            'value' => 'save',
                        ],
                    ])
                    ?>
                </div>
            <?php $form::end(); ?>
        </div>
    </div>
</div>

