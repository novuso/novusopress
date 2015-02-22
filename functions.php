<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

use Novuso\WordPress\Theme\NovusoPress\Framework;

// Composer install is required at the project or theme level
if (!class_exists('Novuso\\WordPress\\Theme\\NovusoPress\\Framework')) {
    if (!file_exists(__DIR__.'/vendor/autoload.php')) {
        throw new RuntimeException('Composer install required');
    }
    require __DIR__.'/vendor/autoload.php';
}

// include helper functions
require __DIR__.'/core/comments.php';
require __DIR__.'/core/searchform.php';
require __DIR__.'/core/formatting.php';

Framework::instance()->init();

Framework::instance()->getViewMeta()->addElement([
    'name'    => 'viewport',
    'content' => 'width=device-width, initial-scale=1'
]);
