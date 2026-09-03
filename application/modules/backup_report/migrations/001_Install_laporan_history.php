<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_laporan_history extends Migration
{
	private $table_name = 'laporan_history';

	private $fields = array(
		'id' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'auto_increment' => true,
		),
		'report_type' => array(
			'type'       => 'VARCHAR',
			'constraint' => 50,
			'null'       => false,
		),
		'export_type' => array(
			'type'       => 'VARCHAR',
			'constraint' => 10,
			'null'       => false,
		),
		'filename' => array(
			'type'       => 'VARCHAR',
			'constraint' => 255,
			'null'       => false,
		),
		'filter_mulai' => array(
			'type'       => 'DATE',
			'null'       => true,
		),
		'filter_akhir' => array(
			'type'       => 'DATE',
			'null'       => true,
		),
		'record_count' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'null'       => false,
			'default'    => 0,
		),
		'file_size' => array(
			'type'       => 'INT',
			'constraint' => 11,
			'null'       => false,
			'default'    => 0,
		),
		'created_by' => array(
			'type'       => 'BIGINT',
			'constraint' => 20,
			'null'       => true,
		),
		'created_on' => array(
			'type'       => 'TIMESTAMP',
			'null'       => false,
			'default'    => 'NOW()',
		),
	);

	public function up()
	{
		$this->dbforge->add_field($this->fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->add_key('report_type');
		$this->dbforge->add_key('created_on');
		$this->dbforge->create_table($this->table_name);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}
