/**
 * Pretix Integration — FreeScout sidebar loader
 * Fires an AJAX request for each sidebar widget on the page and injects the
 * returned order HTML into the placeholder container.
 */
(function ($) {
    'use strict';

    function loadOrdersForContainer(container) {
        var $container = $(container);
        var email = $container.data('email');
        var conversationId = $container.data('conversation');
        var ajaxUrl = $container.data('ajax-url');
        var $target = $('#pretix-orders-' + conversationId);

        if (!email || !ajaxUrl || !$target.length) {
            return;
        }

        fsAjax(
            { action: 'get_orders', email: email },
            ajaxUrl,
            function (response) {
                if (response.status === 'success') {
                    $target.html(response.html);
                } else {
                    var msg = response.msg || 'Error loading Pretix bookings.';
                    $target.html('<p class="pretix-error"><span class="glyphicon glyphicon-warning-sign"></span> ' + msg + '</p>');
                }
            }
        );
    }

    function initPretixSidebars() {
        $('.pretix-sidebar-block').each(function () {
            loadOrdersForContainer(this);
        });
    }

    // Initial page load
    $(document).ready(function () {
        initPretixSidebars();
    });

    // PJAX navigation (FreeScout navigates between conversations via PJAX)
    $(document).on('pjax:end', function () {
        initPretixSidebars();
    });

}(jQuery));
