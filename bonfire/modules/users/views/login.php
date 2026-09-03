<style type="text/css">
.wrapper {
    width: 100%;
}

nav.navbar {
    display: none;
}

/* Full viewport background */
.login-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('<?php echo base_url('assets/images/photo4.jpg'); ?>');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 0;
}

.login-bg::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 253, 249, 0.15);
    z-index: 1;
}

/* Login wrapper */
.login-wrapper {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

/* Login card */
.login-card {
    background: #FFFDF9;
    border: 1px solid #E4D6C2;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(64, 58, 52, 0.08), 0 2px 8px rgba(64, 58, 52, 0.04);
    width: 100%;
    max-width: 420px;
    padding: 2.5rem 2rem;
}

/* Logo inside card */
.login-card .logo-wrapper {
    text-align: center;
    margin-bottom: 1.5rem;
}

.login-card .logo-wrapper img {
    height: 64px;
    width: auto;
}

.login-card .brand-name {
    text-align: center;
    font-size: 1.1rem;
    font-weight: 700;
    color: #C8A96B;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-top: 0.5rem;
}

/* Form */
.login-card .input-group {
    margin-bottom: 1rem;
}

.login-card .form-control {
    border: 1px solid #D8C9B4;
    border-radius: 10px;
    padding: 0.7rem 1rem;
    font-size: 0.9rem;
    color: #403A34;
    background: #FFFDF9;
    transition: border-color 0.2s;
}

.login-card .form-control:focus {
    border-color: #C8A96B;
    box-shadow: 0 0 0 0.15rem rgba(200, 169, 107, 0.15);
}

.login-card .form-control::placeholder {
    color: #8C8175;
}

.login-card .input-group-append .input-group-text {
    background: #F8F5EF;
    border: 1px solid #D8C9B4;
    border-radius: 10px;
    color: #8A6A47;
}

.login-card .input-group-append {
    border-radius: 0 10px 10px 0;
}

.login-card .input-group-append .input-group-text {
    border-left: 0;
}

.login-card .input-group .form-control {
    border-right: 0;
    border-radius: 10px 0 0 10px;
}

/* Buttons */
.login-card .btn-primary {
    background: #C8A96B;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: #fff;
    transition: background 0.2s;
}

.login-card .btn-primary:hover {
    background: #8A6A47;
}

.login-card .btn-secondary {
    background: transparent;
    border: 1px solid #D8C9B4;
    border-radius: 10px;
    color: #8A6A47;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.login-card .btn-secondary:hover {
    background: #F8F5EF;
    border-color: #C8A96B;
    color: #403A34;
}

/* Checkbox */
.login-card .icheck-primary label {
    color: #8C8175;
    font-size: 0.85rem;
}

/* Alert */
.login-card .alert {
    border-radius: 10px;
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 576px) {
    .login-card {
        margin: 1rem;
        padding: 2rem 1.5rem;
    }

    .login-card .logo-wrapper img {
        height: 52px;
    }
}
</style>

<section class="login-bg"></section>

<div class="login-wrapper">
    <div class="login-card">
        <div class="logo-wrapper">
            <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo">
            <div class="brand-name">FASHIONER</div>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Alert!</h5>
            <?php echo $error; ?>
        </div>
        <?php endif;?>

        <?php echo form_open(LOGIN_URL); ?>
        <div class="input-group">
            <input type="text" class="form-control" name="login" placeholder="Username" autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        <div class="input-group">
            <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row justify-content-between align-items-center mt-3">
            <div class="col-sm-7">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember_me" id="remember" value="1">
                    <label for="remember">
                        <?php echo lang('us_remember_note'); ?>
                    </label>
                </div>
            </div>
            <div class="col-sm-5 text-right">
                <input type="submit" class="btn btn-primary btn-block" name="log-me-in" value="<?php echo lang('us_let_me_in'); ?>">
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="<?php echo site_url(); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-home"></i> <?php echo lang('bf_home');?>
            </a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
document.body.classList.add('login-page');
</script>
