<?php if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * This file is part of NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */

use Novuso\WordPress\Theme\NovusoPress\Framework;

$viewManager = Framework::instance()->getViewManager();

if ('page' === get_option('show_on_front')) {
    $template = 'front-page';
} else {
    $template = 'index';
}

echo $viewManager->render($template);
