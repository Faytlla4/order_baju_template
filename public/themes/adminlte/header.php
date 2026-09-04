<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="color:#403A34;"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <?php
                $userDisplayName = !empty($current_user->display_name) ? $current_user->display_name : ($this->settings_lib->item('auth.use_usernames') ? $current_user->username : $current_user->email);
            ?>
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="display:flex;align-items:center;gap:8px;">
                <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="user-image img-circle" style="width:30px;height:30px;">
                <span class="d-none d-md-inline" style="font-size:0.85rem;font-weight:500;color:#403A34;"><?php echo $userDisplayName; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="border:1px solid #E4D6C2;border-radius:10px;box-shadow:0 4px 16px rgba(64,58,52,0.1);right:0;left:auto;">
                <li class="user-header" style="background:#FFFDF9;border-radius:10px 10px 0 0;padding:16px;text-align:center;">
                    <img src="<?php echo base_url('assets/images/anonym.png'); ?>" class="img-circle elevation-2" style="width:48px;height:48px;">
                    <p style="color:#403A34;font-weight:600;margin:8px 0 0;font-size:0.9rem;"><?php echo $userDisplayName; ?></p>
                    <small style="color:#8C8175;font-size:0.75rem;"><?php echo $current_user->email; ?></small>
                </li>
                <li class="user-footer" style="padding:12px;display:flex;justify-content:space-between;align-items:center;">
                    <a href="<?php echo site_url('users/profile'); ?>" class="btn btn-secondary btn-sm" style="border-radius:8px;font-size:0.78rem;">
                        <i class="fas fa-user-cog"></i> Profile
                    </a>
                    <a href="<?php echo site_url('logout'); ?>" class="btn btn-primary btn-sm" style="border-radius:8px;font-size:0.78rem;background:#8A6A47;border-color:#8A6A47;color:#fff;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
