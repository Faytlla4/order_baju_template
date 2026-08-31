<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testuser extends CI_Controller {
    public function index() {
        $this->load->model('users/user_model');
        $this->load->library('settings/settings_lib');
        
        $selects = array(
            'id',
            'email',
            'username',
            'users.role_id',
            'users.deleted',
            'users.active',
            'banned',
            'ban_message',
            'password_hash',
            'force_password_reset'
        );

        if ($this->settings_lib->item('auth.do_login_redirect')) {
            $selects[] = 'login_destination';
        }

        $this->user_model->select($selects);
        // Let's just find the first user
        $user = $this->user_model->find(1);
        var_dump($user);
    }
}
