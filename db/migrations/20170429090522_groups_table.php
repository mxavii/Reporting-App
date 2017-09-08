<?php

use Phinx\Migration\AbstractMigration;

class GroupsTable extends AbstractMigration
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
        $groups = $this->table('groups');
        $groups->addColumn('name', 'string')
                ->addColumn('description', 'string')
                ->addColumn('image', 'string', ['null' => true])
                ->addColumn('creator', 'integer')
                ->addColumn('deleted', 'integer', ['default' => '0'])
                ->addForeignKey('creator', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])

                ->create();
    }
}
