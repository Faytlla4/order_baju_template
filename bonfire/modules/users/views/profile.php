<?php

$defaultLanguage = isset($user->language) ? $user->language : strtolower(settings_item('language'));
$defaultTimezone = isset($user->timezone) ? $user->timezone : strtoupper(settings_item('site.default_user_timezone'));

?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white" style="border-bottom:1px solid #E4D6C2; border-radius:12px 12px 0 0;">
                <h3 class="card-title font-weight-bold" style="color: #403A34; font-size:1.1rem;">
                    <i class="fas fa-user-edit mr-2" style="color:#8A6A47;"></i> <?php echo lang('us_edit_profile'); ?>
                </h3>
            </div>
            
            <?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'autocomplete' => 'off')); ?>
                <div class="card-body p-4">
                    <?php if (validation_errors()) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> <?php echo validation_errors(); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($user) && $user->role_name == 'Banned') : ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-ban mr-1"></i> <?php echo lang('us_banned_admin_note'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($password_hints) && !empty($password_hints)) : ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> <?php echo lang('bf_required_note'); ?></h6>
                            <small><?php echo $password_hints; ?></small>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label font-weight-bold text-sm-right" style="color:#403A34;">
                            <?php echo lang('bf_email'); ?> <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control <?php echo form_error('email') ? 'is-invalid' : ''; ?>" style="border-radius:8px; border:1px solid #E4D6C2;" type="email" id="email" name="email" value="<?php echo set_value('email', isset($user) ? $user->email : ''); ?>" required />
                            <?php if (form_error('email')): ?>
                                <div class="invalid-feedback"><?php echo form_error('email'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="display_name" class="col-sm-3 col-form-label font-weight-bold text-sm-right" style="color:#403A34;">
                            <?php echo lang('bf_display_name'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control <?php echo form_error('display_name') ? 'is-invalid' : ''; ?>" style="border-radius:8px; border:1px solid #E4D6C2;" type="text" id="display_name" name="display_name" value="<?php echo set_value('display_name', isset($user) ? $user->display_name : ''); ?>" />
                            <?php if (form_error('display_name')): ?>
                                <div class="invalid-feedback"><?php echo form_error('display_name'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (settings_item('auth.login_type') !== 'email' || settings_item('auth.use_usernames')) : ?>
                        <div class="form-group row">
                            <label for="username" class="col-sm-3 col-form-label font-weight-bold text-sm-right" style="color:#403A34;">
                                <?php echo lang('bf_username'); ?> <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control <?php echo form_error('username') ? 'is-invalid' : ''; ?>" style="border-radius:8px; border:1px solid #E4D6C2;" type="text" id="username" name="username" value="<?php echo set_value('username', isset($user) ? $user->username : ''); ?>" required />
                                <?php if (form_error('username')): ?>
                                    <div class="invalid-feedback"><?php echo form_error('username'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4" style="border-color:#E4D6C2;">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold text-sm-right" style="color:#8A6A47;">
                            <i class="fas fa-key mr-1"></i> Ubah Password
                        </label>
                        <div class="col-sm-9">
                            <small class="text-muted d-block mb-2">Kosongkan jika tidak ingin mengubah password saat ini.</small>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="password" class="small text-muted font-weight-bold"><?php echo lang('bf_password'); ?></label>
                                    <div class="input-group">
                                        <input class="form-control <?php echo form_error('password') ? 'is-invalid' : ''; ?>" style="border-radius:8px 0 0 8px; border:1px solid #E4D6C2;" type="password" id="password" name="password" value="" placeholder="Password baru" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-light toggle-password" data-target="password" aria-label="Tampilkan password" aria-pressed="false" style="border:1px solid #E4D6C2; border-left:0; border-radius:0 8px 8px 0; color:#8A6A47;">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php if (form_error('password')): ?>
                                        <div class="invalid-feedback"><?php echo form_error('password'); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="pass_confirm" class="small text-muted font-weight-bold"><?php echo lang('bf_password_confirm'); ?></label>
                                    <div class="input-group">
                                        <input class="form-control <?php echo form_error('pass_confirm') ? 'is-invalid' : ''; ?>" style="border-radius:8px 0 0 8px; border:1px solid #E4D6C2;" type="password" id="pass_confirm" name="pass_confirm" value="" placeholder="Ulangi password baru" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-light toggle-password" data-target="pass_confirm" aria-label="Tampilkan konfirmasi password" aria-pressed="false" style="border:1px solid #E4D6C2; border-left:0; border-radius:0 8px 8px 0; color:#8A6A47;">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php if (form_error('pass_confirm')): ?>
                                        <div class="invalid-feedback"><?php echo form_error('pass_confirm'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color:#E4D6C2;">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold text-sm-right" style="color:#8A6A47;">
                            <i class="fas fa-globe mr-1"></i> Preferensi
                        </label>
                        <div class="col-sm-9">
                            <?php if (!empty($languages) && is_array($languages)) : ?>
                                <?php if (count($languages) == 1) : ?>
                                    <input type="hidden" id="language" name="language" value="<?php echo $languages[0]; ?>" />
                                <?php else : ?>
                                    <div class="form-group">
                                        <label for="language" class="small text-muted font-weight-bold"><?php echo lang('bf_language'); ?></label>
                                        <select name="language" id="language" class="form-control select2 <?php echo form_error('language') ? 'is-invalid' : ''; ?>" style="border-radius:8px;">
                                            <?php foreach ($languages as $language) : ?>
                                                <option value="<?php e($language); ?>" <?php echo set_select('language', $language, $defaultLanguage == $language); ?>>
                                                    <?php e(ucfirst($language)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (form_error('language')): ?>
                                            <div class="invalid-feedback"><?php echo form_error('language'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="form-group mb-0">
                                <label for="timezones" class="small text-muted font-weight-bold"><?php echo lang('bf_timezone'); ?></label>
                                <?php
                                echo timezone_menu(
                                    set_value('timezones', isset($user) ? $user->timezone : $defaultTimezone),
                                    'form-control select2' . (form_error('timezones') ? ' is-invalid' : ''),
                                    'timezones',
                                    array('id' => 'timezones', 'style' => 'border-radius:8px;')
                                );
                                ?>
                                <?php if (form_error('timezones')): ?>
                                    <div class="invalid-feedback"><?php echo form_error('timezones'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Allow modules to render custom fields
                    if (isset($current_user)) {
                        Events::trigger('render_user_form', $current_user);
                    }
                    ?>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between align-items-center" style="border-radius:0 0 12px 12px; border-top:1px solid #E4D6C2;">
                    <a href="<?php echo site_url(SITE_AREA); ?>" class="btn btn-secondary" style="border-radius:8px;">
                        <i class="fas fa-arrow-left mr-1"></i> <?php echo lang('bf_action_cancel'); ?>
                    </a>
                    <button type="submit" name="save" class="btn btn-primary" style="background:#8A6A47; border-color:#8A6A47; border-radius:8px; padding:8px 24px;">
                        <i class="fas fa-save mr-1"></i> <?php echo lang('bf_action_save') . ' ' . lang('bf_user'); ?>
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
(function () {
    function bindPasswordToggles() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('.toggle-password');
            if (!button) return;

            var input = document.getElementById(button.getAttribute('data-target'));
            var icon = button.querySelector('i');
            if (!input) return;

            var isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            button.setAttribute('aria-pressed', String(!isVisible));
            icon.classList.toggle('fa-eye', isVisible);
            icon.classList.toggle('fa-eye-slash', !isVisible);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindPasswordToggles);
    } else {
        bindPasswordToggles();
    }
}());
</script>
