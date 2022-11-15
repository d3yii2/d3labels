<?php

use yii\db\Migration;

class m221115_123345_d3yii2_d3labels_label_optimise_index  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3l_label`
              DROP INDEX `definition_id`,
              ADD KEY `definition_id` (
                `definition_id`,
                `model_record_id`
              );
            
                    
        ');
    }

    public function safeDown() {
        echo "m221115_123345_d3yii2_d3labels_label_optimise_index cannot be reverted.\n";
        return false;
    }
}
