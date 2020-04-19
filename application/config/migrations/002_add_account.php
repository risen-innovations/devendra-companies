<?php


class Migration_Add_account extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field(
			array(
				'id' => array(
					'type' => 'INT',
					'constraint' => 10,
					'unsigned' => true,
					'auto_increment' => true
				),
				'name' => array(
					'type' => 'VARCHAR',
					'constraint' => '64',
					'null' => true
				),
				'email' => array(
					'type' => 'VARCHAR',
					'constraint' => '64',
					'null' => true,
				),
				'mobile' => array(
					'type' => 'VARCHAR',
					'constraint' => '12',
					'null' => true,
				),
				'role' => array(
					'type' => 'TINYINT',
					'constraint' => '3',
					'null' => true,
				),
				'company' => array(
					'type' => 'SMALLINT',
					'constraint' => '3',
					'null' => true,
				),
				'avatar_path' => array(
					'type' => 'VARCHAR',
					'constraint' => '32',
					'null' => true,
				),
				'password_hash' => array(
					'type' => 'TEXT',
					'null' => true,
				),
				'account_status' => array(
					'type' => 'TINYINT',
					'constraint' => '3',
					'null' => true,
				),
				'user_id' => array(
					'type' => 'VARCHAR',
					'constraint' => '36',
					'null' => true,
				),
				'datetime_created' => array(
					'type' => 'TIMESTAMP',
					'null' => true,
				),
				'datetime_updated' => array(
					'type' => 'TIMESTAMP',
					'null' => true,
				),

			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('accounts');
	}

	public function down()
	{
		$this->dbforge->drop_table('accounts');
	}
