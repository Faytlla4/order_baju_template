<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
        	$title_text = isset($toolbar_title) ? "{$toolbar_title} : " : '';
        	$title_text .= $this->settings_lib->item('site.title');
        	echo $title_text;
        ?>
    </title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <?php
        $is_fashioner = (isset($fashioner_home) && $fashioner_home === TRUE);

    	Assets::add_css([
    		'plugins/fontawesome-free/css/all.min.css',
    		'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
    		'plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
    		'plugins/datatables-select/css/select.bootstrap4.min.css',
    		'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    		'plugins/select2/css/select2.min.css',
    		'plugins/sweetalert2/sweetalert2.min.css',
    		'css/adminlte.min.css',
    	]);
        if ($is_fashioner) {
            Assets::add_css('css/fashioner-home.css');
        }
    	echo Assets::css();
    ?>

    <script type="text/javascript" async>
    var run_title_text = " <?=$title_text?> ";
    var run_title_speed = 300;
    var run_title_refresh = null;

    function running_title_text() {
        document.title = run_title_text;
        run_title_text = run_title_text.substring(1, run_title_text.length) + run_title_text.charAt(0);
        run_title_refresh = setTimeout("running_title_text()", run_title_speed);
    }
    running_title_text();

    var site_url = '<?=base_url()?>';
    </script>

    <?php if ($is_fashioner): ?>
    <style>
    .f-transition {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8F5EF;
        opacity: 1;
        transition: opacity 0.8s cubic-bezier(0.25, 0.1, 0.25, 1);
        pointer-events: none;
    }
    .f-transition.f-loaded {
        opacity: 0;
        pointer-events: none;
    }
    .f-transition-threads {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 1;
    }
    .f-transition.f-loaded .f-transition-threads {
        opacity: 0;
        transition: opacity 0.6s ease;
    }
    .f-thread {
        position: absolute;
        height: 10px;
        background: #403A34;
        border-radius: 5px;
        animation: f-thread-sweep 2.2s cubic-bezier(0.4, 0, 0.2, 1) both;
        box-shadow: 0 0 12px rgba(64,58,52,0.5);
    }
    .f-thread-1 { top: 12%; width: 65%; animation-delay: 0s; }
    .f-thread-2 { top: 26%; width: 55%; animation-delay: 0.12s; }
    .f-thread-3 { top: 40%; width: 70%; animation-delay: 0.04s; }
    .f-thread-4 { top: 54%; width: 60%; animation-delay: 0.16s; }
    .f-thread-5 { top: 68%; width: 62%; animation-delay: 0.08s; }
    .f-thread-6 { top: 82%; width: 52%; animation-delay: 0.14s; }
    @keyframes f-thread-sweep {
        0%   { left: -70%; }
        100% { left: 115%; }
    }
    .f-transition-center {
        position: relative;
        z-index: 2;
        opacity: 0;
        animation: f-logo-in 1s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both;
    }
    .f-transition-logo {
        width: 140px;
        height: 140px;
        object-fit: contain;
        mix-blend-mode: multiply;
    }
    @keyframes f-logo-in {
        0%   { opacity: 0; transform: scale(0.92); filter: blur(8px); }
        100% { opacity: 1; transform: scale(1); filter: blur(0); }
    }
    </style>
    <?php endif; ?>
</head>

<body class="hold-transition<?php if ($is_fashioner) echo ' fashioner-page'; ?>">
    <?php if ($is_fashioner): ?>
    <div id="f-transition" class="f-transition">
        <div class="f-transition-threads">
            <div class="f-thread f-thread-1"></div>
            <div class="f-thread f-thread-2"></div>
            <div class="f-thread f-thread-3"></div>
            <div class="f-thread f-thread-4"></div>
            <div class="f-thread f-thread-5"></div>
        </div>
        <div class="f-transition-center">
            <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="FASHIONER" class="f-transition-logo">
        </div>
    </div>
    <?php endif; ?>

    <div class="wrapper">
        <?php if ($is_fashioner): ?>
            <?php echo theme_view('home_header'); ?>
            <?php echo Template::message(); ?>
            <?php echo isset($content) ? $content : Template::content(); ?>
            <?php echo theme_view('home_hero'); ?>
        <?php else: ?>
            <?php echo theme_view('header'); ?>
            <?php echo Template::message(); ?>
            <?php echo isset($content) ? $content : Template::content(); ?>
            <?php echo theme_view('footer', array('show' => false)); ?>
        <?php endif; ?>
    </div>

    <?php
    	Assets::add_js([
    		'plugins/jquery/jquery.min.js',
    		'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
    		'plugins/moment/moment.min.js',
    		'plugins/bootstrap/js/bootstrap.bundle.min.js',
    		'plugins/datatables/jquery.dataTables.min.js',
    		'plugins/datatables-bs4/js/dataTables.bootstrap4.min.js',
    		'plugins/datatables-select/js/dataTables.select.min.js',
    		'plugins/datatables-select/js/select.bootstrap4.min.js',
    		'plugins/select2/js/select2.full.min.js',
    		'plugins/sweetalert2/sweetalert2.min.js',
    		'plugins/lodash/lodash.min.js',
    		'js/adminlte.js',
    	], 'external', true);
    	echo Assets::js();
    ?>

    <?php if ($is_fashioner): ?>
    <script>
    window.addEventListener('load', function() {
        var t = document.getElementById('f-transition');
        if (t) {
            setTimeout(function() { t.classList.add('f-loaded'); }, 800);
            setTimeout(function() { t.remove(); }, 1700);
        }
    });
    </script>
    <?php endif; ?>
</body>

</html>
