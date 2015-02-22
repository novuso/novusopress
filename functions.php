<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

use Novuso\WordPress\Theme\NovusoPress\Framework;

$baseDir = get_template_directory();
$childDir = get_stylesheet_directory();

// Composer install is required at the project or theme level
if (!class_exists('Novuso\\WordPress\\Theme\\NovusoPress\\Framework')) {
    if (file_exists($baseDir.'/vendor/autoload.php')) {
        require $baseDir.'/vendor/autoload.php';
    } elseif (file_exists($childDir.'/vendor/autoload.php')) {
        require $childDir.'/vendor/autoload.php';
    } else {
        throw new RuntimeException('Composer install required');
    }
}

// include helper functions
require $baseDir.'/core/comments.php';
require $baseDir.'/core/searchform.php';
require $baseDir.'/core/formatting.php';

Framework::instance()->init();

Framework::instance()->getViewMeta()->addElement([
    'name'    => 'viewport',
    'content' => 'width=device-width, initial-scale=1'
]);
