<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Paths provides the theme directory paths
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Paths
{
    /**
     * Theme paths
     *
     * @var array
     */
    protected $paths;

    /**
     * Constructs Paths
     *
     * @param string $baseDir  The path to the parent theme directory
     * @param string $baseUri  The URI to the parent theme directory
     * @param string $themeDir The path to the child theme directory
     * @param string $themeUri The URI to the child theme directory
     */
    public function __construct($baseDir, $baseUri, $themeDir, $themeUri)
    {
        $this->paths = [
            'base_root_dir'    => $baseDir,
            'base_root_uri'    => $baseUri,
            'base_assets_dir'  => sprintf('%s/%s', $baseDir, 'assets'),
            'base_assets_uri'  => sprintf('%s/%s', $baseUri, 'assets'),
            'base_config_dir'  => sprintf('%s/%s', $baseDir, 'config'),
            'base_lang_dir'    => sprintf('%s/%s', $baseDir, 'languages'),
            'base_views_dir'   => sprintf('%s/%s', $baseDir, 'templates'),
            'theme_root_dir'   => $themeDir,
            'theme_root_uri'   => $themeUri,
            'theme_assets_dir' => sprintf('%s/%s', $themeDir, 'assets'),
            'theme_assets_uri' => sprintf('%s/%s', $themeUri, 'assets'),
            'theme_config_dir' => sprintf('%s/%s', $themeDir, 'config'),
            'theme_lang_dir'   => sprintf('%s/%s', $themeDir, 'languages'),
            'theme_views_dir'  => sprintf('%s/%s', $themeDir, 'templates')
        ];
    }

    /**
     * Creates a Paths instance
     *
     * @return Paths
     */
    public static function create()
    {
        return new static(
            get_template_directory(),
            get_template_directory_uri(),
            get_stylesheet_directory(),
            get_stylesheet_directory_uri()
        );
    }

    /**
     * Retrieves the path to the base root directory
     *
     * @return string
     */
    public function getBaseRootDir()
    {
        return $this->paths['base_root_dir'];
    }

    /**
     * Retrieves the URI to the base root directory
     *
     * @return string
     */
    public function getBaseRootUri()
    {
        return $this->paths['base_root_uri'];
    }

    /**
     * Retrieves the path to the base assets directory
     *
     * @return string
     */
    public function getBaseAssetsDir()
    {
        return $this->paths['base_assets_dir'];
    }

    /**
     * Retrieves the URI to the base assets directory
     *
     * @return string
     */
    public function getBaseAssetsUri()
    {
        return $this->paths['base_assets_uri'];
    }

    /**
     * Retrieves the path to the base config directory
     *
     * @return string
     */
    public function getBaseConfigDir()
    {
        return $this->paths['base_config_dir'];
    }

    /**
     * Retrieves the path to the base language directory
     *
     * @return string
     */
    public function getBaseLanguageDir()
    {
        return $this->paths['base_lang_dir'];
    }

    /**
     * Retrieves the path to the base views directory
     *
     * @return string
     */
    public function getBaseViewsDir()
    {
        return $this->paths['base_views_dir'];
    }

    /**
     * Retrieves the path to the theme root directory
     *
     * @return string
     */
    public function getThemeRootDir()
    {
        return $this->paths['theme_root_dir'];
    }

    /**
     * Retrieves the URI to the theme root directory
     *
     * @return string
     */
    public function getThemeRootUri()
    {
        return $this->paths['theme_root_uri'];
    }

    /**
     * Retrieves the path to the theme assets directory
     *
     * @return string
     */
    public function getThemeAssetsDir()
    {
        return $this->paths['theme_assets_dir'];
    }

    /**
     * Retrieves the URI to the theme assets directory
     *
     * @return string
     */
    public function getThemeAssetsUri()
    {
        return $this->paths['theme_assets_uri'];
    }

    /**
     * Retrieves the path to the theme config directory
     *
     * @return string
     */
    public function getThemeConfigDir()
    {
        return $this->paths['theme_config_dir'];
    }

    /**
     * Retrieves the path to the theme language directory
     *
     * @return string
     */
    public function getThemeLanguageDir()
    {
        return $this->paths['theme_lang_dir'];
    }

    /**
     * Retrieves the path to the theme views directory
     *
     * @return string
     */
    public function getThemeViewsDir()
    {
        return $this->paths['theme_views_dir'];
    }
}
