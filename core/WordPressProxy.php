<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use BadMethodCallException;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * WordPressProxy proxies WordPress function calls within Twig
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class WordPressProxy
{
    /**
     * Set of allowed methods
     *
     * @var array
     */
    protected $allowed;

    /**
     * Constructs WordPressProxy
     */
    public function __construct()
    {
        $framework = Framework::instance();
        $proxyFunctions = file_get_contents(sprintf('%s/%s', $framework->getBaseConfigDir(), 'proxy_functions.json'));
        $this->allowed = json_decode($proxyFunctions, true);
    }

    /**
     * Magic method calls
     *
     * @param string $name      The method name
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->allowed[$name])) {
            throw new BadMethodCallException(sprintf('Invalid method call: %s', $name));
        }

        return call_user_func_array($name, $arguments);
    }
}
