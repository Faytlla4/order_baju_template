<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_master_warna_permissions extends Migration
{
	private $permissionValues = array(
		array(
			'name' => 'Master_warna.Master.View',
			'description' => 'View Master Warna',
			'status' => 'active',
		),
		array(
			'name' => 'Master_warna.Master.Create',
			'description' => 'Create Master Warna',
			'status' => 'active',
		),
		array(
			'name' => 'Master_warna.Master.Edit',
			'description' => 'Edit Master Warna',
			'status' => 'active',
		),
		array(
			'name' => 'Master_warna.Master.Delete',
			'description' => 'Delete Master Warna',
			'status' => 'active',
		),
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
			$this->db->insert($this->tableName, $permissionValue);

			$rolePermissionsData[] = array(
				$this->roleKey       => $this->roleId,
				$this->permissionKey => $this->db->insert_id(),
			);
		}

		$this->db->insert_batch($this->rolePermissionsTable, $rolePermissionsData);
	}

	public function down()
	{
		$permissionNames = array();
		foreach ($this->permissionValues as $permissionValue) {
			$permissionNames[] = $permissionValue[$this->permissionNameField];
		}

		$query = $this->db->select($this->permissionKey)
						  ->where_in($this->permissionNameField, $permissionNames)
						  ->get($this->tableName);

		if (!$query->num_rows()) {
			return;
		}

		$permissionIds = array();
		foreach ($query->result() as $row) {
			$permissionIds[] = $row->{$this->permissionKey};
		}

		$this->db->where_in($this->permissionKey, $permissionIds)
				 ->delete($this->rolePermissionsTable);

		$this->db->where_in($this->permissionNameField, $permissionNames)
				 ->delete($this->tableName);
	}
}