<?php

use cornernote\returnurl\ReturnUrl;
use d3system\yii2\web\D3SystemView;
use d3yii2\d3labels\models\D3Note;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThReturnButton;
use yii\bootstrap\ActiveForm;

/**
 * @var $model D3Note
 * @var D3SystemView $this
 */
LayoutAsset::register($this);
$this->title = Yii::t('d3labels', 'Add a note');
$this->setPageHeader($this->title);
Yii::$app->view->addPageButtons(ThReturnButton::widget(['backUrl' => ReturnUrl::getUrl()]));

?>
<div class="row">
    <div class="col-md-9">
        <div class="panel  rounded shadow">
            <div class="panel-body rounded-bottom">

                <div class="form-body">
                    <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}"
                        ],
                    ]); ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                    <?= ThButton::widget([
                        'label' => Yii::t('d3labels', 'Add'),
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

