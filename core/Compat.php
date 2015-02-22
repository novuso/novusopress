<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Compat provides upgrade notices for unsupported WordPress versions
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Compat
{
    /**
     * Loads requirements notices
     */
    public static function loadNotices()
    {
        $compat = new static();
        add_action('after_switch_theme', [$compat, 'switchTheme']);
        add_action('load-customize.php', [$compat, 'customize']);
        add_action('template_redirect', [$compat, 'preview']);
    }

    /**
     * Handles switch theme event
     */
    public function switchTheme()
    {
        switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
        unset($_GET['activated']);
        add_action('admin_notices', [$this, 'upgradeNotice']);
    }

    /**
     * Handles customize event
     */
    public function customize()
    {
        wp_die($this->getErrorMessage(), '', ['back_link' => true]);
    }

    /**
     * Handles preview event
     */
    public function preview()
    {
        if (isset($_GET['preview'])) {
            wp_die($this->getErrorMessage());
        }
    }

    /**
     * Prints the upgrade notice
     */
    public function upgradeNotice()
    {
        printf('<div class="error"><p>%s</p></div>', $this->getErrorMessage());
    }

    /**
     * Retrieves the error message for unsupported WordPress versions
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return sprintf(__(
            'NovusoPress requires WordPress version %s. You are running version %s. Please upgrade and try again.',
            'novusopress'
        ), Framework::MIN_WP_VER, $GLOBALS['wp_version']);
    }
}
