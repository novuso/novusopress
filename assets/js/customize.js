(function (root, $) {

    'use strict';

    // navbar_location
    wp.customize('novusopress_theme_options[navbar_location]', function (value) {
        value.bind(function (to) {
            var moveAbove = function () {
                var $header = $('#header');
                var $nav = $('#navigation');
                var $container = $('#navigation-container');
                $nav.insertBefore($header);
                $nav.children().wrapAll($container);
                $container.remove();
            };

            var moveBelow = function () {
                var $header = $('#header');
                var $nav = $('#navigation');
                var $container = $('#navigation-container');
                $container.insertAfter($header);
                $container.children().wrapAll($nav);
                $nav.remove();
            };

            switch (to) {
                case 'below':
                    moveBelow();
                    $('body').removeClass('fixed-nav');
                    $('#navigation').removeClass('navbar-fixed-top navbar-static-top');
                    break;
                case 'static':
                    moveAbove();
                    $('body').removeClass('fixed-nav');
                    $('#navigation').removeClass('navbar-fixed-top').addClass('navbar-static-top');
                    break;
                case 'fixed':
                    moveAbove();
                    $('body').addClass('fixed-nav');
                    $('#navigation').removeClass('navbar-static-top').addClass('navbar-fixed-top');
                    break;
            }
        });
    });

    // navbar_color
    wp.customize('novusopress_theme_options[navbar_color]', function (value) {
        value.bind(function (to) {
            $('#navigation')
                .removeClass('navbar-default navbar-inverse navbar-primary')
                .addClass('navbar-' + to);
        });
    });

})(this, jQuery);
