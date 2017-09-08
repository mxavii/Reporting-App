<?php

use Phinx\Migration\AbstractMigration;

class ArticlesTable extends AbstractMigration
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
    // public function change()
    // {
    //     $articles = $this->table('articles');
    //     $articles->addColumn('title', 'string')
    //          ->addColumn('content', 'text')
    //          ->addColumn('image', 'string', ['null' => true])
    //          ->addColumn('group_id', 'string', ['null' => true])
    //          ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP','update' => 'CURRENT_TIMESTAMP'])
    //          ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
    //          ->addColumn('deleted', 'integer', ['default' => '0'])
    //          ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
    //          ->create();
    // }
}
