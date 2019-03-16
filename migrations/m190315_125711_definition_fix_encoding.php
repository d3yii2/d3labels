<?php

use yii\db\Migration;

/**
* Class m190315_165711_definition_fix_encoding*/
class m190315_125711_definition_fix_encoding extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE `d3l_definition`   
              CHANGE `label` `label` VARCHAR(20) CHARSET utf8 NULL  COMMENT \'Label\';

        ');
    }

    public function safeDown()
    {
        echo "m190315_165711_definition_fix_encoding cannot be reverted.\n";
        return false;
    }

}