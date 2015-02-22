<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use WP_Theme;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Theme provides theme support, info, and options
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Theme
{
    /**
     * Theme info
     *
     * @var WP_Theme
     */
    protected $theme;

    /**
     * Theme paths
     *
     * @var Paths
     */
    protected $paths;

    /**
     * Constructs Theme
     *
     * @param Paths $paths The theme paths
     */
    public function __construct(Paths $paths)
    {
        $this->paths = $paths;
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
        if (null === $this->theme) {
            $this->theme = wp_get_theme();
        }

        $headers = [
            'name'        => 'Name',
            'description' => 'Description',
            'author'      => 'Author',
            'authoruri'   => 'AuthorURI',
            'themeuri'    => 'ThemeURI',
            'version'     => 'Version',
            'status'      => 'Status',
            'tags'        => 'Tags',
            'textdomain'  => 'TextDomain'
        ];

        $key = strtolower($key);

        if (isset($headers[$key])) {
            return $this->theme->get($headers[$key]);
        }

        return false;
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
        $options = get_option('novusopress_theme_options') ?: [];

        if (array_key_exists($key, $options)) {
            return apply_filters(sprintf('novusopress_theme_options_%s', $key), $options[$key]);
        }

        $defaults = $this->getThemeDefaultOptions();

        if (array_key_exists($key, $defaults)) {
            return apply_filters(sprintf('novusopress_theme_options_%s', $key), $defaults[$key]);
        }

        return $default;
    }

    /**
     * Enables theme supports
     */
    public function addThemeSupports()
    {
        load_theme_textdomain('novusopress', $this->paths->getBaseLanguageDir());

        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

        register_nav_menus(['main-menu' => __('Main Menu', 'novusopress')]);

        if ($this->getThemeOption('clean_document_head')) {
            $this->cleanDocumentHead();
        }

        if (file_exists(sprintf('%s/css/editor-style.css', $this->paths->getThemeAssetsDir()))) {
            add_editor_style('assets/css/editor-style.css');
        }
    }

    /**
     * Enables default widget support
     */
    public function registerWidgets()
    {
        $this->registerWidget(__('Header Widget Area', 'novusopress'), 'header', 'widgets');
        $this->registerWidget(__('Blog Index Sidebar', 'novusopress'), 'sidebar', 'index');
        $this->registerWidget(__('Blog Post Sidebar', 'novusopress'), 'sidebar', 'single');
        $this->registerWidget(__('Front Page Sidebar', 'novusopress'), 'sidebar', 'front-page');
        $this->registerWidget(__('Standard Page Sidebar', 'novusopress'), 'sidebar', 'page');
        $this->registerWidget(__('Archive List Sidebar', 'novusopress'), 'sidebar', 'archive');
        $this->registerWidget(__('Search Results Sidebar', 'novusopress'), 'sidebar', 'search');
        $this->registerWidget(__('Attachment View Sidebar', 'novusopress'), 'sidebar', 'attachment');
        $this->registerWidget(__('404 Error Sidebar', 'novusopress'), 'sidebar', 'not-found');
        $this->registerWidget(__('Footer Area One', 'novusopress'), 'footer', 'one');
        $this->registerWidget(__('Footer Area Two', 'novusopress'), 'footer', 'two');
        $this->registerWidget(__('Footer Area Three', 'novusopress'), 'footer', 'three');
        $this->registerWidget(__('Footer Area Four', 'novusopress'), 'footer', 'four');
    }

    /**
     * Registers a widget area
     *
     * @param string $name     The display name
     * @param string $location The area location
     * @param string $id       The unique identifier
     */
    public function registerWidget($name, $location, $id)
    {
        $tab = '    ';
        $indent = str_repeat($tab, 5);

        register_sidebar([
            'name'          => $name,
            'id'            => sprintf('%s-%s', $location, $id),
            'class'         => sprintf('%s-%s', $location, $id),
            'before_widget' => sprintf('<div id="%%1$s" class="%s-widget %%2$s">', $location),
            'after_widget'  => sprintf('</div><!-- .%s-widget -->%s', $location, PHP_EOL),
            'before_title'  => sprintf('<h3 class="%s-widget-title">', $location),
            'after_title'   => '</h3>'
        ]);
    }

    /**
     * Removes extra markup from the document head
     */
    public function cleanDocumentHead()
    {
        remove_action('wp_head', 'feed_links_extra');
        remove_action('wp_head', 'feed_links');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
    }

    /**
     * Retrieves the default theme options
     *
     * @return array
     */
    protected function getThemeDefaultOptions()
    {
        return [
            'clean_document_head' => true,
            'display_breadcrumbs' => true,
            'navbar_location'     => 'below',
            'navbar_color'        => 'default'
        ];
    }
}
