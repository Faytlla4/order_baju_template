<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Menjamin Kode Order unik pada database.
 *
 * Generator aplikasi menggunakan PostgreSQL advisory lock,
 * sedangkan unique index menjadi lapisan pengaman terakhir
 * apabila ada proses lain yang mencoba memasukkan kode sama.
 */
class Migration_Order_kode_unique extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		return "
CREATE UNIQUE INDEX IF NOT EXISTS
ux_order_baju_kode_order
ON order_baju (kode_order);
";
	}

	public function down()
	{
		return "
DROP INDEX IF EXISTS
ux_order_baju_kode_order;
";
	}
}