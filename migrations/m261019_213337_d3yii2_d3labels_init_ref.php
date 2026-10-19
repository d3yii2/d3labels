<?php

use yii\db\Migration;

class m261019_213337_d3yii2_d3labels_init_ref  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d3l_ref_def` (
              `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `code` char(15) DEFAULT NULL,
              `class_sys_model_id` tinyint(3) unsigned DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `d3l_ref_def_ibfk_sys_model` (`sys_model_id`),
              KEY `d3l_ref_def_ibfk_class_sys_model` (`class_sys_model_id`),
              CONSTRAINT `d3l_ref_def_ibfk_class_sys_model` FOREIGN KEY (`class_sys_model_id`) REFERENCES `sys_models` (`id`),
              CONSTRAINT `d3l_ref_def_ibfk_sys_model` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            CREATE TABLE `d3l_ref_records` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `record_id` int(10) unsigned NOT NULL,
              `def_id` tinyint(3) unsigned NOT NULL,
              `ref_record_id` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `d3l_ref_records_ibfk_sys_model` (`sys_model_id`),
              KEY `d3l_ref_records_ibfk_def` (`def_id`),
              CONSTRAINT `d3l_ref_records_ibfk_def` FOREIGN KEY (`def_id`) REFERENCES `d3l_ref_def` (`id`),
              CONSTRAINT `d3l_ref_records_ibfk_sys_model` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            
                    
        ');
    }

    public function safeDown() {
        echo "m261019_213337_d3yii2_d3labels_init_ref cannot be reverted.\n";
        return false;
    }
}
