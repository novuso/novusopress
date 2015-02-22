<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

if (post_password_required()) {
    return;
}

novusopress_comments_area_start();

if (have_comments()) {

    novusopress_comments_title();

    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
        novusopress_comments_nav_above();
    }

    novusopress_comments_list();

    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
        novusopress_comments_nav_below();
    }

    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) {
        novusopress_comments_closed();
    }

}

novusopress_comments_form();

novusopress_comments_area_end();
