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

    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-admin.css?v=3'); ?>">
    <style>
    /* Sidebar — white modern compact */
    .main-sidebar.sidebar-dark-primary { background: #FFFDF9 !important; box-shadow: 2px 0 12px rgba(0,0,0,0.06) !important; }
    .main-sidebar .brand-link { padding: 14px 18px !important; display: flex !important; align-items: center !important; gap: 10px !important; background: #FFFDF9 !important; border-bottom: 1px solid #E4D6C2 !important; }
    .main-sidebar .brand-link .brand-image { width: 30px !important; height: 30px !important; border-radius: 6px !important; }
    .main-sidebar .brand-link .brand-text { color: #403A34 !important; font-family: 'Cormorant Garamond', Georgia, serif !important; font-size: 1.05rem !important; font-weight: 600 !important; letter-spacing: 0.04em !important; text-transform: uppercase !important; }
    .sidebar-dark-primary .user-panel { border-bottom: 1px solid #E4D6C2 !important; padding: 10px 18px !important; margin-bottom: 4px !important; }
    .sidebar-dark-primary .user-panel .image img { width: 30px !important; height: 30px !important; }
    .sidebar-dark-primary .user-panel .info a { color: #403A34 !important; font-size: 0.8rem !important; font-weight: 500 !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link { color: #8C8175 !important; border-radius: 7px !important; margin: 2px 12px !important; padding: 8px 12px !important; font-size: 0.8rem !important; font-weight: 500 !important; transition: all 0.15s ease !important; line-height: 1.3 !important; background: transparent !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover { color: #FFFDF9 !important; background: #8A6A47 !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:focus { color: #FFFDF9 !important; background: #8A6A47 !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:active { color: #FFFDF9 !important; background: #7a5d3e !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active { color: #8A6A47 !important; background: rgba(200,169,107,0.12) !important; font-weight: 600 !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link .nav-icon { color: #8C8175 !important; width: 18px !important; margin-right: 8px !important; font-size: 0.85rem !important; transition: color 0.15s ease !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover .nav-icon { color: #FFFDF9 !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active .nav-icon { color: #8A6A47 !important; }
    .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link { padding-left: 42px !important; font-size: 0.78rem !important; color: #8C8175 !important; padding: 6px 12px !important; }
    .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link:hover { color: #FFFDF9 !important; background: #8A6A47 !important; }
    .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link.active { color: #8A6A47 !important; background: rgba(200,169,107,0.08) !important; }
    .sidebar-dark-primary .nav-sidebar .nav-item .nav-link .nav-arrow { color: #8C8175 !important; font-size: 0.65rem !important; transition: all 0.15s ease !important; }
    .sidebar-dark-primary .nav-sidebar .nav-item > .nav-link:hover .nav-arrow { color: #FFFDF9 !important; }
    .sidebar-dark-primary .nav-sidebar .nav-item.menu-open > .nav-link .nav-arrow { color: #8A6A47 !important; transform: rotate(90deg) !important; }
    .sidebar-dark-primary .nav-sidebar > .nav-item + .nav-item { border-top: 1px solid #E4D6C2 !important; margin-top: 4px !important; padding-top: 4px !important; }
    .sidebar-dark-primary .nav-treeview { display: none !important; }
    .sidebar-dark-primary .nav-item.menu-open > .nav-treeview { display: block !important; }
    </style>

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
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Tailor Thread Transition -->
    <!-- Tailor Thread Transition -->
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
    <style>
    .f-transition {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8F5EF;
        transition: opacity 0.35s ease, visibility 0.35s ease;
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
        background: linear-gradient(90deg, transparent 0%, #403A34 30%, #403A34 70%, transparent 100%);
        opacity: 0.5;
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
    @keyframes f-thread-slide {
        0%   { transform: translateX(-100%); }
        50%  { transform: translateX(0%); }
        100% { transform: translateX(100%); }
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

    <div class="wrapper">
        <?php
        	echo theme_view('header');
        	echo theme_view('sidebar');
        ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <?php if (isset($toolbar_title)): ?>
                            <h1 class="m-0" style="font-size:1.2rem;font-weight:600;"><?php echo $toolbar_title; ?></h1>
                            <?php endif;?>
                        </div>
                        <div class="col-sm-6" id="sub-menu">
                            <?php Template::block('sub_nav', '');?>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php
                    	echo Template::message();
                    	echo isset($content) ? $content : Template::content();
                    ?>
                </div>
            </section>
        </div>

        <?php echo theme_view('footer'); ?>
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
    		'plugins/datedropper-jquery.3.1.1/datedropper-jquery.js',
    		'plugins/timedropper-jquery.1.2.0/timedropper-jquery.js',
    		'plugins/lodash/lodash.min.js',
    		'js/adminlte.js',
    	], 'external', true);
    	echo Assets::js();
    ?>
    <script>
    $(document).ready(function() {
        // Only expand parent of currently active submenu item
        $('.nav-treeview .nav-link.active').each(function() {
            var $parent = $(this).closest('.nav-item').parent('.nav-treeview').closest('.nav-item');
            if ($parent.length) {
                $parent.addClass('menu-open');
                $parent.children('.nav-treeview').show();
                $parent.children('.nav-link').addClass('active');
            }
        });
        // Toggle submenu on click
        $(document).on('click', 'a[data-widget="treeview"]', function(e) {
            e.preventDefault();
            var $parent = $(this).closest('.nav-item');
            var $submenu = $parent.children('.nav-treeview');
            if ($submenu.length) {
                if ($parent.hasClass('menu-open')) {
                    $submenu.slideUp(200);
                    $parent.removeClass('menu-open');
                    $(this).removeClass('active');
                } else {
                    $submenu.slideDown(200);
                    $parent.addClass('menu-open');
                    $(this).addClass('active');
                }
            }
        });
    });
    // Fade out Tailor Thread Transition
    window.addEventListener('load', function() {
        var t = document.getElementById('f-transition');
        if (t) {
            setTimeout(function() { t.classList.add('f-loaded'); }, 350);
            setTimeout(function() { t.remove(); }, 800);
        }
    });
    // Force-remove hold-transition
    setTimeout(function() {
        $('body.hold-transition').removeClass('hold-transition');
    }, 1000);
    </script>
</body>

</html>
