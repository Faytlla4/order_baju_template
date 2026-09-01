<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_master_jenis_baju extends Migration
{
	private $table_name = 'master_jenis_baju';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'nama_jenis' => array(
			'type'       => 'VARCHAR',
			'constraint' => 50,
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
			array('nama_jenis' => 'Polo',  'urutan' => 1, 'keterangan' => 'Kaos berkerah',  'status' => 1, 'created_by' => 0),
			array('nama_jenis' => 'Kaos',  'urutan' => 2, 'keterangan' => 'Kaos polos',     'status' => 1, 'created_by' => 0),
			array('nama_jenis' => 'Kemeja','urutan' => 3, 'keterangan' => 'Baju kerja resmi','status' => 1, 'created_by' => 0),
			array('nama_jenis' => 'Hoodie','urutan' => 4, 'keterangan' => 'Jaket bertudung','status' => 1, 'created_by' => 0),
			array('nama_jenis' => 'Jaket', 'urutan' => 5, 'keterangan' => 'Jaket luar',     'status' => 1, 'created_by' => 0),
		);
		$this->db->insert_batch($this->table_name, $seed);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}