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

    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-admin.css'); ?>">

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
    <div id="f-transition" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:#F8F5EF;transition:opacity 0.35s ease,visibility 0.35s ease;pointer-events:none;">
        <div style="text-align:center;animation:f-center-in 0.5s cubic-bezier(.22,.68,0,1.1) both;">
            <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="FASHIONER" style="width:80px;height:80px;object-fit:contain;mix-blend-mode:multiply;">
        </div>
    </div>
    <style>
    @keyframes f-center-in {
        from { opacity:0; transform:translateY(12px) scale(0.97); }
        to   { opacity:1; transform:translateY(0) scale(1); }
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
    // Sidebar submenus
    $(function() {
        $('.nav-sidebar .nav-item.active').parents('.nav-treeview').each(function() {
            $(this).prev('.nav-link').addClass('active');
            $(this).parent().addClass('menu-open');
        });
    });
    // Fade out transition
    window.addEventListener('load', function() {
        var t = document.getElementById('f-transition');
        if (t) {
            setTimeout(function() { t.style.opacity = '0'; t.style.visibility = 'hidden'; }, 300);
            setTimeout(function() { t.remove(); }, 700);
        }
    });
    // Force-remove hold-transition
    setTimeout(function() {
        $('body.hold-transition').removeClass('hold-transition');
    }, 1000);
    </script>
</body>

</html>
