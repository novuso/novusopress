<?php

namespace Novuso\WordPress\Theme\NovusoPress;

use WP_Customize_Manager;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * Customizer provides the theme customization
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Customizer
{
    /**
     * Registers theme customizations
     *
     * @param WP_Customize_Manager $customizeManager
     */
    public function register($customizeManager)
    {
        // clean document head
        $customizeManager->add_section('clean_document_head', [
            'title'       => __('Clean Document Head', 'novusopress'),
            'description' => __('Allows you to remove extra HTML tags from the document head', 'novusopress'),
            'capability'  => 'edit_theme_options',
            'priority'    => 900
        ]);
        $customizeManager->add_setting('novusopress_theme_options[clean_document_head]', [
            'default'    => true,
            'type'       => 'option',
            'capability' => 'edit_theme_options'
        ]);
        $customizeManager->add_control('novusopress_theme_options[clean_document_head]', [
            'section'  => 'clean_document_head',
            'settings' => 'novusopress_theme_options[clean_document_head]',
            'label'    => __('Remove extra HTML tags', 'novusopress'),
            'type'     => 'checkbox'
        ]);

        // display breadcrumbs
        $customizeManager->add_section('display_breadcrumbs', [
            'title'       => __('Breadcrumbs Navigation', 'novusopress'),
            'description' => __('Allows you to control the display of breadcrumbs navigation', 'novusopress'),
            'capability'  => 'edit_theme_options',
            'priority'    => 130
        ]);
        $customizeManager->add_setting('novusopress_theme_options[display_breadcrumbs]', [
            'default'    => true,
            'type'       => 'option',
            'capability' => 'edit_theme_options'
        ]);
        $customizeManager->add_control('novusopress_theme_options[display_breadcrumbs]', [
            'section'  => 'display_breadcrumbs',
            'settings' => 'novusopress_theme_options[display_breadcrumbs]',
            'label'    => __('Display breadcrumbs', 'novusopress'),
            'type'     => 'checkbox'
        ]);

        // navbar location
        $customizeManager->add_setting('novusopress_theme_options[navbar_location]', [
            'default'    => 'below',
            'type'       => 'option',
            'capability' => 'edit_theme_options'
        ]);
        $customizeManager->add_control('novusopress_theme_options[navbar_location]', [
            'section'  => 'nav',
            'settings' => 'novusopress_theme_options[navbar_location]',
            'label'    => __('Navbar Location', 'novusopress'),
            'type'     => 'select',
            'choices'  => [
                'below'  => __('Below Header', 'novusopress'),
                'fixed'  => __('Fixed Top', 'novusopress'),
                'static' => __('Static Top', 'novusopress')
            ]
        ]);

        // navbar color
        $customizeManager->add_setting('novusopress_theme_options[navbar_color]', [
            'default'    => 'default',
            'type'       => 'option',
            'capability' => 'edit_theme_options'
        ]);
        $customizeManager->add_control('novusopress_theme_options[navbar_color]', [
            'section'  => 'nav',
            'settings' => 'novusopress_theme_options[navbar_color]',
            'label'    => __('Navbar Color', 'novuopress'),
            'type'     => 'select',
            'choices'  => [
                'default' => __('Light', 'novusopress'),
                'inverse' => __('Dark', 'novusopress'),
                'primary' => __('Custom', 'novusopress')
            ]
        ]);

        // customize.js
        $customizeManager->get_setting('novusopress_theme_options[navbar_location]')->transport = 'postMessage';
        $customizeManager->get_setting('novusopress_theme_options[navbar_color]')->transport = 'postMessage';
    }
}
