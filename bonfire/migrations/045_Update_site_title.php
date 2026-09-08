<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Migration - Update site title to FASHIONER
 */
class Migration_Update_site_title extends CI_Migration {

    public function up() {
        $this->db->where('name', 'site.title');
        $this->db->update('settings', array('value' => 'FASHIONER'));
    }

    public function down() {
        $this->db->where('name', 'site.title');
        $this->db->update('settings', array('value' => 'SI-Reklame'));
    }
}
