<?php

use yii\db\Migration;

/**
* Class m190329_104417_definition_add_code*/
class m190329_084417_definition_add_code extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE `d3l_definition`   
              ADD COLUMN `code` CHAR(20) CHARSET latin1 NULL  COMMENT \'Code\' AFTER `sys_company_id`;

        ');
    }

    public function safeDown()
    {
        echo "m190329_104417_definition_add_code cannot be reverted.\n";
        return false;
    }

}