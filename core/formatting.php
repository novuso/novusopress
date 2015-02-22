<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

// content more_link
if (!function_exists('novusopress_formatting_content_more_link')) {
    function novusopress_formatting_content_more_link($link) {
        $link = preg_replace('/#more-[0-9]+/', '', $link);
        $link = str_replace('class="', 'class="btn btn-default ', $link);

        return $link;
    }
}

add_filter('the_content_more_link', 'novusopress_formatting_content_more_link', 10, 1);

// comment reply_link
if (!function_exists('novusopress_formatting_comment_reply_link')) {
    function novusopress_formatting_comment_reply_link($link) {
        $link = str_replace('class="', 'class="btn btn-xs btn-default ', $link);
        $link = str_replace("class='", "class='btn btn-xs btn-default ", $link);

        return $link;
    }
}

add_filter('comment_reply_link', 'novusopress_formatting_comment_reply_link', 10, 1);
