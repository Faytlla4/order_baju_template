<?php defined('BASEPATH') || exit('No direct script access allowed');

class Backup_report_model extends CI_Model
{
	public function get_document_history($tgl_mulai = '', $tgl_akhir = '')
	{
		$sql = "SELECT id, file_name, file_size, jumlah_dokumen, filter_used,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str
			FROM backup_document_history";
		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}
		if (!empty($conditions)) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql .= ' ORDER BY created_on DESC';

		return empty($params)
			? $this->db->query($sql)->result()
			: $this->db->query($sql, $params)->result();
	}

	public function get_database_history($tgl_mulai = '', $tgl_akhir = '')
	{
		$sql = "SELECT id, file_name, file_size, status,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str
			FROM backup_database_history";
		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}
		if (!empty($conditions)) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql .= ' ORDER BY created_on DESC';

		return empty($params)
			? $this->db->query($sql)->result()
			: $this->db->query($sql, $params)->result();
	}

	public function get_laporan_history($tgl_mulai = '', $tgl_akhir = '', $jenis = '')
	{
		$sql = "SELECT id, report_type, export_type, filename, filter_mulai, filter_akhir,
				record_count, file_size, created_by,
				to_char(created_on, 'DD-MM-YYYY HH24:MI') AS created_on_str,
				CASE
					WHEN filter_mulai IS NOT NULL AND filter_akhir IS NOT NULL THEN
						to_char(filter_mulai, 'DD-MM-YYYY') || ' s/d ' || to_char(filter_akhir, 'DD-MM-YYYY')
					WHEN filter_mulai IS NOT NULL THEN
						'Mulai ' || to_char(filter_mulai, 'DD-MM-YYYY')
					WHEN filter_akhir IS NOT NULL THEN
						'Sampai ' || to_char(filter_akhir, 'DD-MM-YYYY')
					ELSE 'Semua Periode'
				END AS filter_periode
			FROM laporan_history";
		$params = array();
		$conditions = array();

		if ($tgl_mulai !== '') {
			$conditions[] = 'created_on::date >= ?';
			$params[] = $tgl_mulai;
		}
		if ($tgl_akhir !== '') {
			$conditions[] = 'created_on::date <= ?';
			$params[] = $tgl_akhir;
		}
		if ($jenis !== '' && $jenis !== null) {
			$conditions[] = 'report_type = ?';
			$params[] = $jenis;
		}
		if (!empty($conditions)) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql .= ' ORDER BY created_on DESC';

		return empty($params)
			? $this->db->query($sql)->result()
			: $this->db->query($sql, $params)->result();
	}
}
