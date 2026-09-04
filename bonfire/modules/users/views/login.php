<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500;600&display=swap');

* { margin: 0; padding: 0; box-sizing: border-box; }

.wrapper {
    width: 100%;
    background: #2a231c;
    background-image: url('<?php echo base_url('assets/images/bg.jfif'); ?>');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
}

nav.navbar, .main-sidebar { display: none !important; }

.login-wrapper {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.login-wrapper::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.45) 100%);
    z-index: 1;
}

.login-card {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    position: relative;
    z-index: 2;
    background: rgba(255,253,249,0.95);
    border: none;
    border-radius: 24px;
    width: 100%;
    max-width: 420px;
    padding: 3rem 2.5rem 2.5rem;
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    box-shadow:
        0 4px 6px rgba(0,0,0,0.02),
        0 12px 24px rgba(0,0,0,0.04),
        0 32px 64px rgba(0,0,0,0.08),
        0 0 0 1px rgba(255,255,255,0.5) inset;
    animation: card-enter 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes card-enter {
    from { opacity: 0; transform: translateY(20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.login-card .logo-wrapper {
    text-align: center;
    margin-bottom: 0.5rem;
}

.login-card .logo-wrapper img {
    height: 72px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.06));
}

.login-card .brand-name {
    text-align: center;
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #403A34;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-top: 0.5rem;
}

.login-card .brand-tagline {
    text-align: center;
    font-size: 0.72rem;
    font-weight: 400;
    color: #8C8175;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-top: 0.3rem;
}

.login-card .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 1.5rem 0;
}

.login-card .divider::before,
.login-card .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, #E4D6C2, transparent);
}

.login-card .divider span {
    font-size: 0.68rem;
    color: #8C8175;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
}

.login-card .form-group {
    margin-bottom: 1rem;
}

.login-card .form-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    color: #403A34;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 0.4rem;
}

.login-card .input-icon {
    position: relative;
}

.login-card .input-icon .icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #8C8175;
    font-size: 0.82rem;
    pointer-events: none;
    transition: color 0.2s ease;
}

.login-card .input-icon .form-control {
    padding-left: 40px;
}

.login-card .input-icon:focus-within .icon {
    color: #8A6A47;
}

.login-card .form-control {
    font-family: 'Inter', sans-serif;
    width: 100%;
    border: 1.5px solid #E4D6C2;
    border-radius: 12px;
    padding: 0.75rem 1rem 0.75rem 40px;
    font-size: 0.85rem;
    color: #403A34;
    background: rgba(255,255,255,0.7);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    outline: none;
}

.login-card .form-control:focus {
    border-color: #8A6A47;
    box-shadow: 0 0 0 3px rgba(138,106,71,0.08);
    background: #fff;
}

.login-card .form-control::placeholder {
    color: #B0A899;
    font-weight: 400;
}

.login-card .btn-login {
    font-family: 'Inter', sans-serif;
    width: 100%;
    background: #1a1a1a;
    border: none;
    border-radius: 12px;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #fff;
    letter-spacing: 0.03em;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    margin-top: 0.5rem;
}

.login-card .btn-login:hover {
    background: #333;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.login-card .btn-login:active {
    transform: translateY(0);
    box-shadow: none;
}

.login-card .login-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 1.5rem;
}

.login-card .icheck-primary label {
    font-family: 'Inter', sans-serif;
    color: #8C8175;
    font-size: 0.78rem;
    font-weight: 400;
}

.login-card .btn-home {
    font-family: 'Inter', sans-serif;
    background: transparent;
    border: 1.5px solid #E4D6C2;
    border-radius: 10px;
    color: #8C8175;
    font-weight: 500;
    font-size: 0.78rem;
    padding: 0.45rem 1rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.login-card .btn-home:hover {
    background: rgba(138,106,71,0.06);
    border-color: #8A6A47;
    color: #8A6A47;
    text-decoration: none;
}

.login-card .alert {
    font-family: 'Inter', sans-serif;
    border-radius: 10px;
    font-size: 0.8rem;
    border: none;
    background: rgba(220,53,69,0.08);
    color: #dc3545;
    padding: 0.7rem 1rem;
    margin-bottom: 1rem;
}

@media (max-width: 576px) {
    .login-card { margin: 1rem; padding: 2rem 1.5rem; border-radius: 20px; }
    .login-card .logo-wrapper img { height: 56px; }
    .login-card .brand-name { font-size: 1.3rem; }
}
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="logo-wrapper">
            <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="Logo">
            <div class="brand-name">FASHIONER</div>
            <div class="brand-tagline">Clothing Management System</div>
        </div>

        <div class="divider"><span>Welcome back</span></div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="font-size:1rem;line-height:1;padding:0 0.3rem;">&times;</button>
            <?php echo $error; ?>
        </div>
        <?php endif;?>

        <?php echo form_open(LOGIN_URL); ?>
        <div class="form-group">
            <label class="form-label">Username</label>
            <div class="input-icon">
                <i class="fas fa-user icon"></i>
                <input type="text" class="form-control" name="login" placeholder="Masukkan username" autocomplete="off">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-icon">
                <i class="fas fa-lock icon"></i>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password" autocomplete="off">
            </div>
        </div>

        <button type="submit" class="btn-login" name="log-me-in">
            <?php echo lang('us_let_me_in'); ?> <i class="fas fa-arrow-right" style="margin-left:6px;font-size:0.75rem;"></i>
        </button>

        <div class="login-footer">
            <div class="icheck-primary">
                <input type="checkbox" name="remember_me" id="remember" value="1">
                <label for="remember"><?php echo lang('us_remember_note'); ?></label>
            </div>
            <a href="<?php echo site_url(); ?>" class="btn-home">
                <i class="fas fa-home"></i> <?php echo lang('bf_home');?>
            </a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>document.body.classList.add('login-page');</script>
