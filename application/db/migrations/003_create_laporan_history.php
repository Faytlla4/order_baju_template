<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Create_laporan_history extends Migration
{
	public $migration_type = 'sql';

	public function up()
	{
		return "
CREATE TABLE IF NOT EXISTS laporan_history (
    id SERIAL PRIMARY KEY,
    report_type VARCHAR(50) NOT NULL,
    export_type VARCHAR(10) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filter_mulai DATE NULL,
    filter_akhir DATE NULL,
    record_count INTEGER NOT NULL DEFAULT 0,
    file_size INTEGER NOT NULL DEFAULT 0,
    created_by BIGINT NULL,
    created_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_laporan_history_report_type
    ON laporan_history (report_type);
CREATE INDEX IF NOT EXISTS idx_laporan_history_created_on
    ON laporan_history (created_on);
";
	}

	public function down()
	{
		return 'DROP TABLE IF EXISTS laporan_history;';
	}
}
