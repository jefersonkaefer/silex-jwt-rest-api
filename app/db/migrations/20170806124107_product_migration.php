<?php

use Phinx\Migration\AbstractMigration;

class ProductMigration extends AbstractMigration
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
        $this
            ->table('products', [
                'signed' => false
            ])
            ->addColumn('category_id', 'integer', [
                'null'      => true,
                'signed'    => false
            ])
            ->addColumn('user_id', 'integer', [
                'signed' => false
            ])
            ->addColumn('name', 'string', [
                'limit' => 128,
                'null'  => false,
            ])
            ->addColumn('description', 'text')
            ->addColumn('price', 'decimal', [
                'precision' => 8,
                'scale'     => 2
            ])
            ->addColumn('created_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP'
            ])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->save()
        ;
    }
}
