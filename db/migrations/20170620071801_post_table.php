<?php

use Phinx\Migration\AbstractMigration;

class PostTable extends AbstractMigration
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
        // $post = $this->table('posts');
        // $post->addColumn('content', 'string')
        //      ->addColumn('image', 'string', ['null' => true])
        //      ->addColumn('group_id', 'integer')
        //      ->addColumn('creator', 'integer')
        //      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        //      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP','update' => 'CURRENT_TIMESTAMP'])
        //      ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
        //      ->addForeignKey('creator', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
        //      ->create();
         }
}
