<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_order_baju extends Migration
{
	private $table_name = 'order_baju';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'kode_order' => array(
			'type'       => 'VARCHAR',
			'constraint' => 50,
			'null'       => false,
		),
		'nama_customer' => array(
			'type'       => 'VARCHAR',
			'constraint' => 100,
			'null'       => false,
		),
		'produk' => array(
			'type'       => 'VARCHAR',
			'constraint' => 100,
			'null'       => false,
		),
		'ukuran' => array(
			'type'       => 'VARCHAR',
			'constraint' => 20,
			'null'       => false,
		),
		'warna' => array(
			'type'       => 'VARCHAR',
			'constraint' => 30,
			'null'       => false,
		),
		'jumlah' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'null'       => false,
		),
		'harga' => array(
			'type'       => 'DECIMAL',
			'constraint' => '12,2',
			'null'       => false,
		),
		'total_harga' => array(
			'type'       => 'DECIMAL',
			'constraint' => '12,2',
			'null'       => false,
		),
		'status_order' => array(
			'type'       => 'VARCHAR',
			'constraint' => 30,
			'null'       => false,
		),
		'tanggal_order' => array(
			'type'       => 'DATE',
			'null'       => false,
		),
		'deleted' => array(
			'type'       => 'TINYINT',
			'constraint' => 1,
			'default'    => '0',
		),
		'deleted_on' => array(
			'type'       => 'TIMESTAMP',
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
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}
