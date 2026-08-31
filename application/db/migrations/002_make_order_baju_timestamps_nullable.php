<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_make_order_baju_timestamps_nullable extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "

ALTER TABLE order_baju ALTER COLUMN deleted_on DROP NOT NULL;
ALTER TABLE order_baju ALTER COLUMN modified_on DROP NOT NULL;
ALTER TABLE order_baju ALTER COLUMN deleted_on DROP DEFAULT;
ALTER TABLE order_baju ALTER COLUMN modified_on DROP DEFAULT;

";
		return $sql;
	}

	public function down()
	{
		$sql = "ALTER TABLE order_baju ALTER COLUMN deleted_on SET DEFAULT NULL;
ALTER TABLE order_baju ALTER COLUMN modified_on SET DEFAULT NULL;

";
		return $sql;
	}
}