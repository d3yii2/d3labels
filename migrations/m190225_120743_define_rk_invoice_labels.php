<?php

use yii\db\Migration;

/**
* Class m190225_120743_define_rk_invoice_labels*/
class m190225_120743_define_rk_invoice_labels extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $items = [
            [
                'modelClass' => 'd3modules\lietvediba\models\RkInvoice',
                'label' => ['title' => 'Test #1', 'icon' => 'test1'],
            ],
            [
                'modelClass' => 'd3modules\lietvediba\models\RkInvoice',
                'label' => ['title' => 'Test #2', 'icon' => 'test2'],
            ]
        ];

        foreach ($items as $label) {
            $def = new \d3yii2\d3labels\logic\D3Definition($label['modelClass'], $label['label']);
            $def->save();
        }

        return true;
    }

    public function safeDown()
    {
        return true;
    }

}