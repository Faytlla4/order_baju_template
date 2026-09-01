<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Simplified_transaksi extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "
ALTER TABLE transaksi DROP COLUMN IF EXISTS tanggal_transaksi;
ALTER TABLE transaksi DROP COLUMN IF EXISTS deleted;
ALTER TABLE transaksi DROP COLUMN IF EXISTS deleted_by;
ALTER TABLE transaksi DROP COLUMN IF EXISTS created_by;
ALTER TABLE transaksi DROP COLUMN IF EXISTS modified_on;
ALTER TABLE transaksi DROP COLUMN IF EXISTS modified_by;
";
		return $sql;
	}

	public function down()
	{
		$sql = '';
		return $sql;
	}
}