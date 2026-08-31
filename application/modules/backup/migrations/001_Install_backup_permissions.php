<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_backup_permissions extends Migration
{
	private $permissionValues = array(
		array('name' => 'Backup.Backup.View', 'description' => 'View Backup', 'status' => 'active'),
		array('name' => 'Backup.Backup.Document', 'description' => 'Backup Dokumen (PDF+XLSX ZIP)', 'status' => 'active'),
		array('name' => 'Backup.Backup.Database', 'description' => 'Backup Database (pg_dump ZIP)', 'status' => 'active'),
	);

	private $permissionKey = 'permission_id';
	private $permissionNameField = 'name';
	private $rolePermissionsTable = 'role_permissions';
	private $roleId = '1';
	private $roleKey = 'role_id';
	private $tableName = 'permissions';

	public function up()
	{
		$rolePermissionsData = array();
		foreach ($this->permissionValues as $permissionValue) {
			// Skip if already exists
			$exists = $this->db->where($this->permissionNameField, $permissionValue[$this->permissionNameField])->get($this->tableName)->row();
			if ($exists) {
				$rolePermissionsData[] = array($this->roleKey => $this->roleId, $this->permissionKey => $exists->{$this->permissionKey});
				continue;
			}
			$this->db->insert($this->tableName, $permissionValue);
			$rolePermissionsData[] = array($this->roleKey => $this->roleId, $this->permissionKey => $this->db->insert_id());
		}
		if (!empty($rolePermissionsData)) {
			// Avoid duplicate role_permissions
			foreach ($rolePermissionsData as $rp) {
				$exists = $this->db->where($this->roleKey, $rp[$this->roleKey])->where($this->permissionKey, $rp[$this->permissionKey])->get($this->rolePermissionsTable)->row();
				if (!$exists) {
					$this->db->insert($this->rolePermissionsTable, $rp);
				}
			}
		}
	}

	public function down()
	{
		$permissionNames = array();
		foreach ($this->permissionValues as $permissionValue) {
			$permissionNames[] = $permissionValue[$this->permissionNameField];
		}
		$query = $this->db->select($this->permissionKey)->where_in($this->permissionNameField, $permissionNames)->get($this->tableName);
		if (!$query->num_rows()) {
			return;
		}
		$permissionIds = array();
		foreach ($query->result() as $row) {
			$permissionIds[] = $row->{$this->permissionKey};
		}
		$this->db->where_in($this->permissionKey, $permissionIds)->delete($this->rolePermissionsTable);
		$this->db->where_in($this->permissionNameField, $permissionNames)->delete($this->tableName);
	}
}
