<?php

use Phinx\Migration\AbstractMigration;

class ItemsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $items = $this->table('items');
        $items->addColumn('name', 'string')
             ->addColumn('description', 'string', ['null' => true])
            //  ->addColumn('image', 'string', ['null' => true])
             ->addColumn('group_id', 'integer')
             ->addColumn('user_id', 'integer', ['null' => true])
             ->addColumn('creator', 'integer')
             ->addColumn('start_date', 'date')
             ->addColumn('end_date', 'date', ['null' => true])
             ->addColumn('recurrent', 'string', ['null' => true])
             ->addColumn('status', 'integer', ['default' => '0'])
             ->addColumn('privacy', 'integer', ['default' => '0'])
             ->addColumn('reported_at', 'timestamp', ['null' => true])
             ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
             ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP','update' => 'CURRENT_TIMESTAMP'])
             ->addColumn('deleted', 'integer', ['default' => '0'])
             ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
             ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
             ->addForeignKey('creator', 'users', 'id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
             ->create();
    }
}
