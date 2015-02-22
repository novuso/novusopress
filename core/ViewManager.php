<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * ViewManager renders the current theme template
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ViewManager
{
    /**
     * Cache directory
     *
     * @var string
     */
    const CACHE_DIR = '/tmp/novusopress';

    /**
     * Theme paths
     *
     * @var Paths
     */
    protected $paths;

    /**
     * Constructs ViewManager
     *
     * @param Paths $paths The theme paths
     */
    public function __construct(Paths $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Renders a view
     *
     * @param string $template The template name
     *
     * @return string
     */
    public function render($template)
    {
        $debug = defined('WP_DEBUG') ? WP_DEBUG : false;

        if (is_writable('/tmp')) {
            if (!is_dir(self::CACHE_DIR)) {
                mkdir(self::CACHE_DIR);
            }
            $cache = self::CACHE_DIR;
        } else {
            $cache = null;
        }

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem([
            $this->paths->getThemeViewsDir(),
            $this->paths->getBaseViewsDir()
        ]);

        $twig = new Twig_Environment($loader, [
            'auto_reload'      => $debug,
            'cache'            => $cache,
            'charset'          => 'UTF-8',
            'debug'            => $debug,
            'strict_variables' => $debug
        ]);

        $view = $twig->loadTemplate(sprintf('%s.html.twig', $template));

        return $view->render([
            'view'  => new ViewModel($template),
            'posts' => new PostIterator(),
            'wp'    => new WordPressProxy()
        ]);
    }
}
