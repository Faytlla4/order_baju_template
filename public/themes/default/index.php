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
    	echo Assets::css();
    ?>

    <?php if ($is_fashioner): ?>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-home.css?v=2'); ?>">
    <?php endif; ?>

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
        transition: opacity 0.25s ease, visibility 0.25s ease;
        pointer-events: none;
    }
    .f-transition.f-loaded {
        opacity: 0;
        visibility: hidden;
    }
    .f-transition-threads {
        position: absolute;
        inset: 0;
        overflow: hidden;
    }
    .f-thread {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, #C8A96B 30%, #C8A96B 70%, transparent 100%);
        opacity: 0.25;
    }
    .f-thread-1 { top: 25%; width: 35%; left: -35%; animation: f-thread-move 4s linear infinite; }
    .f-thread-2 { top: 40%; width: 30%; left: -30%; animation: f-thread-move 4.5s linear 0.8s infinite; }
    .f-thread-3 { top: 55%; width: 40%; left: -40%; animation: f-thread-move 3.8s linear 1.5s infinite; }
    .f-thread-4 { top: 68%; width: 28%; left: -28%; animation: f-thread-move 4.2s linear 0.4s infinite; }
    .f-thread-5 { top: 80%; width: 32%; left: -32%; animation: f-thread-move 4.3s linear 1.1s infinite; }
    @keyframes f-thread-move {
        0%   { left: -40%; opacity: 0; }
        10%  { opacity: 0.5; }
        90%  { opacity: 0.5; }
        100% { left: 110%; opacity: 0; }
    }
    .f-transition-center {
        position: relative;
        z-index: 2;
        animation: f-center-in 0.5s cubic-bezier(.22,.68,0,1.1) both;
    }
    .f-transition-logo {
        width: 180px;
        height: 180px;
        object-fit: contain;
        mix-blend-mode: multiply;
    }
    @keyframes f-center-in {
        from { opacity: 0; transform: translateY(12px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
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
    (function() {
        var t = document.getElementById('f-transition');
        if (!t) return;
        function dismiss() {
            t.classList.add('f-loaded');
            setTimeout(function() { t.remove(); }, 300);
        }
        if (document.readyState === 'complete') {
            setTimeout(dismiss, 200);
        } else {
            window.addEventListener('load', function() { setTimeout(dismiss, 200); });
        }
        // Safety: force-remove after 2s max even if load event never fires
        setTimeout(function() { if (t && t.parentNode) t.remove(); }, 2000);
    })();
    </script>
    <?php endif; ?>
</body>

</html>
