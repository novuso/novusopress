<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use Walker_Nav_Menu;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * NavbarWalker is the menu item walker for a navigation bar
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class NavbarWalker extends Walker_Nav_Menu
{
    /**
     * The initial indent level
     *
     * @var integer
     */
    protected $indent;

    /**
     * Constructs NavbarWalker
     *
     * @param integer $indent The initial indent level
     */
    public function __construct($indent)
    {
        $this->indent = (integer) $indent;
    }

    /**
     * Starts the list before the elements are added
     *
     * @param string  $output Passed by reference. Used to append additional content.
     * @param integer $depth  Depth of menu item. Used for padding.
     * @param array   $args   An array of arguments.
     *
     * @see \Walker::start_lvl()
     */
    public function start_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = str_repeat('    ', $this->indent + $depth + 1);
        $output .= sprintf('%s%s<ul class="dropdown-menu" role="menu">%s', PHP_EOL, $indent, PHP_EOL);
    }

    /**
     * Ends the list of after the elements are added
     *
     * @param string  $output Passed by reference. Used to append additional content.
     * @param integer $depth  Depth of menu item. Used for padding.
     * @param array   $args   An array of arguments.
     *
     * @see \Walker::end_lvl()
     */
    public function end_lvl(&$output, $depth = 0, $args = [])
    {
        $indent = str_repeat('    ', $this->indent + $depth + 1);
        $output .= sprintf('%s</ul>%s', $indent, PHP_EOL);
    }

    /**
     * Start the element output
     *
     * @param string  $output Passed by reference. Used to append additional content.
     * @param object  $item   Menu item data object.
     * @param integer $depth  Depth of menu item. Used for padding.
     * @param array   $args   An array of arguments.
     * @param integer $id     Current item ID.
     *
     * @see \Walker::start_el()
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $indent = ($depth)
            ? str_repeat('    ', $this->indent + $depth + 1)
            : str_repeat('    ', $this->indent + $depth);

        $class_names = '';
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = sprintf('menu-item-%s', $item->ID);

        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));

        if ($args->has_children) {
            $class_names .= ' dropdown';
        }

        if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
            $class_names .= ' active';
        }

        $class_names = $class_names ? sprintf(' class="%s"', esc_attr($class_names)) : '';

        $id = apply_filters('nav_menu_item_id', sprintf('menu-item-%s', $item->ID), $item, $args);
        $id = $id ? sprintf(' id="%s"', esc_attr($id)) : '';

        $output .= sprintf('%s<li%s%s>', $indent, $id, $class_names);

        $atts = [];
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';

        if ($args->has_children && $depth === 0) {
            $atts['data-toggle'] = 'dropdown';
            $atts['class'] = 'dropdown-toggle';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);
        $attributes = [];

        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes[] = sprintf(' %s="%s"', $attr, $value);
            }
        }

        $item_output = [];
        $item_output[] = $args->before;
        $item_output[] = sprintf('<a%s>', implode('', $attributes));
        $item_output[] = $args->link_before;
        $item_output[] = apply_filters('the_title', $item->title, $item->ID);
        $item_output[] = $args->link_after;
        $item_output[] = ($args->has_children && 0 === $depth) ? ' <span class="caret"></span></a>' : '</a>';
        $item_output[] = $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', implode('', $item_output), $item, $depth, $args);
    }

    /**
     * Ends the element output, if needed
     *
     * @param string  $output Passed by reference. Used to append additional content.
     * @param object  $item   Page data object. Not used.
     * @param integer $depth  Depth of page. Not Used.
     * @param array   $args   An array of arguments.
     *
     * @see \Walker::end_el()
     */
    public function end_el(&$output, $item, $depth = 0, $args = [])
    {
        if ($depth === 0 && in_array('menu-item-has-children', $item->classes)) {
            $indent = str_repeat('    ', $this->indent + $depth);
            $output .= sprintf('%s</li>%s', $indent, PHP_EOL);
        } else {
            $output .= sprintf('</li>%s', PHP_EOL);
        }
    }

    /**
     * Traverse elements to create list from elements
     *
     * Display one element if the element doesn't have any children otherwise,
     * display the element and its children. Will only traverse up to the max
     * depth and no ignore elements under that depth.
     *
     * This method shouldn't be called directly, use the walk() method instead.
     *
     * @param object  $element           Data object.
     * @param array   $children_elements List of elements to continue traversing.
     * @param integer $max_depth         Max depth to traverse.
     * @param integer $depth             Depth of current element.
     * @param array   $args              An array of arguments.
     * @param string  $output            Passed by reference. Used to append additional content.
     *
     * @return null
     */
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        if (!$element) {
            return;
        }

        $id_field = $this->db_fields['id'];

        if (is_object($args[0])) {
           $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }

        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
}
