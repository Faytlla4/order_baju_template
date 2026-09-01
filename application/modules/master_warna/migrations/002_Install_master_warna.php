<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_master_warna extends Migration
{
	private $table_name = 'master_warna';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'nama_warna' => array(
			'type'       => 'VARCHAR',
			'constraint' => 30,
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
			array('nama_warna' => 'Hitam', 'urutan' => 1, 'keterangan' => 'Warna dasar',  'status' => 1, 'created_by' => 0),
			array('nama_warna' => 'Putih', 'urutan' => 2, 'keterangan' => 'Warna dasar',  'status' => 1, 'created_by' => 0),
			array('nama_warna' => 'Merah', 'urutan' => 3, 'keterangan' => 'Warna terang', 'status' => 1, 'created_by' => 0),
			array('nama_warna' => 'Biru',  'urutan' => 4, 'keterangan' => 'Warna terang', 'status' => 1, 'created_by' => 0),
			array('nama_warna' => 'Hijau', 'urutan' => 5, 'keterangan' => 'Warna terang', 'status' => 1, 'created_by' => 0),
			array('nama_warna' => 'Kuning','urutan' => 6, 'keterangan' => 'Warna terang', 'status' => 1, 'created_by' => 0),
		);
		$this->db->insert_batch($this->table_name, $seed);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}