<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use Closure;
use Iterator;
use WP_Post;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * PostIterator is an iterator for the WordPress loop
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class PostIterator implements Iterator
{
    /**
     * Valid callback
     *
     * @var Closure
     */
    protected $valid;

    /**
     * Current callback
     *
     * @var Closure
     */
    protected $current;

    /**
     * Rewind callback
     *
     * @var Closure
     */
    protected $rewind;

    /**
     * Constructs PostIterator
     */
    public function __construct()
    {
        $this->valid = function () {
            return have_posts();
        };

        $this->current = function () {
            global $post;
            the_post();

            return $post;
        };

        $this->rewind = function () {
            rewind_posts();
        };
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return call_user_func($this->valid);
    }

    /**
     * Prepares the current post
     *
     * @return WP_Post
     */
    public function current()
    {
        return call_user_func($this->current);
    }

    /**
     * Rewinds the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        call_user_func($this->rewind);
    }

    /**
     * Not implemented
     *
     * @return void
     */
    public function next()
    {
    }

    /**
     * Not implemented
     *
     * @return void
     */
    public function key()
    {
    }
}
