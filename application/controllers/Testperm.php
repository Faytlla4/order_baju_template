<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testperm extends CI_Controller {
    public function index() {
        $this->load->library('users/auth');
        var_dump($this->auth->has_permission('Site.Content.View', 1));
        var_dump($this->auth->has_permission('Site.Content.View'));
    }
}
