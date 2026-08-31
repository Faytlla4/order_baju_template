<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_link_order_baju_masters extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		$sql = "

ALTER TABLE order_baju ADD COLUMN ukuran_id INTEGER NULL DEFAULT NULL;
ALTER TABLE order_baju ADD COLUMN warna_id INTEGER NULL DEFAULT NULL;
ALTER TABLE order_baju ADD COLUMN jenis_baju_id INTEGER NULL DEFAULT NULL;

ALTER TABLE order_baju ADD CONSTRAINT fk_order_baju_ukuran FOREIGN KEY (ukuran_id) REFERENCES master_ukuran (id);
ALTER TABLE order_baju ADD CONSTRAINT fk_order_baju_warna FOREIGN KEY (warna_id) REFERENCES master_warna (id);
ALTER TABLE order_baju ADD CONSTRAINT fk_order_baju_jenis FOREIGN KEY (jenis_baju_id) REFERENCES master_jenis_baju (id);

ALTER TABLE order_baju DROP COLUMN ukuran;
ALTER TABLE order_baju DROP COLUMN warna;

";
		return $sql;
	}

	public function down()
	{
		$sql = "ALTER TABLE order_baju DROP CONSTRAINT IF EXISTS fk_order_baju_jenis;
ALTER TABLE order_baju DROP CONSTRAINT IF EXISTS fk_order_baju_warna;
ALTER TABLE order_baju DROP CONSTRAINT IF EXISTS fk_order_baju_ukuran;

ALTER TABLE order_baju ADD COLUMN ukuran VARCHAR(20) NULL;
ALTER TABLE order_baju ADD COLUMN warna VARCHAR(30) NULL;

ALTER TABLE order_baju DROP COLUMN jenis_baju_id;
ALTER TABLE order_baju DROP COLUMN warna_id;
ALTER TABLE order_baju DROP COLUMN ukuran_id;

";
		return $sql;
	}
}