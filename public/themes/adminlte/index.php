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

    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-admin.css?v=6'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/fashioner-dashboard.css?v=8'); ?>">

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
        <div class="f-circle"></div>
        <img src="<?php echo base_url('assets/images/logo-transparent.png'); ?>" alt="FASHIONER" class="f-logo">
    </div>
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
    // Sidebar treeview — fully custom (does NOT use AdminLTE's plugin, so there
    // is zero conflict). The toggle triggers are tagged `data-fdb-toggle="treeview"`
    // in sidebar.php. AdminLTE's selector `[data-widget="treeview"]` no longer
    // matches anything, so the built-in plugin is bypassed entirely.
    $(function() {
        var $sidebar = $('.main-sidebar');
        if (!$sidebar.length) { return; }

        // Auto-open the parent of any currently active submenu item.
        $sidebar.find('.nav-treeview .nav-link.active').each(function() {
            var $parent = $(this).closest('.nav-item').parent('.nav-treeview').closest('.nav-item');
            if ($parent.length) {
                $parent.addClass('menu-open');
                $parent.children('.nav-link').addClass('active');
            }
        });

        // Click handler — bound directly on each toggle, NOT delegated through
        // document, so AdminLTE or any other plugin can't intercept it.
        $sidebar.find('[data-fdb-toggle="treeview"]').each(function() {
            var $trigger = $(this);
            $trigger.on('click.fdbTreeview', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $parent = $trigger.closest('.nav-item');
                var $sub = $parent.children('.nav-treeview').first();
                if (!$sub.length) { return; }
                if ($parent.hasClass('menu-open')) {
                    $sub.stop(true, true).slideUp(180, function() {
                        $sub.css('display', '');
                    });
                    $parent.removeClass('menu-open');
                    $trigger.removeClass('active');
                } else {
                    // Sibling submenus (only at the same nesting level) close.
                    $parent.siblings('.nav-item.menu-open').each(function() {
                        var $sib = $(this);
                        var $sibSub = $sib.children('.nav-treeview').first();
                        if ($sibSub.length) {
                            $sibSub.stop(true, true).slideUp(180, function() {
                                $sibSub.css('display', '');
                            });
                        }
                        $sib.removeClass('menu-open');
                        $sib.children('[data-fdb-toggle="treeview"]').removeClass('active');
                    });
                    $sub.stop(true, true).slideDown(180);
                    $parent.addClass('menu-open');
                    $trigger.addClass('active');
                }
            });
        });
    });
    // Page Transition
    (function() {
        var t = document.getElementById('f-transition');
        if (!t) return;
        var circle = t.querySelector('.f-circle');
        var logo = t.querySelector('.f-logo');
        circle.classList.add('animate');
        logo.classList.add('animate');
        setTimeout(function() { if (t && t.parentNode) t.remove(); }, 1000);
    })();
    // Force-remove hold-transition
    setTimeout(function() {
        $('body.hold-transition').removeClass('hold-transition');
    }, 1000);
    </script>
</body>

</html>
