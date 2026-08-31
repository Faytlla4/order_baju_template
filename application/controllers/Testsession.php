<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testsession extends CI_Controller {
    public function index() {
        $this->load->library('session');
        $this->load->library('users/auth');
        var_dump($this->session->userdata());
        var_dump($this->auth->is_logged_in());
    }
}
