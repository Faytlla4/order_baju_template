<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testroles extends CI_Controller {
    public function index() {
        $this->load->database();
        $q = $this->db->get('roles');
        foreach($q->result() as $r) {
            echo $r->role_name . ' -> ' . $r->login_destination . "\n";
        }
    }
}
