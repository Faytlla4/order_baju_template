<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_master_ukuran extends Migration
{
	private $table_name = 'master_ukuran';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'nama_ukuran' => array(
			'type'       => 'VARCHAR',
			'constraint' => 20,
			'null'       => false,
		),
		'urutan' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'default'    => 0,
			'null'       => false,
		),
		'keterangan' => array(
			'type'       => 'VARCHAR',
			'constraint' => 255,
			'null'       => true,
		),
		'status' => array(
			'type'       => 'TINYINT',
			'constraint' => 1,
			'default'    => '1',
			'null'       => false,
		),
		'deleted' => array(
			'type'       => 'TINYINT',
			'constraint' => 1,
			'default'    => '0',
		),
		'deleted_on' => array(
			'type'       => 'TIMESTAMP',
			'null'       => true,
		),
		'deleted_by' => array(
			'type'       => 'BIGINT',
			'constraint' => 20,
			'null'       => true,
		),
		'created_on' => array(
			'type'       => 'TIMESTAMP',
			'default'    => 'NOW()',
		),
		'created_by' => array(
			'type'       => 'BIGINT',
			'constraint' => 20,
			'null'       => false,
		),
		'modified_on' => array(
			'type'       => 'TIMESTAMP',
			'null'       => true,
		),
		'modified_by' => array(
			'type'       => 'BIGINT',
			'constraint' => 20,
			'null'       => true,
		),
	);

	public function up()
	{
		$this->dbforge->add_field($this->fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table($this->table_name);

		// Seed data awal
		$seed = array(
			array('nama_ukuran' => 'S',   'urutan' => 1, 'keterangan' => 'Small',      'status' => 1, 'created_by' => 0),
			array('nama_ukuran' => 'M',   'urutan' => 2, 'keterangan' => 'Medium',     'status' => 1, 'created_by' => 0),
			array('nama_ukuran' => 'L',   'urutan' => 3, 'keterangan' => 'Large',      'status' => 1, 'created_by' => 0),
			array('nama_ukuran' => 'XL',  'urutan' => 4, 'keterangan' => 'Extra Large', 'status' => 1, 'created_by' => 0),
			array('nama_ukuran' => 'XXL', 'urutan' => 5, 'keterangan' => 'Double Extra Large', 'status' => 1, 'created_by' => 0),
			array('nama_ukuran' => 'XXXL','urutan' => 6, 'keterangan' => 'Triple Extra Large', 'status' => 1, 'created_by' => 0),
		);
		$this->db->insert_batch($this->table_name, $seed);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}