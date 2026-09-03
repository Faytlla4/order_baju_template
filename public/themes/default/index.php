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

    <!-- Tailor Thread Transition CSS -->
    <style>
    .f-transition {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8F5EF;
        transition: opacity 0.45s ease, visibility 0.45s ease;
    }
    .f-transition.f-loaded {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .f-transition-threads {
        position: absolute;
        inset: 0;
        overflow: hidden;
    }
    .f-thread {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, transparent, #C8A96B, transparent);
        opacity: 0.35;
        animation: f-thread-slide 1.8s ease-in-out infinite;
    }
    .f-thread-1 { top: 20%; width: 100%; animation-delay: 0s; }
    .f-thread-2 { top: 35%; width: 80%; left: 10%; animation-delay: 0.15s; }
    .f-thread-3 { top: 50%; width: 60%; left: 20%; animation-delay: 0.3s; }
    .f-thread-4 { top: 65%; width: 80%; left: 10%; animation-delay: 0.15s; }
    .f-thread-5 { top: 80%; width: 100%; animation-delay: 0s; }
    @keyframes f-thread-slide {
        0%   { transform: translateX(-100%); }
        50%  { transform: translateX(0%); }
        100% { transform: translateX(100%); }
    }
    .f-transition-center {
        position: relative;
        z-index: 2;
        text-align: center;
        animation: f-center-in 0.6s cubic-bezier(.22,.68,0,1.1) both;
    }
    .f-transition-logo {
        width: 72px;
        height: 72px;
        object-fit: contain;
        mix-blend-mode: multiply;
        margin-bottom: 12px;
    }
    .f-transition-brand {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.4rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        color: #403A34;
        text-transform: uppercase;
    }
    @keyframes f-center-in {
        from { opacity: 0; transform: translateY(16px) scale(0.96); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    </style>
</head>

<body class="hold-transition<?php if ($is_fashioner) echo ' fashioner-page'; ?>">
    <!-- Tailor Thread Transition — all pages -->
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
            <div class="f-transition-brand">FASHIONER</div>
        </div>
    </div>

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

    <script>
    window.addEventListener('load', function() {
        var t = document.getElementById('f-transition');
        if (t) {
            setTimeout(function() { t.classList.add('f-loaded'); }, 350);
            setTimeout(function() { t.remove(); }, 900);
        }
    });
    </script>
</body>

</html>
