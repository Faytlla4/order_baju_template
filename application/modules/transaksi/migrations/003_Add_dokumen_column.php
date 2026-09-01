<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Tambah kolom dokumen ke tabel transaksi.
 *
 * Kolom ini menyimpan daftar nama file dokumen dalam format JSON, misal:
 * ["nota.pdf","desain.png","bukti.jpg"]
 *
 * Hanya menambah kolom; tidak mengubah data yang sudah ada.
 */
class Migration_Add_dokumen_column extends Migration
{
	private $table_name = 'transaksi';

	public function up()
	{
		$this->dbforge->add_column($this->table_name, array(
			'dokumen' => array(
				'type'       => 'TEXT',
				'null'       => true,
				'default'    => NULL,
			),
		));

		$this->db->query(
			"UPDATE transaksi SET dokumen = '[]' WHERE dokumen IS NULL"
		);
	}

	public function down()
	{
		$this->dbforge->drop_column($this->table_name, 'dokumen');
	}
}