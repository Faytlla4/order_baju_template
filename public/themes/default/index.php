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
    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-home.css?v=6'); ?>">
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
        pointer-events: none;
        overflow: hidden;
        background: #2A2520;
    }
    .f-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: radial-gradient(circle, #C8A96B 0%, #8A6A47 50%, #5C5048 100%);
        transform: translate(-50%, -50%);
    }
    .f-circle.animate {
        animation: f-circle-expand 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    .f-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 160px;
        height: 160px;
        object-fit: contain;
        mix-blend-mode: normal;
        filter: brightness(0) invert(1);
        opacity: 0;
    }
    .f-logo.animate {
        animation: f-logo-show 0.4s ease 0.25s forwards,
                   f-logo-hide 0.35s ease 0.55s forwards;
    }
    @keyframes f-circle-expand {
        from { width: 0; height: 0; }
        to   { width: 280vmax; height: 280vmax; }
    }
    @keyframes f-logo-show {
        from { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
        to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
    @keyframes f-logo-hide {
        from { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        to   { opacity: 0; transform: translate(-50%, -50%) scale(0.85); }
    }
    </style>
    <?php endif; ?>
</head>

<body class="hold-transition<?php if ($is_fashioner) echo ' fashioner-page'; ?>">
    <?php if ($is_fashioner): ?>
    <div id="f-transition" class="f-transition">
        <div class="f-circle"></div>
        <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="FASHIONER" class="f-logo">
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
        var circle = t.querySelector('.f-circle');
        var logo = t.querySelector('.f-logo');
        circle.classList.add('animate');
        logo.classList.add('animate');
        setTimeout(function() { if (t && t.parentNode) t.remove(); }, 1000);
    })();
    </script>
    <?php endif; ?>
</body>

</html>
