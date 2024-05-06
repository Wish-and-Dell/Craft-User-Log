<?php
namespace wishanddell\userlog\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%user_log}}')) {
            
            $this->createTable('{{%user_log}}', [
                'id' => $this->integer()->notNull()->defaultValue('0'),
                'userId' => $this->integer()->notNull(),
                'ipAddress' => $this->char(255)->null(),
                'location' => $this->char(255)->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'PRIMARY KEY(id)',
            ]);
            
            $this->alterColumn('{{%user_log}}', 'id', $this->integer().' NOT NULL AUTO_INCREMENT');
        }
    }

    public function safeDown()
    {
        // ...
    }
}