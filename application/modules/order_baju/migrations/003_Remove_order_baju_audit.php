<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Sederhanakan tabel order_baju: hapus kolom audit Bonfire.
 * Kolom bisnis + FK master tetap dipertahankan.
 */
class Migration_Remove_order_baju_audit extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "
ALTER TABLE order_baju DROP COLUMN IF EXISTS created_on;
ALTER TABLE order_baju DROP COLUMN IF EXISTS created_by;
ALTER TABLE order_baju DROP COLUMN IF EXISTS modified_on;
ALTER TABLE order_baju DROP COLUMN IF EXISTS modified_by;
ALTER TABLE order_baju DROP COLUMN IF EXISTS deleted;
ALTER TABLE order_baju DROP COLUMN IF EXISTS deleted_by;
";
		return $sql;
	}

	public function down()
	{
		$sql = "
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS created_on TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW();
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS created_by BIGINT;
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS modified_on TIMESTAMP WITH TIME ZONE;
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS modified_by BIGINT;
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS deleted SMALLINT NOT NULL DEFAULT 0;
ALTER TABLE order_baju ADD COLUMN IF NOT EXISTS deleted_by BIGINT;
";
		return $sql;
	}
}