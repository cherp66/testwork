<?php
namespace console\migrations;

class m221015_122713_tree
{

    public function up($db)
    {
        $db->query("CREATE TABLE  IF NOT EXISTS `tree` (
            `id` INT(10) NOT NULL AUTO_INCREMENT,
            `parent_id` INT(10),
            `name` varchar(255) NOT NULL,
            `description` text NOT NULL,           
            PRIMARY KEY (`id`),
            FOREIGN KEY (`parent_id`) REFERENCES `tree` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE,
            CHECK (`left` <= `right`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down($db)
    {
        $db->query("DROP TABLE tree");
    }

}

