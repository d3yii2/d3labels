<?php

use yii\db\Migration;

/**
* Class m190225_123403_define_sys_model_rk_invoice*/
class m190225_123403_define_sys_model_rk_invoice extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute("INSERT IGNORE INTO sys_models SET table_name='rk_invoice', class_name='d3modules\\\lietvediba\\\models\\\RkInvoice'");
    }

    public function safeDown()
    {
        return true;
    }

}