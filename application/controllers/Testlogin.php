<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testlogin extends CI_Controller {
    public function index() {
        $this->load->library('users/auth');
        $this->load->library('settings/settings_lib');
        
        $login = 'admin'; // Using username
        
        // Find the user to get the password
        $this->load->model('users/user_model');
        $user = $this->user_model->find_by('username', $login);
        if (!$user) {
            echo "User not found";
            return;
        }
        
        // Let's pretend the password is correct, we'll bypass the password check 
        // actually we can just manually setupSession like Auth::login does
        
        $selects = array(
            'id', 'email', 'username', 'users.role_id', 'users.deleted', 'users.active',
            'banned', 'ban_message', 'password_hash', 'force_password_reset'
        );
        if ($this->settings_lib->item('auth.do_login_redirect')) {
            $selects[] = 'login_destination';
        }

        $this->user_model->select($selects);
        $user = $this->user_model->find_by('username', $login);
        
        echo "DB login_destination: " . $user->login_destination . "\n";
        
        // Setup session manually because we don't know the password
        $this->session->set_userdata(
            array(
                'user_id'     => $user->id,
                'auth_custom' => $user->username,
                'user_token'  => sha1($user->id . $user->password_hash),
                'identity'    => $login,
                'role_id'     => $user->role_id,
                'logged_in'   => true,
            )
        );
        
        echo "Session set. user_token: " . sha1($user->id . $user->password_hash) . "\n";
        
        $this->auth->login_destination = empty($user->login_destination) ? '' : $user->login_destination;
        echo "Auth->login_destination: " . $this->auth->login_destination . "\n";
        
        // Now test Auth::is_logged_in()
        echo "is_logged_in: " . ($this->auth->is_logged_in() ? 'TRUE' : 'FALSE') . "\n";
        
        if ($this->settings_lib->item('auth.do_login_redirect')
            && !empty($this->auth->login_destination)
        ) {
            echo "Redirecting to: " . $this->auth->login_destination . "\n";
        } else {
            echo "Redirecting to: /\n";
        }
    }
}
