<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Simplified_master_warna extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "
ALTER TABLE master_warna DROP COLUMN IF EXISTS keterangan;
ALTER TABLE master_warna DROP COLUMN IF EXISTS deleted;
ALTER TABLE master_warna DROP COLUMN IF EXISTS deleted_by;
ALTER TABLE master_warna DROP COLUMN IF EXISTS created_by;
ALTER TABLE master_warna DROP COLUMN IF EXISTS modified_on;
ALTER TABLE master_warna DROP COLUMN IF EXISTS modified_by;
";
		return $sql;
	}

	public function down()
	{
		$sql = '';
		return $sql;
	}
}