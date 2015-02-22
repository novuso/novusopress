<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Assets loads the assets for the current theme template
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Assets
{
    /**
     * Theme paths
     *
     * @var Paths
     */
    protected $paths;

    /**
     * Theme version
     *
     * @var string
     */
    protected $version;

    /**
     * Dependencies config
     *
     * @var array
     */
    protected $dependencies;

    /**
     * Constructs Assets
     *
     * @param Paths  $paths   The theme paths
     * @param string $version The theme version
     */
    public function __construct(Paths $paths, $version)
    {
        $this->paths = $paths;
        $this->version = $version;
    }

    /**
     * Loads a JavaScript file
     *
     * @param string  $handle The script handle
     * @param string  $url    The script URL
     * @param array   $deps   An array of dependencies
     * @param boolean $footer Whether or not the file can be loaded at the bottom of the page
     */
    public function loadScript($handle, $url, array $deps = [], $footer = true)
    {
        wp_register_script($handle, $url, $deps, $this->version, $footer);
        wp_enqueue_script($handle);
    }

    /**
     * Loads a CSS file
     *
     * @param string  $handle The stylesheet handle
     * @param string  $url    The stylesheet URL
     * @param array   $deps   An array of dependencies
     * @param string  $media  The media type for this stylesheet
     */
    public function loadStyle($handle, $url, array $deps = [], $media = 'all')
    {
        wp_register_style($handle, $url, $deps, $this->version, $media);
        wp_enqueue_style($handle);
    }

    /**
     * Loads frontend scripts
     */
    public function loadSiteScripts()
    {
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        $mainHandle = 'site';
        $this->loadMainScriptByHandle($mainHandle);

        $viewHandle = $this->getViewHandle();

        if (file_exists(sprintf('%s/js/views/%s.js', $this->paths->getThemeAssetsDir(), $viewHandle))) {
            $viewSrc = sprintf('%s/js/views/%s.js', $this->paths->getThemeAssetsUri(), $viewHandle);
            $this->loadScript($viewHandle, $viewSrc, [$mainHandle], true);
        }
    }

    /**
     * Loads frontend styles
     */
    public function loadSiteStyles()
    {
        $this->loadCdnStyles();

        $mainHandle = 'site';
        $this->loadMainStyleByHandle($mainHandle);

        $viewHandle = $this->getViewHandle();

        if (file_exists(sprintf('%s/css/views/%s.css', $this->paths->getThemeAssetsDir(), $viewHandle))) {
            $viewSrc = sprintf('%s/css/views/%s.css', $this->paths->getThemeAssetsUri(), $viewHandle);
            $this->loadStyle($viewHandle, $viewSrc, [$mainHandle]);
        }
    }

    /**
     * Loads admin scripts
     */
    public function loadAdminScripts()
    {
        $this->loadMainScriptByHandle('admin');
    }

    /**
     * Loads admin styles
     */
    public function loadAdminStyles()
    {
        $this->loadMainStyleByHandle('admin');
    }

    /**
     * Loads login page scripts
     */
    public function loadLoginScripts()
    {
        $this->loadMainScriptByHandle('login');
    }

    /**
     * Loads login page styles
     */
    public function loadLoginStyles()
    {
        $this->loadMainStyleByHandle('login');
    }

    /**
     * Loads customize page script
     */
    public function loadCustomizeScript()
    {
        $handle = 'novusopress_customize';
        $url = sprintf('%s/js/customize.js', $this->paths->getBaseAssetsUri());
        $deps = ['jquery', 'customize-preview'];
        $this->loadScript($handle, $url, $deps, true);
    }

    /**
     * Loads stylesheets from CDNs such as Google fonts
     */
    protected function loadCdnStyles()
    {
        if (file_exists(sprintf('%s/cdn-styles.json', $this->paths->getThemeConfigDir()))) {
            $cdnContent = file_get_contents(sprintf('%s/cdn-styles.json', $this->paths->getThemeConfigDir()));
            $cdnStyles = json_decode($cdnContent, true);
            for ($i = 0; $i < count($cdnStyles); $i++) {
                $handle = $i ? 'cdn'.$i : 'cdn';
                wp_enqueue_style($handle, $cdnStyles[$i], [], null);
            }
        }
    }

    /**
     * Loads a main script file using handle conventions
     *
     * @param string $mainHandle The script handle
     */
    protected function loadMainScriptByHandle($mainHandle)
    {
        $deps = $this->getScriptDeps($mainHandle);

        if (file_exists(sprintf('%s/js/%s.js', $this->paths->getThemeAssetsDir(), $mainHandle))) {
            $mainSrc = sprintf('%s/js/%s.js', $this->paths->getThemeAssetsUri(), $mainHandle);
        } else {
            // load empty script in case we need to load dependencies
            $mainSrc = sprintf('%s/js/%s.js', $this->paths->getBaseAssetsUri(), $mainHandle);
        }

        $this->loadScript($mainHandle, $mainSrc, $deps, true);
    }

    /**
     * Loads a main stylesheet file using handle conventions
     *
     * @param string $mainHandle The style handle
     */
    protected function loadMainStyleByHandle($mainHandle)
    {
        $deps = $this->getStyleDeps($mainHandle);

        if (file_exists(sprintf('%s/css/%s.css', $this->paths->getThemeAssetsDir(), $mainHandle))) {
            $mainSrc = sprintf('%s/css/%s.css', $this->paths->getThemeAssetsUri(), $mainHandle);
        } else {
            // load styles in case we need to load dependencies
            $mainSrc = sprintf('%s/css/%s.css', $this->paths->getBaseAssetsUri(), $mainHandle);
        }

        $this->loadStyle($mainHandle, $mainSrc, $deps);
    }

    /**
     * Retrieves script dependencies
     *
     * @param string $section The section of the site
     *
     * @return array
     */
    protected function getScriptDeps($section)
    {
        $dependencies = $this->getDependencies();
        $deps = isset($dependencies['scripts']) ? $dependencies['scripts'] : [];

        return isset($deps[$section]) ? $deps[$section] : [];
    }

    /**
     * Retrieves style dependencies
     *
     * @param string $section The section of the site
     *
     * @return array
     */
    protected function getStyleDeps($section)
    {
        $dependencies = $this->getDependencies();
        $deps = isset($dependencies['styles']) ? $dependencies['styles'] : [];

        return isset($deps[$section]) ? $deps[$section] : [];
    }

    /**
     * Retrieves the dependencies config
     *
     * @return array
     */
    protected function getDependencies()
    {
        if (null === $this->dependencies) {
            if (file_exists(sprintf('%s/dependencies.json', $this->paths->getThemeConfigDir()))) {
                $depsContent = file_get_contents(sprintf('%s/dependencies.json', $this->paths->getThemeConfigDir()));
            } else {
                $depsContent = file_get_contents(sprintf('%s/dependencies.json', $this->paths->getBaseConfigDir()));
            }
            $this->dependencies = json_decode($depsContent, true);
        }

        return $this->dependencies;
    }

    /**
     * Retrieves the current view handle
     *
     * @return string
     */
    protected function getViewHandle()
    {
        if (is_front_page() && is_home()) {
            return 'index';
        } elseif (is_front_page()) {
            return 'front-page';
        } elseif (is_home()) {
            return 'index';
        } elseif (is_page_template()) {
            global $wp_query;
            $templateName = get_post_meta($wp_query->post->ID, '_wp_page_template', true);

            return substr($templateName, 0, -4);
        } elseif (is_page()) {
            return 'page';
        } elseif (is_attachment()) {
            return 'attacment';
        } elseif (is_single()) {
            return 'single';
        } elseif (is_archive()) {
            return 'archive';
        } elseif (is_search()) {
            return 'search';
        }

        return 'not-found';
    }
}
