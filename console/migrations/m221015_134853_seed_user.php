<?php
namespace console\migrations;

class m221015_134853_seed_user
{

    public function up($db)
    {
        $password = password_hash('12345', PASSWORD_DEFAULT);
        $db->query("INSERT INTO `user` SET 
             `login` = 'admin', 
             `password` = '". $password. "'"
        );
    }

    public function down($db)
    {
        $db->query("TRUNCATE `user`");
    }

}