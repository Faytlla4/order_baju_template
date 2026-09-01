<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Instalasi tabel transaksi.
 *
 * Tabel transaksi menyimpan data transaksi yang dibuat dari order Content
 * (order_baju). Satu order hanya boleh memiliki satu transaksi (unique index
 * pada order_baju_id) sehingga tidak ada transaksi ganda.
 *
 * Hanya membuat tabel baru; tidak mengubah tabel lain.
 */
class Migration_Install_transaksi extends Migration
{
	private $table_name = 'transaksi';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'order_baju_id' => array(
			'type'       => 'INT',
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
		'status_transaksi' => array(
			'type'       => 'VARCHAR',
			'constraint' => 30,
			'null'       => false,
			'default'    => 'Draft',
		),
		'tanggal_transaksi' => array(
			'type'       => 'DATE',
			'null'       => false,
		),
		'deleted' => array(
			'type'       => 'SMALLINT',
			'constraint' => 2,
			'default'    => '0',
		),
		'deleted_by' => array(
			'type'       => 'BIGINT',
			'constraint' => 20,
			'null'       => true,
		),
		'created_on' => array(
			'type'       => 'TIMESTAMP',
			'null'       => false,
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

		// Relasi ke order_baju (pola FK yang dipakai project).
		$this->db->query(
			'ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_order_baju ' .
			'FOREIGN KEY (order_baju_id) REFERENCES order_baju (id);'
		);

		// Satu order hanya boleh memiliki satu transaksi.
		$this->db->query(
			'CREATE UNIQUE INDEX uq_transaksi_order_baju ON transaksi (order_baju_id);'
		);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}