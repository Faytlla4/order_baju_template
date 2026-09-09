/*
 * FASHIONER SIDEBAR STATE SYNC
 *
 * AdminLTE PushMenu remains the single owner of sidebar state.
 *
 * This script deliberately does NOT implement a second PushMenu.
 * It does NOT change URLs.
 * It does NOT change routes.
 * It does NOT touch application data.
 * It does NOT touch backend/database code.
 *
 * Desktop:
 *
 *   body.sidebar-collapse
 *       =>
 *   CSS moves the complete 290px sidebar outside the viewport.
 *
 *   body without sidebar-collapse
 *       =>
 *   CSS restores the 290px sidebar.
 *
 * Mobile:
 *
 *   body.sidebar-open
 *       =>
 *   sidebar visible.
 *
 *   body without sidebar-open
 *       =>
 *   sidebar hidden.
 */

(function (window, document, $) {

    'use strict';


    /*
     * jQuery is required because AdminLTE PushMenu uses jQuery.
     */

    if (!$) {
        return;
    }


    /*
     * Selectors.
     */

    var SIDEBAR_SELECTOR = '.main-sidebar';

    var PUSHMENU_SELECTOR = '[data-widget="pushmenu"]';

    var DESKTOP_BREAKPOINT = 992;


    /*
     * ----------------------------------------------------------------------
     * Check sidebar
     * ----------------------------------------------------------------------
     */

    function sidebarExists() {

        return $(SIDEBAR_SELECTOR).length > 0;
    }


    /*
     * ----------------------------------------------------------------------
     * Remove stale inline layout values
     * ----------------------------------------------------------------------
     *
     * AdminLTE communicates the sidebar state using body classes.
     *
     * If an old/custom script leaves inline width or margin values,
     * those values can override the CSS state.
     *
     * We therefore remove ONLY sidebar geometry.
     *
     * Nothing else is touched.
     */

    function clearStaleInlineLayout() {

        var $sidebar = $(SIDEBAR_SELECTOR);


        if (!$sidebar.length) {
            return;
        }


        /*
         * On mobile, AdminLTE's normal PushMenu behavior owns the layout.
         */

        if (window.innerWidth < DESKTOP_BREAKPOINT) {
            return;
        }


        $sidebar.css({

            width: '',

            minWidth: '',

            maxWidth: '',

            marginLeft: '',

            transform: '',

            opacity: '',

            visibility: '',

            pointerEvents: ''
        });
    }


    /*
     * ----------------------------------------------------------------------
     * Synchronize after AdminLTE PushMenu events
     * ----------------------------------------------------------------------
     */

    function syncAfterPushMenuEvent() {

        window.setTimeout(function () {

            clearStaleInlineLayout();

        }, 0);
    }


    /*
     * ----------------------------------------------------------------------
     * Listen to AdminLTE PushMenu
     * ----------------------------------------------------------------------
     *
     * IMPORTANT:
     *
     * We do NOT bind another click handler to the hamburger.
     *
     * AdminLTE remains the only component that decides whether the
     * sidebar is collapsed or expanded.
     */

    function bindPushMenuEvents() {

        $(document)

            .off(
                'collapsed.lte.pushmenu.fashionerSidebarFix ' +
                'shown.lte.pushmenu.fashionerSidebarFix ' +
                'collapsed-done.lte.pushmenu.fashionerSidebarFix',
                PUSHMENU_SELECTOR
            )

            .on(
                'collapsed.lte.pushmenu.fashionerSidebarFix ' +
                'shown.lte.pushmenu.fashionerSidebarFix ' +
                'collapsed-done.lte.pushmenu.fashionerSidebarFix',
                PUSHMENU_SELECTOR,
                syncAfterPushMenuEvent
            );
    }


    /*
     * ----------------------------------------------------------------------
     * Window resize
     * ----------------------------------------------------------------------
     *
     * Prevent stale inline values after switching between desktop/mobile.
     */

    function bindResize() {

        var timer = null;


        $(window)

            .off('resize.fashionerSidebarFix')

            .on('resize.fashionerSidebarFix', function () {

                window.clearTimeout(timer);


                timer = window.setTimeout(function () {

                    clearStaleInlineLayout();

                }, 100);
            });
    }


    /*
     * ----------------------------------------------------------------------
     * Initialization
     * ----------------------------------------------------------------------
     */

    function init() {

        if (!sidebarExists()) {
            return;
        }


        bindPushMenuEvents();

        bindResize();


        /*
         * AdminLTE initializes PushMenu on window load.
         *
         * Give AdminLTE a moment to establish the body state first.
         */

        window.setTimeout(function () {

            clearStaleInlineLayout();

        }, 50);
    }


    /*
     * jQuery DOM ready.
     */

    $(init);


})(window, document, window.jQuery);