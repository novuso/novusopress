<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Framework provides the core functionality for NovusoPress
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Framework
{
    /**
     * Framework version
     *
     * @var string
     */
    const VERSION = '0.1.2';

    /**
     * Minimum WordPress version
     *
     * @var string
     */
    const MIN_WP_VER = '4.1';

    /**
     * Framework instance
     *
     * @var Framework
     */
    protected static $instance;

    /**
     * Search form count
     *
     * @var integer
     */
    protected static $sfcount;

    /**
     * View Manager
     *
     * @var ViewManager
     */
    protected $viewManager;

    /**
     * Theme customizer
     *
     * @var Customizer
     */
    protected $customizer;

    /**
     * Theme
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Assets
     *
     * @var Assets
     */
    protected $assets;

    /**
     * Theme paths
     *
     * @var array
     */
    protected $paths;

    /**
     * Constructs Framework
     */
    public function __construct()
    {
        $this->paths = Paths::create();
        $this->viewManager = new ViewManager($this->paths);
        $this->theme = new Theme($this->paths);
        $this->assets = new Assets($this->paths, $this->theme->getThemeInfo('version'));
        $this->viewMeta = new ViewMeta();
        $this->customizer = new Customizer();
    }

    /**
     * Retrieves Framework instance
     *
     * @return Framework
     */
    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Increments and returns the search form count
     *
     * @return integer
     */
    public static function searchFormCount()
    {
        if (null === static::$sfcount) {
            static::$sfcount = 1;
        } else {
            static::$sfcount++;
        }

        return static::$sfcount;
    }

    /**
     * Initializes the framework
     */
    public function init()
    {
        // Requires WordPress 4.1
        if (version_compare($GLOBALS['wp_version'], self::MIN_WP_VER, '<')) {
            Compat::loadNotices();
        }

        // core actions
        add_action('after_setup_theme', [$this->theme, 'addThemeSupports']);
        add_action('widgets_init', [$this->theme, 'registerWidgets']);
        add_action('wp_enqueue_scripts', [$this->assets, 'loadSiteScripts']);
        add_action('wp_enqueue_scripts', [$this->assets, 'loadSiteStyles']);
        add_action('admin_enqueue_scripts', [$this->assets, 'loadAdminScripts']);
        add_action('admin_enqueue_scripts', [$this->assets, 'loadAdminStyles']);
        add_action('login_enqueue_scripts', [$this->assets, 'loadLoginScripts']);
        add_action('login_enqueue_scripts', [$this->assets, 'loadLoginStyles']);

        // meta elements
        add_action('wp_head', [$this->viewMeta, 'render'], 1);

        // theme customizations
        add_action('customize_register', [$this->customizer, 'register']);
        add_action('customize_preview_init', [$this->assets, 'loadCustomizeScript']);
    }

    /**
     * Retrieves the View Manager
     *
     * @return ViewManager
     */
    public function getViewManager()
    {
        return $this->viewManager;
    }

    /**
     * Retrieves the View Meta
     *
     * @return ViewMeta
     */
    public function getViewMeta()
    {
        return $this->viewMeta;
    }

    /**
     * Retrieves theme information
     *
     * Returns the field value or false on failure.
     *
     * @param string $key The theme info key
     *
     * @return string|false
     */
    public function getThemeInfo($key)
    {
        return $this->theme->getThemeInfo($key);
    }

    /**
     * Retrieves theme option
     *
     * @param string $key     The theme option key
     * @param mixed  $default A default value to return if the option is undefined
     *
     * @return mixed
     */
    public function getThemeOption($key, $default = null)
    {
        return $this->theme->getThemeOption($key, $default);
    }

    /**
     * Registers a widget area
     *
     * @param string $name     The display name
     * @param string $location The area location
     * @param string $id       The unique identifier
     *
     * @return $this
     */
    public function registerWidget($name, $location, $id)
    {
        $this->theme->registerWidget($name, $location, $id);

        return $this;
    }

    /**
     * Loads a JavaScript file
     *
     * @param string  $handle The script handle
     * @param string  $url    The script URL
     * @param array   $deps   An array of dependencies
     * @param boolean $footer Whether or not the file can be loaded at the bottom of the page
     *
     * @return $this
     */
    public function loadScript($handle, $url, array $deps = [], $footer = true)
    {
        $this->assets->loadScript($handle, $url, $deps, $footer);

        return $this;
    }

    /**
     * Loads a CSS file
     *
     * @param string  $handle The stylesheet handle
     * @param string  $url    The stylesheet URL
     * @param array   $deps   An array of dependencies
     * @param string  $media  The media type for this stylesheet
     *
     * @return $this
     */
    public function loadStyle($handle, $url, array $deps = [], $media = 'all')
    {
        $this->assets->loadStyle($handle, $url, $deps, $media);

        return $this;
    }

    /**
     * Retrieves the path to the base root directory
     *
     * @return string
     */
    public function getBaseRootDir()
    {
        return $this->paths->getBaseRootDir();
    }

    /**
     * Retrieves the URI to the base root directory
     *
     * @return string
     */
    public function getBaseRootUri()
    {
        return $this->paths->getBaseRootUri();
    }

    /**
     * Retrieves the path to the base assets directory
     *
     * @return string
     */
    public function getBaseAssetsDir()
    {
        return $this->paths->getBaseAssetsDir();
    }

    /**
     * Retrieves the URI to the base assets directory
     *
     * @return string
     */
    public function getBaseAssetsUri()
    {
        return $this->paths->getBaseAssetsUri();
    }

    /**
     * Retrieves the path to the base config directory
     *
     * @return string
     */
    public function getBaseConfigDir()
    {
        return $this->paths->getBaseConfigDir();
    }

    /**
     * Retrieves the path to the base language directory
     *
     * @return string
     */
    public function getBaseLanguageDir()
    {
        return $this->paths->getBaseLanguageDir();
    }

    /**
     * Retrieves the path to the base views directory
     *
     * @return string
     */
    public function getBaseViewsDir()
    {
        return $this->paths->getBaseViewsDir();
    }

    /**
     * Retrieves the path to the theme root directory
     *
     * @return string
     */
    public function getThemeRootDir()
    {
        return $this->paths->getThemeRootDir();
    }

    /**
     * Retrieves the URI to the theme root directory
     *
     * @return string
     */
    public function getThemeRootUri()
    {
        return $this->paths->getThemeRootUri();
    }

    /**
     * Retrieves the path to the theme assets directory
     *
     * @return string
     */
    public function getThemeAssetsDir()
    {
        return $this->paths->getThemeAssetsDir();
    }

    /**
     * Retrieves the URI to the theme assets directory
     *
     * @return string
     */
    public function getThemeAssetsUri()
    {
        return $this->paths->getThemeAssetsUri();
    }

    /**
     * Retrieves the path to the theme config directory
     *
     * @return string
     */
    public function getThemeConfigDir()
    {
        return $this->paths->getThemeConfigDir();
    }

    /**
     * Retrieves the path to the theme language directory
     *
     * @return string
     */
    public function getThemeLanguageDir()
    {
        return $this->paths->getThemeLanguageDir();
    }

    /**
     * Retrieves the path to the theme views directory
     *
     * @return string
     */
    public function getThemeViewsDir()
    {
        return $this->paths->getThemeViewsDir();
    }
}
