<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testsessionread extends CI_Controller {
    public function index() {
        $this->load->library('session');
        $this->session->set_userdata('test_key', 'test_value');
        echo "Set session test_key to test_value.<br>";
        echo "<a href='/testsessionread/read'>Read Session</a>";
    }

    public function read() {
        $this->load->library('session');
        echo "Session test_key: " . $this->session->userdata('test_key') . "<br>";
        echo "Session user_id: " . $this->session->userdata('user_id') . "<br>";
        echo "Session identity: " . $this->session->userdata('identity') . "<br>";
        echo "Session user_token: " . $this->session->userdata('user_token') . "<br>";
    }
}
