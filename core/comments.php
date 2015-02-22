<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

use Novuso\WordPress\Theme\NovusoPress\CommentWalker;

if (!function_exists('novusopress_comments_list')) {
    function novusopress_comments_list(array $args = [], $echo = true) {
        $defaults = [
            'listEl'       => 'ol',
            'listId'       => 'comment-list',
            'listClass'    => 'comment-list list-unstyled',
            'callback'     => 'novusopress_comment',
            'endCallback'  => 'novusopress_comment_end',
            'indent'       => 5,
            'tab'          => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = sprintf('%s<%s id="%s" class="%s">%s', $tabs, $listEl, $listId, $listClass, PHP_EOL);
        $output[] = wp_list_comments([
            'callback'     => $callback,
            'end-callback' => $endCallback,
            'walker'       => new CommentWalker(),
            'echo'         => false
        ]);
        $output[] = sprintf('%s</%s><!-- #%s -->%s', $tabs, $listEl, $listId, PHP_EOL);

        $output = apply_filters('novusopress_comments_list_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comment')) {
    function novusopress_comment($comment, $args, $depth) {
        $tab = '    ';
        $indent = ($depth == 1) ? 5 + $depth : 4 + ($depth * 2);
        $output = [];

        switch ($comment->comment_type) {
            case 'pingback':
            case 'trackback':
                $output[] = sprintf(
                    '%s<li id="li-comment-%s" class="post pingback">%s',
                    str_repeat($tab, $indent),
                    get_comment_ID(),
                    PHP_EOL
                );
                $output[] = sprintf(
                    '%s<p>%s: %s',
                    str_repeat($tab, $indent + 1),
                    __('Pingback', 'novusopress'),
                    get_comment_author_link()
                );
                if (current_user_can('edit_comment', $comment->comment_ID)) {
                    $output[] = sprintf(
                        ' <span class="edit-link"><a href="%s">%s</a></span>',
                        get_edit_comment_link(),
                        __('Edit', 'novusopress')
                    );
                }
                $output[] = sprintf('</p>%s', PHP_EOL);
                break;
            default:
                $output[] = sprintf(
                    '%s<li id="li-comment-%s" class="%s">%s',
                    str_repeat($tab, $indent),
                    get_comment_ID(),
                    implode(' ', get_comment_class()),
                    PHP_EOL
                );
                $output[] = sprintf(
                    '%s<div id="comment-%s" class="comment-body">%s',
                    str_repeat($tab, $indent + 1),
                    get_comment_ID(),
                    PHP_EOL
                );
                $output[] = sprintf('%s<footer class="comment-meta">%s', str_repeat($tab, $indent + 2), PHP_EOL);
                $output[] = sprintf(
                    '%s<div class="comment-author vcard pad-bottom clearfix">%s',
                    str_repeat($tab, $indent + 3),
                    PHP_EOL
                );
                if (current_user_can('edit_comment', $comment->comment_ID)) {
                    $output[] = str_repeat($tab, $indent + 4);
                    $output[] = sprintf(
                        '<span class="edit-link"><a href="%s" class="btn btn-xs btn-default align-right">%s</a></span>',
                        get_edit_comment_link(),
                        __('Edit', 'novusopress')
                    );
                    $output[] = PHP_EOL;
                }
                $size = 64;
                if ('0' != $comment->comment_parent) {
                    $size = 38;
                }
                $output[] = str_repeat($tab, $indent + 4);
                $output[] = '<div class="thumbnail inline-box align-left">';
                $output[] = get_avatar($comment, $size);
                $output[] = '</div>';
                $output[] = PHP_EOL;
                $output[] = str_repeat($tab, $indent + 4);
                $output[] = sprintf(
                    // translators: 1: comment author, 2: date and time
                    __('%1$s on %2$s', 'novusopress'),
                    sprintf('<span class="fn">%s</span>', get_comment_author_link()),
                    sprintf(
                        '<a href="%1$s" class="comment-datetime"><time datetime="%2$s">%3$s</time></a>',
                        esc_url(get_comment_link($comment->comment_ID)),
                        get_comment_time('c'),
                        // translators: 1: date, 2: time
                        sprintf(__('%1$s at %2$s', 'novusopress'), get_comment_date(), get_comment_time())
                    )
                );
                $output[] = PHP_EOL;
                $output[] = sprintf('%s</div><!-- .comment-author -->%s', str_repeat($tab, $indent + 3), PHP_EOL);
                if ($comment->comment_approved == '0') {
                    $output[] = PHP_EOL;
                    $output[] = str_repeat($tab, $indent + 3);
                    $output[] = sprintf(
                        '<div class="alert alert-info comment-awaiting-moderation">%s</div>',
                        __('Your comment is awaiting moderation', 'novusopress')
                    );
                }
                $output[] = sprintf('%s</footer><!-- .comment-meta -->%s', str_repeat($tab, $indent + 2), PHP_EOL);
                $output[] = str_repeat($tab, $indent + 2);
                $output[] = '<div class="comment-content">';
                $output[] = PHP_EOL;
                $output[] = str_repeat($tab, $indent + 3);
                ob_start();
                comment_text();
                $output[] = ob_get_clean();
                $output[] = str_repeat($tab, $indent + 2);
                $output[] = '</div>';
                $output[] = PHP_EOL;
                $output[] = str_repeat($tab, $indent + 2);
                $output[] = '<div class="reply">';
                $output[] = PHP_EOL;
                $output[] = str_repeat($tab, $indent + 3);
                $output[] = get_comment_reply_link(array_merge($args, [
                    'reply_text' => __('Reply &darr;', 'novusopress'),
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth']
                ]));
                $output[] = PHP_EOL;
                $output[] = str_repeat($tab, $indent + 2);
                $output[] = '</div>';
                $output[] = PHP_EOL;
                $output[] = sprintf(
                    '%s</div><!-- #comment-%s -->%s',
                    str_repeat($tab, $indent + 1),
                    get_comment_ID(),
                    PHP_EOL
                );
                break;
        }

        if ($args['has_children'] && ((integer) $args['max_depth'] > $depth)) {
            $output[] = str_repeat($tab, $indent + 1);
        }

        $output = apply_filters('novusopress_comment_output', implode('', $output));

        echo $output;
    }
}

if (!function_exists('novusopress_comment_end')) {
    function novusopress_comment_end($comment, $args, $depth) {
        $tab = '    ';
        $indent = ($depth == 1) ? 7 + $depth : 6 + ($depth * 2);
        $output = [];

        $output[] = sprintf(
            '%s</li><!-- #li-comment-%d -->%s',
            str_repeat($tab, $indent),
            $comment->comment_ID,
            PHP_EOL
        );

        $output = apply_filters('novusopress_comment_end_output', implode('', $output));

        echo $output;
    }
}

if (!function_exists('novusopress_comments_title')) {
    function novusopress_comments_title(array $args = [], $echo = true) {
        $defaults = [
            'headerEl'    => 'h3',
            'headerId'    => 'comments-title',
            'headerClass' => 'comments-title',
            'indent'      => 5,
            'tab'         => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $title = sprintf(
            _nx(
                'One thought on &ldquo;%2$s&rdquo;',
                '%1$s thoughts on &ldquo;%2$s&rdquo;',
                get_comments_number(),
                'comments title',
                'novusopress'
            ),
            number_format_i18n(get_comments_number()),
            get_the_title()
        );

        $output[] = $tabs;
        $output[] = sprintf('<%s id="%s" class="%s">%s</%s>', $headerEl, $headerId, $headerClass, $title, $headerEl);
        $output[] = PHP_EOL;

        $output = apply_filters('novusopress_comments_title_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_nav_above')) {
    function novusopress_comments_nav_above(array $args = [], $echo = true) {
        $defaults = [
            'containerEl'    => 'nav',
            'containerId'    => 'comment-nav-above',
            'containerClass' => 'comment-nav-above',
            'indent'         => 5,
            'tab'            => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = $tabs;
        $output[] = sprintf('<%s id="%s" class="%s">', $containerEl, $containerId, $containerClass);
        $output[] = PHP_EOL;
        $output[] = str_repeat($tab, $indent + 1);
        $links = trim(paginate_comments_links(['echo' => false, 'type' => 'list']));
        $links = str_replace("\n", PHP_EOL.str_repeat($tab, $indent + 1), $links);
        $links = str_replace("\t", $tab, $links);
        $links = str_replace("<ul class='page-numbers'>", '<ul class="pagination">', $links);
        $links = str_replace(
            "<li><span class='page-numbers current'>",
            '<li class="active"><span class="page-numbers">',
            $links
        );
        $output[] = $links;
        $output[] = PHP_EOL;
        $output[] = $tabs;
        $output[] = sprintf('</%s><!-- #%s -->', $containerEl, $containerId);
        $output[] = PHP_EOL;

        $output = apply_filters('novusopress_comments_nav_above_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_nav_below')) {
    function novusopress_comments_nav_below(array $args = [], $echo = true) {
        $defaults = [
            'containerEl'    => 'nav',
            'containerId'    => 'comment-nav-below',
            'containerClass' => 'comment-nav-below',
            'indent'         => 5,
            'tab'            => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = $tabs;
        $output[] = sprintf('<%s id="%s" class="%s">', $containerEl, $containerId, $containerClass);
        $output[] = PHP_EOL;
        $output[] = str_repeat($tab, $indent + 1);
        $links = trim(paginate_comments_links(['echo' => false, 'type' => 'list']));
        $links = str_replace("\n", PHP_EOL.str_repeat($tab, $indent + 1), $links);
        $links = str_replace("\t", $tab, $links);
        $links = str_replace("<ul class='page-numbers'>", '<ul class="pagination">', $links);
        $links = str_replace(
            "<li><span class='page-numbers current'>",
            '<li class="active"><span class="page-numbers">',
            $links
        );
        $output[] = $links;
        $output[] = PHP_EOL;
        $output[] = $tabs;
        $output[] = sprintf('</%s><!-- #%s -->', $containerEl, $containerId);
        $output[] = PHP_EOL;

        $output = apply_filters('novusopress_comments_nav_above_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_closed')) {
    function novusopress_comments_closed(array $args = [], $echo = true) {
        $defaults = [
            'containerEl'    => 'p',
            'containerId'    => 'nocomments',
            'containerClass' => 'nocomments',
            'indent'         => 5,
            'tab'            => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = $tabs;
        $output[] = sprintf('<%s id="%s" class="%s">', $containerEl, $containerId, $containerClass);
        $output[] = __('Comments are closed', 'novusopress');
        $output[] = sprintf('</%>', $containerEl);
        $output[] = PHP_EOL;

        $output = apply_filters('novusopress_comments_closed_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_form')) {
    function novusopress_comments_form($echo = true) {
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $ariaReq = ($req ? ' aria-required="true"' : '');

        $fields = [
            'author' => PHP_EOL.'<div class="form-group"><label for="author"'.($req ? ' class="required ' : ' class="')
                .'col-sm-3 control-label">'.__('Name', 'novusopress').'</label><div class="col-sm-9"><input id="author" '
                .'name="author" type="text" class="form-control" value="'.esc_attr($commenter['comment_author'])
                .'"'.$ariaReq.'></div></div>',
            'email'  => '<div class="form-group"><label for="email"'.($req ? ' class="required ' : ' class="')
                .'col-sm-3 control-label">'.__('Email', 'novusopress').'</label><div class="col-sm-9"><input id="email" '
                .'name="email" type="email" class="form-control" value="'.esc_attr($commenter['comment_author_email'])
                .'" aria-describedby="email-notes"'.$ariaReq.'></div></div>',
            'url'    => '<div class="form-group"><label for="url" class="col-sm-3 control-label">'.__('Website', 'novusopress')
                .'</label><div class="col-sm-9"><input id="url" name="url" type="url" class="form-control" value="'
                .esc_attr($commenter['comment_author_url']).'"></div></div>',
        ];

        $requiredText = sprintf(
            ' '.__('Required fields are marked %s', 'novusopress'),
            '<span class="required">*</span>'
        );

        $user = wp_get_current_user();
        $userIdentity = $user->exists() ? $user->display_name : '';

        $args = [
            'fields'               => $fields,
            'comment_field'        => '<div class="form-group"><label for="comment" class="required col-sm-3 control-label">'
                .__('Comment', 'novusopress').'</label><div class="col-sm-9"><textarea id="comment" name="comment" '
                .'cols="45" rows="8" class="form-control" aria-describedby="form-allowed-tags" aria-required="true">'
                .'</textarea></div></div>',
            'must_log_in'          => '<div class="row"><div class="col-sm-9 col-sm-offset-3">'
                .'<div class="alert alert-info" style="margin-bottom:0"><p class="must-log-in">'
                .sprintf(
                    __('You must be <a href="%s">logged in</a> to post a comment.', 'novusopress'),
                    wp_login_url(apply_filters('the_permalink', get_permalink()))
                )
                .'</p></div></div></div>',
            'logged_in_as'         => '<div class="form-group"><div class="col-sm-9 col-sm-offset-3">'
                .'<div class="alert alert-info" style="margin-bottom:0"><p class="logged-in-as">'
                .sprintf(
                    __('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'novusopress'),
                    get_edit_user_link(),
                    $userIdentity,
                    wp_logout_url(apply_filters('the_permalink', get_permalink()))
                )
                .'</p></div></div></div>',
            'comment_notes_before' => '<div class="form-group"><div class="col-sm-9 col-sm-offset-3">'
                .'<div class="alert alert-info" style="margin-bottom:0"><p class="comment-notes"><span id="email-notes">'
                .__('Your email address will not be published.', 'novusopress').'</span>'.($req ? $requiredText : '')
                .'</p></div></div></div>',
            'comment_notes_after'  => '',
            'id_form'              => 'commentform',
            'id_submit'            => 'submit',
            'class_submit'         => 'submit btn btn-primary pull-right',
            'name_submit'          => 'submit',
            'title_reply'          => __('Leave a Reply', 'novusopress'),
            'title_reply_to'       => __('Leave a Reply to %s', 'novusopress'),
            'cancel_reply_link'    => __('Cancel reply'),
            'label_submit'         => __('Post Comment')
        ];

        $output = [];

        ob_start();
        comment_form($args);
        $form = ob_get_clean();
        $form = str_replace('class="comment-form"', 'class="comment-form form-horizontal"', $form);
        $form = str_replace('<h3', '<div class="row"><div class="col-sm-9 col-sm-offset-3"><h3', $form);
        $form = str_replace('</h3>', '</h3></div></div>', $form);
        $form = str_replace('<p class="form-submit">', '<p class="form-submit clearfix">', $form);
        $form = str_replace(
            'id="cancel-comment-reply-link"',
            'id="cancel-comment-reply-link" class="btn btn-sm btn-default pull-right"',
            $form
        );

        $output[] = $form;

        $output = apply_filters('novusopress_comments_form_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_area_start')) {
    function novusopress_comments_area_start(array $args = [], $echo = true) {
        $defaults = [
            'containerEl'    => 'div',
            'containerId'    => 'comments',
            'containerClass' => 'comments-area',
            'indent'         => 0,
            'tab'            => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = sprintf(
            '%s<%s id="%s" class="%s">%s',
            $tabs,
            $containerEl,
            $containerId,
            $containerClass,
            PHP_EOL
        );

        $output = apply_filters('novusopress_comments_area_start_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}

if (!function_exists('novusopress_comments_area_end')) {
    function novusopress_comments_area_end(array $args = [], $echo = true) {
        $defaults = [
            'containerEl'    => 'div',
            'containerId'    => 'comments-area',
            'indent'         => 4,
            'tab'            => '    '
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tabs = str_repeat($tab, $indent);
        $output = [];

        $output[] = sprintf('%s</%s><!-- #%s -->', $tabs, $containerEl, $containerId);

        $output = apply_filters('novusopress_comments_area_end_output', implode('', $output));

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }
}
