<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Hapus kolom file legacy di tabel report.
 *
 * Tabel report dulunya memiliki kolom khusus per jenis file:
 *   - nama_file_pdf, path_file_pdf
 *   - nama_file_excel, path_file_excel
 *
 * Sejak refactor, semua hasil cetak (PDF & Excel) disimpan pada kolom
 * generic 'nama_file' dan 'path_file', dengan 'tipe_report' sebagai
 * penanda jenis. Keempat kolom legacy tersebut selalu NULL pada seluruh
 * baris dan tidak dirujuk oleh kode mana pun.
 *
 * Migration ini hanya menghapus kolom; tidak mengubah data yang ada.
 */
class Migration_Drop_legacy_file_columns extends Migration
{
	private $table_name = 'report';

	public function up()
	{
		$this->db->query(
			"ALTER TABLE {$this->table_name}
				DROP COLUMN IF EXISTS nama_file_pdf,
				DROP COLUMN IF EXISTS path_file_pdf,
				DROP COLUMN IF EXISTS nama_file_excel,
				DROP COLUMN IF EXISTS path_file_excel"
		);
	}

	public function down()
	{
		$this->dbforge->add_column($this->table_name, array(
			'nama_file_pdf' => array(
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => true,
			),
			'path_file_pdf' => array(
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => true,
			),
			'nama_file_excel' => array(
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => true,
				'default'    => NULL,
			),
			'path_file_excel' => array(
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => true,
				'default'    => NULL,
			),
		));
	}
}