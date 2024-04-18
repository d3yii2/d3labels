<?php

use yii\db\Migration;

/**
* Class m240417_221951_create_d3l_notes*/
class m240417_221951_create_d3l_notes extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->createTable(
            'd3l_notes',
            [
                'id' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'model_id' => 'TINYINT UNSIGNED NOT null',
                'model_record_id' => 'INT UNSIGNED NOT null',
                'notes' => 'VARCHAR(255) CHARSET utf8',
                'user_id' => 'INT UNSIGNED',
                'time' => 'TIMESTAMP',
            ]
        );
        $this->addForeignKey('fk_model_id', 'd3l_notes', 'model_id', 'sys_models', 'id');
    }

    public function safeDown()
    {
        echo "m240417_221951_create_d3l_notes cannot be reverted.\n";
        return false;
    }

}