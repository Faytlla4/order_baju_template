<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Simplified_order_baju extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS created_on timestamp NOT NULL DEFAULT NOW();
";
		return $sql;
	}

	public function down()
	{
		$sql = "ALTER TABLE order_baju DROP COLUMN IF EXISTS created_on;
";
		return $sql;
	}
}