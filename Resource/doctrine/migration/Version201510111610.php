<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201510111610 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createPlgHoliday($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_holiday');
    }

    protected function createPlgHoliday(Schema $schema)
    {
        $table = $schema->createTable("plg_holiday");
        $table->addColumn('holiday_id', 'integer', array(
            'autoincrement' => true,
        ));

        $table->addColumn('title', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('month', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('day', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('rank', 'integer', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('creator_id', 'integer', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('holiday_id'));

        $targetTable = $schema->getTable('dtb_member');
        $table->addForeignKeyConstraint(
            $targetTable,
            array('creator_id'),
            array('member_id')
        );

    }

}
