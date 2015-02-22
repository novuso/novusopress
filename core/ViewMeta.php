<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * ViewMeta provides methods for adding meta elements to the document
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ViewMeta
{
    protected $meta = [];

    /**
     * Adds a meta element
     *
     * @param array $attrs An associated array of attributes
     *
     * @return ViewMeta
     */
    public function addElement(array $attrs = [])
    {
        $this->meta[] = $attrs;

        return $this;
    }

    /**
     * Prints meta output
     */
    public function render()
    {
        $output = [];

        foreach ($this->meta as $element) {
            $output[] = '<meta';
            foreach ($element as $key => $value) {
                $output[] = sprintf(' %s="%s"', $key, $value);
            }
            $output[] = ">\n";
        }

        $output = apply_filters('novusopress_meta_output', implode('', $output));

        echo $output;
    }
}
