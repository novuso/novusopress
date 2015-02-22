<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

if (!function_exists('novusopress_searchform_render')) {
    function novusopress_searchform_render($count, $echo = true) {
        $output = [];
        $formId = $count ? 'search-form-'.$count : 'search-form';
        $inputId = $count ? 'input_search_'.$count : 'input_search';
        $submitId = $count ? 'searchsubmit-'.$count : 'searchsubmit';
        $level = 4;
        $indent = str_repeat('    ', $level + 1);

        $output[] = PHP_EOL;
        $output[] = $indent;
        $output[] = sprintf('<form id="%s" method="get" action="%s" role="search">', $formId, esc_url(home_url('/')));
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 2);
        $output[] = sprintf('<label for="%s" class="sr-only">%s</label>', $inputId, __('Search for', 'novusopress'));
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 2);
        $output[] = '<div class="input-group">';
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 3);
        $output[] = sprintf(
            '<input id="%s" type="search" name="s" placeholder="%s" class="form-control">',
            $inputId,
            __('Search', 'novusopress')
        );
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 3);
        $output[] = '<span class="input-group-btn">';
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 4);
        $output[] = sprintf('<button id="%s" type="submit" class="btn btn-default">', $submitId);
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 5);
        $output[] = '<span class="fa fa-search"></span>';
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 4);
        $output[] = '</button>';
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 3);
        $output[] = '</span>';
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level + 2);
        $output[] = '</div>';
        $output[] = PHP_EOL;
        $output[] = $indent;
        $output[] = sprintf('</form><!-- #%s -->', $formId);
        $output[] = PHP_EOL;
        $output[] = str_repeat('    ', $level);

        $output = apply_filters('novusopress_searchform_render_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}
