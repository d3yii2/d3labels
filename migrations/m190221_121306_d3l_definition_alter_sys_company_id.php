<?php

use yii\db\Migration;

/**
* Class m190221_121306_d3l_definition_alter_sys_company_id*/
class m190221_121306_d3l_definition_alter_sys_company_id extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `d3l_definition` CHANGE `sys_company_id` `sys_company_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL;');
    }

    public function safeDown()
    {
        echo "m190221_121306_d3l_definition_alter_sys_company_id cannot be reverted.\n";
        return false;
    }

}