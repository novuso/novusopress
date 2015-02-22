<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use Walker_Comment;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * NavbarWalker is the menu item walker for a navigation bar
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class CommentWalker extends Walker_Comment
{
    /**
     * Start the list before the elements are added.
     *
     * @param string  $output Passed by reference; used to append additional content
     * @param integer $depth  Depth of comment
     * @param array   $args   Uses 'style' argument for type of HTML list
     */
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $GLOBALS['comment_depth'] = $depth + 1;

        switch ($args['style']) {
            case 'div':
                break;
            case 'ol':
                $output .= '<ol class="children list-unstyled">'.PHP_EOL;
                break;
            case 'ul':
            default:
                $output .= '<ul class="children list-unstyled">'.PHP_EOL;
                break;
        }
    }

    /**
     * End the list of items after the elements are added.
     *
     * @param string  $output Passed by reference; used to append additional content
     * @param integer $depth  Depth of comment
     * @param array   $args   Will only append content if style argument value is 'ol' or 'ul'
     */
    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        $tab = '    ';
        $indent = ($depth == 1) ? 8 + $depth : 7 + ($depth * 2);

        switch ($args['style']) {
            case 'div':
                break;
            case 'ol':
            case 'ul':
            default:
                $output .= str_repeat($tab, $indent);
                break;
        }

        parent::end_lvl($output, $depth, $args);
    }
}
