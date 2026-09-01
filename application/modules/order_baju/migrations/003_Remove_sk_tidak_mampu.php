<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Remove_sk_tidak_mampu extends Migration
{
	public function up()
	{
		// Remove old permissions
		$permissionNames = array(
			'Sk_tidak_mampu.Content.View',
			'Sk_tidak_mampu.Content.Create',
			'Sk_tidak_mampu.Content.Edit',
			'Sk_tidak_mampu.Content.Delete',
		);

		$query = $this->db->select('permission_id')
						  ->where_in('name', $permissionNames)
						  ->get('permissions');

		if ($query->num_rows()) {
			$permissionIds = array();
			foreach ($query->result() as $row) {
				$permissionIds[] = $row->permission_id;
			}

			$this->db->where_in('permission_id', $permissionIds)
					 ->delete('role_permissions');

			$this->db->where_in('name', $permissionNames)
					 ->delete('permissions');
		}

		// Drop old table
		if ($this->db->table_exists('sk_tidak_mampu')) {
			$this->dbforge->drop_table('sk_tidak_mampu');
		}
	}

	public function down()
	{
		// Restore old permissions
		$permissionValues = array(
			array(
				'name' => 'Sk_tidak_mampu.Content.View',
				'description' => 'View Sk_tidak_mampu Content',
				'status' => 'active',
			),
			array(
				'name' => 'Sk_tidak_mampu.Content.Create',
				'description' => 'Create Sk_tidak_mampu Content',
				'status' => 'active',
			),
			array(
				'name' => 'Sk_tidak_mampu.Content.Edit',
				'description' => 'Edit Sk_tidak_mampu Content',
				'status' => 'active',
			),
			array(
				'name' => 'Sk_tidak_mampu.Content.Delete',
				'description' => 'Delete Sk_tidak_mampu Content',
				'status' => 'active',
			),
		);

		$rolePermissionsData = array();
		foreach ($permissionValues as $permissionValue) {
			$this->db->insert('permissions', $permissionValue);
			$rolePermissionsData[] = array(
				'role_id'       => '1',
				'permission_id' => $this->db->insert_id(),
			);
		}
		$this->db->insert_batch('role_permissions', $rolePermissionsData);

		// Recreate old table
		$fields = array(
			'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true),
			'nama' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false),
			'alamat' => array('type' => 'TEXT', 'null' => false),
			'jenis_surat' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false),
			'no_telepon' => array('type' => 'INT', 'constraint' => 16, 'null' => false),
			'tanggal' => array('type' => 'DATE', 'null' => false),
			'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => '0'),
			'deleted_on' => array('type' => 'TIMESTAMP'),
			'deleted_by' => array('type' => 'BIGINT', 'constraint' => 20, 'null' => true),
			'created_on' => array('type' => 'TIMESTAMP', 'default' => 'NOW()'),
			'created_by' => array('type' => 'BIGINT', 'constraint' => 20, 'null' => false),
			'modified_on' => array('type' => 'TIMESTAMP'),
			'modified_by' => array('type' => 'BIGINT', 'constraint' => 20, 'null' => true),
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('sk_tidak_mampu');
	}
}
