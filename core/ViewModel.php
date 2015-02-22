<?php

namespace Novuso\WordPress\Theme\NovusoPress;

if (!defined('ABSPATH')) exit('direct script access is not allowed');
/**
 * ViewModel provides methods for rendering view data in the template
 *
 * @author    John Nickell
 * @copyright Copyright (c) 2015, Novuso. (http://novuso.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class ViewModel
{
    /**
     * Template name
     *
     * @var string
     */
    protected $template;

    /**
     * Constructs ViewModel
     *
     * @param string $template The template name
     */
    public function __construct($template)
    {
        $this->template = (string) $template;
    }

    /**
     * Retrieves the name of the template
     *
     * @return string
     */
    public function template()
    {
        $output = $this->template;

        return apply_filters('novusopress_view_template_output', $output);
    }

    /**
     * Retrieves the content for posts
     *
     * @return string
     */
    public function postContent($type = 'content')
    {
        $output = [];

        if ($type === 'excerpt') {
            ob_start();
            the_excerpt();
            $output[] = ob_get_clean();
        } else {
            ob_start();
            the_content(sprintf(__('Continue reading %s', 'novusopress'), the_title('<span class="sr-only">', '</span>', false)));
            $output[] = ob_get_clean();
        }

        return apply_filters('novusopress_view_post_content_output', implode('', $output));
    }

    /**
     * Retrieves the content for attachments
     *
     * @return string
     */
    public function attachmentContent()
    {
        $output = [];

        if (wp_attachment_is_image()) {
            $output[] = sprintf(
                '<div class="attachment-image">%s</div>',
                wp_get_attachment_image(
                    get_the_ID(),
                    apply_filters('novusopress_view_attachment_image_size', 'medium')
                )
            );
        } else {
            $output[] = sprintf(
                '<div class="attachment-link"><a href="%s">%s</a></div>',
                wp_get_attachment_url(get_the_ID()),
                get_the_title()
            );
        }

        if (has_excerpt()) {
            $output[] = sprintf('<div class="entry-caption">%s</div>', get_the_excerpt());
        }

        return apply_filters('novusopress_view_attachment_content_output', implode('', $output));
    }

    /**
     * Retrieves the output of wp_head
     *
     * @return string
     */
    public function wpHead()
    {
        ob_start();
        wp_head();
        $output = ob_get_clean();

        return apply_filters('novusopress_view_wp_head_output', $output);
    }

    /**
     * Retrieves the output of wp_footer
     *
     * @return string
     */
    public function wpFooter()
    {
        ob_start();
        wp_footer();
        $output = ob_get_clean();

        return apply_filters('novusopress_view_wp_footer_output', $output);
    }

    /**
     * Retrieves the language attribute
     *
     * @return string
     */
    public function langAttr()
    {
        ob_start();
        language_attributes();
        $output = ob_get_clean();

        return apply_filters('novusopress_view_lang_attr_output', $output);
    }

    /**
     * Retrieves the charset
     *
     * @return string
     */
    public function charset()
    {
        $output = get_bloginfo('charset');

        return apply_filters('novusopress_view_charset_output', $output);
    }

    /**
     * Retrieves the site name
     *
     * @return string
     */
    public function siteName()
    {
        $output = get_bloginfo('name');

        return apply_filters('novusopress_view_site_name_output', $output);
    }

    /**
     * Retrieves the site name heading
     *
     * @return string
     */
    public function siteNameHeading(array $args = [])
    {
        $defaults = [
            'id'     => 'site-name',
            'class'  => 'site-name',
            'homeEl' => 'h1',
            'pageEl' => 'h3'
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $el = (is_front_page() || is_home()) ? $homeEl : $pageEl;

        $output = [];
        $output[] = sprintf('<%s id="%s" class="%s">', $el, $id, $class);
        $output[] = get_bloginfo('name');
        $output[] = sprintf('</%s>', $el);

        return apply_filters('novusopress_view_site_name_heading_output', implode('', $output));
    }

    /**
     * Retrieves the site description
     *
     * @return string
     */
    public function siteDescription()
    {
        $output = get_bloginfo('description', 'display');

        return apply_filters('novusopress_view_site_description_output', $output);
    }

    /**
     * Retrieves the site description heading
     *
     * @return string
     */
    public function siteDescriptionHeading(array $args = [])
    {
        $defaults = [
            'id'     => 'site-description',
            'class'  => 'site-description',
            'homeEl' => 'h3',
            'pageEl' => 'h5'
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $el = (is_front_page() || is_home()) ? $homeEl : $pageEl;

        $output = [];
        $output[] = sprintf('<%s id="%s" class="%s">', $el, $id, $class);
        $output[] = get_bloginfo('description');
        $output[] = sprintf('</%s>', $el);

        return apply_filters('novusopress_view_site_description_heading_output', implode('', $output));
    }

    /**
     * Checks if the logo image exists
     *
     * @param string $filename The logo filename
     *
     * @return boolean
     */
    public function logoExists($filename = 'logo.png')
    {
        $framework = Framework::instance();

        if (file_exists(sprintf('%s/img/%s', $framework->getThemeAssetsDir(), $filename))) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the logo markup
     *
     * @param string  $filename The logo filename
     * @param boolean $linked   Whether or not the logo should link to the homepage
     *
     * @return string
     */
    public function logo($filename = 'logo.png', $linked = true)
    {
        $framework = Framework::instance();
        $output = [];

        if (file_exists(sprintf('%s/img/%s', $framework->getThemeAssetsDir(), $filename))) {
            if ($linked) {
                $output[] = sprintf(
                    '<a href="%s" alt="%s logo">',
                    esc_url(home_url('/')),
                    esc_attr(get_bloginfo('name'))
                );
            }

            $output[] = sprintf('<img src="%s/img/%s">', esc_attr($framework->getThemeAssetsUri()), $filename);

            if ($linked) {
                $output[] = '</a>';
            }
        }

        return apply_filters('novusopress_view_logo_output', implode('', $output));
    }

    /**
     * Retrieves the all rights reserved statement
     *
     * @return string
     */
    public function rightsReserved()
    {
        $output = __('All Rights Reserved', 'novusopress');

        return apply_filters('novusopress_view_rights_reserved_output', $output);
    }

    /**
     * Checks if the current post has a featured image
     *
     * @return boolean
     */
    public function hasFeaturedImage()
    {
        if (!has_post_thumbnail() || post_password_required() || is_attachment()) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the featured image
     *
     * @return string
     */
    public function featuredImage()
    {
        if (!has_post_thumbnail() || post_password_required() || is_attachment()) {
            $output = '';
        } else {
            if (is_singular()) {
                $output = get_the_post_thumbnail();
            } else {
                $output = get_the_post_thumbnail(null, 'post-thumbnail', ['alt' => get_the_title()]);
            }
        }

        return apply_filters('novusopress_view_featured_image_output', $output);
    }

    /**
     * Retrieves the blog title
     *
     * @return string
     */
    public function blogTitle()
    {
        if (get_option('page_for_posts')) {
            $blogPageId = get_option('page_for_posts');

            $output = get_the_title($blogPageId);
        } else {
            $output = '';
        }

        return apply_filters('novusopress_view_blog_title_output', $output);
    }

    /**
     * Retrieves the archive title
     *
     * @return string
     */
    public function archiveTitle()
    {
        if (is_category()) {
            $output = sprintf('%s: %s', __('Category', 'novusopress'), single_cat_title('', false));
        } elseif (is_tag()) {
            $output = sprintf('%s: %s', __('Tag', 'novusopress'), single_tag_title('', false));
        } elseif (is_day()) {
            $output = sprintf('%s: %s', __('Date Archive', 'novusopress'), get_the_time(get_option('date_format')));
        } elseif (is_month()) {
            $output = sprintf('%s: %s', __('Month Archive', 'novusopress'), get_the_time('F Y'));
        } elseif (is_year()) {
            $output = sprintf('%s: %s', __('Year Archive', 'novusopress'), get_the_time('Y'));
        } elseif (is_author()) {
            global $author;

            $userdata = get_userdata($author);

            $output = sprintf('%s: %s', __('Author', 'novusopress'), $userdata->display_name);
        } else {
            $output = '';
        }

        return apply_filters('novusopress_view_archive_title_output', $output);
    }

    /**
     * Retrieves the search title
     *
     * @return string
     */
    public function searchTitle()
    {
        $output = sprintf('%s: %s', __('Search results', 'novusopress'), get_search_query());

        return apply_filters('novusopress_view_search_title_output', $output);
    }

    /**
     * Retrieves the not found error title
     *
     * @return string
     */
    public function notFoundTitle()
    {
        $output = __('Page Not Found', 'novusopress');

        return apply_filters('novusopress_view_not_found_title_output', $output);
    }

    /**
     * Checks if the sidebar is active
     *
     * @return boolean
     */
    public function isSidebarActive()
    {
        return is_active_sidebar(sprintf('sidebar-%s', $this->template));
    }

    /**
     * Retrieves the dynamic sidebar output
     *
     * @return string
     */
    public function dynamicSidebar()
    {
        ob_start();
        dynamic_sidebar(sprintf('sidebar-%s', $this->template));
        $output = ob_get_clean();

        return apply_filters('novusopress_view_dynamic_sidebar_output', $output);
    }

    /**
     * Checks if the header widgets are active
     *
     * @return boolean
     */
    public function isHeaderWidgetsActive()
    {
        return is_active_sidebar('header-widgets');
    }

    /**
     * Retrieves the header widgets output
     *
     * @return string
     */
    public function headerWidgets()
    {
        ob_start();
        dynamic_sidebar('header-widgets');
        $output = ob_get_clean();

        return apply_filters('novusopress_view_header_widgets_output', $output);
    }

    /**
     * Checks if footer widget areas are active
     *
     * @return boolean
     */
    public function isFooterActive()
    {
        if (is_active_sidebar('footer-one')
            || is_active_sidebar('footer-two')
            || is_active_sidebar('footer-three')
            || is_active_sidebar('footer-four')) {
            return true;
        }

        return false;
    }

    /**
     * Checks if footer widget area one is active
     *
     * @return boolean
     */
    public function isFooterOneActive()
    {
        return is_active_sidebar('footer-one');
    }

    /**
     * Retrieves footer widget area one output
     *
     * @return string
     */
    public function footerOne()
    {
        ob_start();
        dynamic_sidebar('footer-one');
        $output = ob_get_clean();

        return apply_filters('novusopress_view_footer_one_output', $output);
    }

    /**
     * Checks if footer widget area two is active
     *
     * @return boolean
     */
    public function isFooterTwoActive()
    {
        return is_active_sidebar('footer-two');
    }

    /**
     * Retrieves footer widget area two output
     *
     * @return string
     */
    public function footerTwo()
    {
        ob_start();
        dynamic_sidebar('footer-two');
        $output = ob_get_clean();

        return apply_filters('novusopress_view_footer_two_output', $output);
    }

    /**
     * Checks if footer widget area three is active
     *
     * @return boolean
     */
    public function isFooterThreeActive()
    {
        return is_active_sidebar('footer-three');
    }

    /**
     * Retrieves footer widget area three output
     *
     * @return string
     */
    public function footerThree()
    {
        ob_start();
        dynamic_sidebar('footer-three');
        $output = ob_get_clean();

        return apply_filters('novusopress_view_footer_three_output', $output);
    }

    /**
     * Checks if footer widget area four is active
     *
     * @return boolean
     */
    public function isFooterFourActive()
    {
        return is_active_sidebar('footer-four');
    }

    /**
     * Retrieves footer widget area four output
     *
     * @return string
     */
    public function footerFour()
    {
        ob_start();
        dynamic_sidebar('footer-four');
        $output = ob_get_clean();

        return apply_filters('novusopress_view_footer_four_output', $output);
    }

    /**
     * Checks if the main menu is active
     *
     * @return boolean
     */
    public function isMainMenuActive()
    {
        return has_nav_menu('main-menu');
    }

    /**
     * Checks if the main menu navbar is static
     *
     * @return boolean
     */
    public function isStaticNav()
    {
        if (Framework::instance()->getThemeOption('navbar_location') === 'static') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the main menu navbar is fixed
     *
     * @return boolean
     */
    public function isFixedNav()
    {
        if (Framework::instance()->getThemeOption('navbar_location') === 'fixed') {
            return true;
        }

        return false;
    }

    /**
     * Checks if the main menu navbar is below header
     *
     * @return boolean
     */
    public function isBelowNav()
    {
        if (Framework::instance()->getThemeOption('navbar_location') === 'below') {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the toggle navigation text
     *
     * @return string
     */
    public function toggleNavigation()
    {
        $output = __('Toggle navigation', 'novusopress');

        return apply_filters('novusopress_view_toggle_navigation_output', $output);
    }

    /**
     * Retrieves the navbar color class
     *
     * @return string
     */
    public function navbarColor()
    {
        $output = Framework::instance()->getThemeOption('navbar_color');

        return apply_filters('novusopress_view_navbar_color_output', $output);
    }

    /**
     * Retrieves the main menu
     *
     * @param array $args An associated array of options
     *
     * @return string
     */
    public function mainMenu(array $args = [])
    {
        $defaults = [
            'searchForm'     => false,
            'btnColor'       => 'default',
            'containerEl'    => 'div',
            'containerId'    => 'navbar-collapse-main-menu',
            'containerClass' => 'navbar-collapse-main-menu navbar-collapse collapse',
            'menuEl'         => 'ul',
            'menuId'         => 'navbar-main-menu',
            'menuClass'      => 'navbar-main-menu navbar-nav nav',
            'depth'          => 2,
            'indent'         => 2
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $tab = '    ';
        $indent = (integer) $indent;

        $wrap = [];
        $wrap[] = PHP_EOL;
        $wrap[] = str_repeat($tab, $indent + 1);
        $wrap[] = sprintf('<%s id="%%1$s" class="%%2$s">', $menuEl);
        $wrap[] = PHP_EOL;
        $wrap[] = '%3$s';
        $wrap[] = str_repeat($tab, $indent + 1);
        $wrap[] = sprintf('</%s><!-- #%s -->', $menuEl, $menuId);

        if ($searchForm) {
            $wrap[] = $this->getNavbarSearchForm($btnColor);
        }

        $wrap[] = PHP_EOL;
        $wrap[] = str_repeat($tab, $indent);

        $output = [];
        $output[] = wp_nav_menu([
            'theme_location'  => 'main-menu',
            'container'       => $containerEl,
            'container_id'    => $containerId,
            'container_class' => $containerClass,
            'menu_id'         => $menuId,
            'menu_class'      => $menuClass,
            'items_wrap'      => implode('', $wrap),
            'depth'           => $depth,
            'echo'            => false,
            'walker'          => new NavbarWalker($indent + 2)
        ]);
        $output[] = sprintf('<!-- #%s -->', $containerId);

        return apply_filters('novusopress_view_main_menu_output', implode('', $output));
    }

    /**
     * Checks whether or not to display breadcrumbs
     *
     * @return boolean
     */
    public function displayBreadcrumbs()
    {
        if (!Framework::instance()->getThemeOption('display_breadcrumbs')) {
            return false;
        }

        if (!is_front_page() && !(is_home() && 'page' !== get_option('show_on_front'))) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the breadcrumbs markup
     *
     * @param array $args An associated array of options
     *
     * @return string
     */
    public function breadcrumbs(array $args = [])
    {
        global $post;

        $defaults = [
            'containerEl'    => 'div',
            'containerId'    => 'breadcrumbs-container',
            'containerClass' => 'breadcrumbs-container container',
            'listEl'         => 'ol',
            'listId'         => 'breadcrumb',
            'listClass'      => 'breadcrumb',
            'crumbEl'        => 'li',
            'activeEl'       => null,
            'activeClass'    => 'active'
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        if ($activeEl) {
            $openActive = sprintf('<%s><%s class="%s">', $crumbEl, $activeEl, $activeClass);
            $closeActive = sprintf('</%s></%s>', $activeEl, $crumbEl);
        } else {
            $openActive = sprintf('<%s class="%s">', $crumbEl, $activeClass);
            $closeActive = sprintf('</%s>', $crumbEl);
        }

        $output = [];
        $output[] = sprintf('<%s id="%s" class="%s">', $containerEl, $containerId, $containerClass);
        $output[] = PHP_EOL.str_repeat('    ', 2);
        $output[] = sprintf('<%s id="%s" class="%s">', $listEl, $listId, $listClass);
        $output[] = sprintf(
            '<%s><a href="%s">%s</a></%s>',
            $crumbEl,
            esc_url(home_url('/')),
            __('Home', 'novusopress'),
            $crumbEl
        );

        if (is_home() && get_option('page_for_posts')) {
            $blogPageId = get_option('page_for_posts');
            $blogPage = get_page($blogPageId);
            $output[] = $openActive;
            $output[] = $blogPage->post_title;
            $output[] = $closeActive;
        } elseif (is_category()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $cat = get_category(get_query_var('cat'), false);

            if ($cat->parent != 0) {
                $parents = get_category_parents($cat->parent, true, sprintf('</%s><%s>', $crumbEl, $crumbEl));
                $parents = preg_replace('# title=".*"#', '', $parents);
                $output[] = mb_substr(sprintf('<%s>%s', $crumbEl, $parents), 0, -4);
            }

            $output[] = $openActive;
            $output[] = single_cat_title('', false);
            $output[] = $closeActive;
        } elseif (is_search()) {
            $output[] = $openActive;
            $output[] = get_search_query();
            $output[] = $closeActive;
        } elseif (is_day()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $output[] = sprintf(
                '<%s><a href="%s">%s</a></%s>',
                $crumbEl,
                get_year_link(get_the_time('Y')),
                get_the_time('Y'),
                $crumbEl
            );

            $output[] = sprintf(
                '<%s><a href="%s">%s</a></%s>',
                $crumbEl,
                get_month_link(get_the_time('Y'), get_the_time('m')),
                get_the_time('F'),
                $crumbEl
            );

            $output[] = $openActive;
            $output[] = get_the_time('d');
            $output[] = $closeActive;
        } elseif (is_month()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $output[] = sprintf(
                '<%s><a href="%s">%s</a></%s>',
                $crumbEl,
                get_year_link(get_the_time('Y')),
                get_the_time('Y'),
                $crumbEl
            );

            $output[] = $openActive;
            $output[] = get_the_time('F');
            $output[] = $closeActive;
        } elseif (is_year()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $output[] = $openActive;
            $output[] = get_the_time('Y');
            $output[] = $closeActive;
        } elseif (is_single() && !is_attachment()) {
            if ('post' !== get_post_type()) {
                $postType = get_post_type_object(get_post_type());
                $typeSlug = $postType->rewrite;
                $typeLink = trailingslashit(esc_url(home_url('/')).$typeSlug['slug']);
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    $typeLink,
                    $postType->labels->singular_name,
                    $crumbEl
                );
            } else {
                if (get_option('page_for_posts')) {
                    $blogPageId = get_option('page_for_posts');
                    $output[] = sprintf(
                        '<%s><a href="%s">%s</a></%s>',
                        $crumbEl,
                        get_permalink($blogPageId),
                        get_the_title($blogPageId),
                        $crumbEl
                    );
                }

                $cat = get_the_category();

                if (isset($cat[0])) {
                    $cat = $cat[0];
                    $parents = get_category_parents($cat, true, sprintf('</%s><%s>', $crumbEl, $crumbEl));
                    $parents = preg_replace('# title=".*"#', '', $parents);
                    $output[] = mb_substr(sprintf('<%s>%s', $crumbEl, $parents), 0, -4);
                }
            }

            $output[] = $openActive;
            $output[] = get_the_title();
            $output[] = $closeActive;
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $postType = get_post_type_object(get_post_type());
            $output[] = $openActive;
            $output[] = $postType->labels->singular_name;
            $output[] = $closeActive;
        } elseif (is_attachment()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);

            if (isset($cat[0])) {
                $cat = $cat[0];
                $parents = get_category_parents($cat, true, sprintf('</%s><%s>', $crumbEl, $crumbEl));
                $parents = preg_replace('# title=".*"#', '', $parents);
                $output[] = mb_substr(sprintf('<%s>%s', $crumbEl, $parents), 0, -4);
            }

            $output[] = sprintf(
                '<%s><a href="%s">%s</a></%s>',
                $crumbEl,
                get_permalink($parent),
                $parent->post_title,
                $crumbEl
            );

            $output[] = $openActive;
            $output[] = get_the_title();
            $output[] = $closeActive;
        } elseif (is_page() && !$post->post_parent) {
            $output[] = $openActive;
            $output[] = get_the_title();
            $output[] = $closeActive;
        } elseif (is_page() && $post->post_parent) {
            $parentId = $post->post_parent;
            $frontId = get_option('page_on_front');

            if ($parentId != $frontId) {
                $breadcrumbs = [];

                while ($parentId) {
                    $page = get_page($parentId);

                    if ($parentId != $frontId) {
                        $breadcrumbs[] = sprintf(
                            '<%s><a href="%s">%s</a></%s>',
                            $crumbEl,
                            get_permalink($page->ID),
                            get_the_title($page->ID),
                            $crumbEl
                        );
                    }

                    $parentId = $page->post_parent;
                }

                for ($i = count($breadcrumbs) - 1; $i >= 0; $i--) {
                    $output[] = $breadcrumbs[$i];
                }
            }

            $output[] = $openActive;
            $output[] = get_the_title();
            $output[] = $closeActive;
        } elseif (is_tag()) {
            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $output[] = $openActive;
            $output[] = single_tag_title('', false);
            $output[] = $closeActive;
        } elseif (is_author()) {
            global $author;

            if (get_option('page_for_posts')) {
                $blogPageId = get_option('page_for_posts');
                $output[] = sprintf(
                    '<%s><a href="%s">%s</a></%s>',
                    $crumbEl,
                    get_permalink($blogPageId),
                    get_the_title($blogPageId),
                    $crumbEl
                );
            }

            $userdata = get_userdata($author);
            $output[] = $openActive;
            $output[] = $userdata->display_name;
            $output[] = $closeActive;
        } elseif (is_404()) {
            $output[] = $openActive;
            $output[] = __('Error 404', 'novusopress');
            $output[] = $closeActive;
        } elseif (has_post_format() && !is_singular()) {
            $output[] = $openActive;
            $output[] = get_post_format_string(get_post_format());
            $output[] = $closeActive;
        }

        $output[] = sprintf('</%s>', $listEl);
        $output[] = PHP_EOL.'    ';
        $output[] = sprintf('</%s><!-- #%s -->', $containerEl, $containerId);

        $output = implode('', $output);

        return apply_filters('novusopress_view_breadcrumbs_output', $output);
    }

    /**
     * Retrieves the pagination markup
     *
     * @param array $args An associated array of options
     *
     * @return string
     */
    public function pagination(array $args = [])
    {
        $defaults = [
            'range'          => 2,
            'pages'          => null,
            'containerEl'    => 'div',
            'containerId'    => 'pagination-container',
            'containerClass' => 'pagination-container',
            'listEl'         => 'ul',
            'listClass'      => 'pagination',
            'itemEl'         => 'li',
            'innerEl'        => 'span',
            'activeClass'    => 'active',
            'disabledClass'  => 'disabled',
            'overview'       => false
        ];

        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        $output = [];

        if (is_singular()) {
            global $page, $numpages;

            if ($overview) {
                $before = sprintf(
                    '<%s class="%s"><%s class="%s"><%s>%s</%s></%s><%s>',
                    $listEl,
                    $listClass,
                    $itemEl,
                    $disabledClass,
                    $innerEl,
                    sprintf(__('Page %d of %d', 'novusopress'), $page, $numpages),
                    $innerEl,
                    $itemEl,
                    $itemEl
                );
            } else {
                $before = sprintf('<%s class="%s"><%s>', $listEl, $listClass, $itemEl);
            }

            add_filter('wp_link_pages_link', function ($link, $i) use ($activeClass, $innerEl) {
                global $page;

                if ($i === $page) {
                    return sprintf('<%s class="%s">%s</%s>', $innerEl, $activeClass, $link, $innerEl);
                }

                return $link;
            }, 10, 2);

            $links = wp_link_pages([
                'before'         => $before,
                'after'          => sprintf('</%s></%s>', $itemEl, $listEl),
                'next_or_number' => 'number',
                'separator'      => sprintf('</%s><%s>', $itemEl, $itemEl),
                'echo'           => false
            ]);

            $output[] = preg_replace(
                sprintf('#<%s>[\s]*<%s class="%s">#', $itemEl, $innerEl, $activeClass),
                sprintf('<%s class="%s"><%s>', $itemEl, $activeClass, $innerEl),
                $links
            );
        } else {
            global $paged;

            $range = (integer) $range;
            $show = ($range * 2) + 1;

            if (empty($paged)) $paged = 1;

            if (null === $pages) {
                global $wp_query;

                $pages = $wp_query->max_num_pages;

                if (!$pages) {
                    $pages = 1;
                }
            }

            $paged = (integer) $paged;
            $pages = (integer) $pages;

            if (1 !== $pages) {
                $output[] = sprintf('<%s class="%s">', $listEl, $listClass);

                if ($overview) {
                    $output[] = sprintf(
                        '<%s class="%s"><%s>%s</%s></%s>',
                        $itemEl,
                        $disabledClass,
                        $innerEl,
                        sprintf(__('Page %d of %d', 'novusopress'), $paged, $pages),
                        $innerEl,
                        $itemEl
                    );
                }

                if ($paged > 2 && $paged > $range + 1 && $show < $pages) {
                    $output[] = sprintf(
                        '<%s><a href="%s">%s %s</a></%s>',
                        $itemEl,
                        get_pagenum_link(1),
                        '&laquo;',
                        __('First', 'novusopress'),
                        $itemEl
                    );
                }

                if ($paged > 1 && $show < $pages) {
                    $output[] = sprintf(
                        '<%s><a href="%s">%s %s</a></%s>',
                        $itemEl,
                        get_pagenum_link($paged - 1),
                        '&lsaquo;',
                        __('Previous', 'novusopress'),
                        $itemEl
                    );
                }

                for ($i = 1; $i <= $pages; $i++) {
                    if (1 !== $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $show)) {
                        if ($paged === $i) {
                            $output[] = sprintf(
                                '<%s class="%s"><%s>%s</%s></%s>',
                                $itemEl,
                                $activeClass,
                                $innerEl,
                                $i,
                                $innerEl,
                                $itemEl
                            );
                        } else {
                            $output[] = sprintf(
                                '<%s><a href="%s">%s</a></%s>',
                                $itemEl,
                                get_pagenum_link($i),
                                $i,
                                $itemEl
                            );
                        }
                    }
                }

                if ($paged < $pages && $show < $pages) {
                    $output[] = sprintf(
                        '<%s><a href="%s">%s %s</a></%s>',
                        $itemEl,
                        get_pagenum_link($paged + 1),
                        __('Next', 'novusopress'),
                        '&rsaquo;',
                        $itemEl
                    );
                }

                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $show < $pages) {
                    $output[] = sprintf(
                        '<%s><a href="%s">%s %s</a></%s>',
                        $itemEl,
                        get_pagenum_link($pages),
                        __('Last', 'novusopress'),
                        '&raquo;',
                        $itemEl
                    );
                }

                $output[] = sprintf('</%s>', $listEl);
            }
        }

        $output = implode('', $output);

        if (!empty($output)) {
            $output = sprintf(
                '<%s id="%s" class="%s">%s</%s>',
                $containerEl,
                $containerId,
                $containerClass,
                $output,
                $containerEl
            );
        }

        return apply_filters('novusopress_view_pagination_output', $output);
    }

    /**
     * Retrieves the comments template
     *
     * @return string
     */
    public function comments()
    {
        $output = [];

        if (comments_open() || get_comments_number()) {
            ob_start();
            comments_template();
            $output[] = ob_get_clean();
        }

        return apply_filters('novusopress_view_comments_output', implode('', $output));
    }

    /**
     * Retrieves the post format meta
     *
     * @return string
     */
    public function postFormatMeta()
    {
        $output = [];

        $format = get_post_format();
        if (current_theme_supports('post-formats', $format)) {
            $output[] = '<div class="meta-format">';
            $output[] = sprintf(
                '<span class="entry-format">%s<a href="%s">%s</a></span>',
                sprintf('<span class="sr-only">%s</span>', _x('Format', 'Used before post format', 'novusopress')),
                esc_url(get_post_format_link($format)),
                get_post_format_string($format)
            );
            $output[] = '</div>';
        }

        return apply_filters('novusopress_view_post_format_meta_output', implode('', $output));
    }

    /**
     * Retrieves the post time meta
     *
     * @return string
     */
    public function postTimeMeta()
    {
        $output = [];

        if (in_array(get_post_type(), ['post', 'attachment'])) {
            $timeString = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

            $timeString = sprintf(
                $timeString,
                esc_attr(get_the_date('c')),
                get_the_date()
            );

            $output[] = '<div class="meta-date">';
            $output[] = sprintf(
                '<span class="posted-on"><span class="sr-only">%s</span><span class="label label-default">'
                    .'<span class="fa fa-calendar"></span> <a href="%s" rel="bookmark">%s</a></span></span>',
                _x('Posted on', 'Used before published date', 'novusopress'),
                esc_url(get_permalink()),
                $timeString
            );
            $output[] = '</div>';
        }

        return apply_filters('novusopress_view_post_time_meta_output', implode('', $output));
    }

    /**
     * Retrieves the post author meta
     *
     * @return string
     */
    public function postAuthorMeta()
    {
        /* TODO: Complete with author box
        $output = [];

        if ('post' === get_post_type()) {
            $output[] = sprintf(
                '<span class="byline"><span class="author vcard">'
                    .'<span class="sr-only">%s</span><a href="%s">%s</a></span></span>',
                _x('Author', 'Used before post author name', 'novusopress'),
                esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                get_the_author()
            );
        }

        return apply_filters('novusopress_view_post_author_meta_output', implode('', $output));
        */
    }

    /**
     * Retrieves the post classification meta
     *
     * @return string
     */
    public function postClassificationMeta()
    {
        $output = [];

        if ('post' === get_post_type()) {
            $output[] = '<ul class="entry-classification list-inline">';
            $categoriesList = get_the_category_list('</span> <span class="label label-primary"><span class="fa fa-folder"></span> ');
            if ($categoriesList) {
                $output[] = '<li class="meta-categories meta-item"><span class="label label-primary">';
                $output[] = '<span class="fa fa-folder"></span> ';
                $output[] = $categoriesList;
                $output[] = '</span></li>';
            }
            $tagsList = get_the_tag_list('', '</span> <span class="label label-info"><span class="fa fa-tag"></span> ');
            if ($tagsList) {
                $output[] = '<li class="meta-tags meta-item"><span class="label label-info">';
                $output[] = '<span class="fa fa-tag"></span> ';
                $output[] = $tagsList;
                $output[] = '</span></li>';
            }
            $output[] = '</ul>';
        }

        return apply_filters('novusopress_view_post_classification_meta_output', implode('', $output));
    }

    /**
     * Checks if there is a comments link
     *
     * @return boolean
     */
    public function hasCommentsLink()
    {
        if (is_single() || post_password_required() || (!comments_open() || !get_comments_number())) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the comments popup link
     *
     * @return string
     */
    public function commentsPopupLink()
    {
        $output = [];

        if (is_single() || post_password_required() || (!comments_open() || !get_comments_number())) {
            return '';
        }

        ob_start();
        comments_popup_link(
            __('Leave a comment', 'novusopress'),
            sprintf(__('%1$s1%2$s Comment', 'novusopress'), '<span class="badge">', '</span>'),
            sprintf(__('%1$s%%%2$s Comments', 'novusopress'), '<span class="badge">', '</span>'),
            'btn btn-default'
        );
        $link = ob_get_clean();

        $output[] = '<div class="comments-link-area pad-bottom">';
        $output[] = sprintf('<span class="comments-link">%s</span>', $link);
        $output[] = '</div>';

        return apply_filters('novusopress_view_comments_popup_link_output', implode('', $output));
    }

    /**
     * Retrieves the image attachment link
     *
     * @return string
     */
    public function imageAttachmentLink()
    {
        $output = [];

        if (is_attachment() && wp_attachment_is_image()) {
            $metadata = wp_get_attachment_metadata();
            $output[] = sprintf(
                '<span class="full-size-link"><span class="sr-only">%s</span><a href="%s">%s &times; %s</a></span>',
                _x('Full size', 'Used before full size attachment link', 'novusopress'),
                esc_url(wp_get_attachment_url()),
                $metadata['width'],
                $metadata['height']
            );
        }

        return apply_filters('novusopress_view_image_attachment_link_output', implode('', $output));
    }

    /**
     * Retrieves the body classes
     *
     * @param string|array $classes Additional body classes
     *
     * @return string
     */
    public function bodyClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes = get_body_class($classes);

        if (Framework::instance()->getThemeOption('navbar_location') === 'fixed') {
            $classes[] = 'fixed-nav';
        }

        $classes = apply_filters('novusopress_view_body_classes', $classes);
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_body_class_output', $output);
    }

    /**
     * Retrieves the wrapper classes
     *
     * @param string|array $classes Additional wrapper classes
     *
     * @return string
     */
    public function wrapperClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes[] = 'wrapper';
        $classes[] = sprintf('template-%s', $this->template);

        if (is_active_sidebar(sprintf('sidebar-%s', $this->template))) {
            $classes[] = 'active-sidebar';
        }

        if (has_nav_menu('main-menu')) {
            $classes[] = 'active-main-menu';
        }

        if (is_active_sidebar('header-widgets')) {
            $classes[] = 'active-header-widgets';
        }

        $i = 0;
        if (is_active_sidebar('footer-one')) $i++;
        if (is_active_sidebar('footer-two')) $i++;
        if (is_active_sidebar('footer-three')) $i++;
        if (is_active_sidebar('footer-four')) $i++;

        switch ($i) {
            case 1:
                $classes[] = 'active-footers-one';
                break;
            case 2:
                $classes[] = 'active-footers-two';
                break;
            case 3:
                $classes[] = 'active-footers-three';
                break;
            case 4:
                $classes[] = 'active-footers-four';
                break;
            default:
                break;
        }

        $classes = apply_filters('novusopress_view_wrapper_classes', $classes);
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_wrapper_class_output', $output);
    }

    /**
     * Retrieves the content classes
     *
     * @param string|array $classes Additional content classes
     *
     * @return string
     */
    public function contentClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes[] = sprintf('main-%s', $this->template);
        $classes[] = 'main-content';

        if (is_active_sidebar(sprintf('sidebar-%s', $this->template))) {
            $classes[] = 'col-sm-8 col-md-9';
        } else {
            $classes[] = 'col-sm-12';
        }

        $classes = apply_filters('novusopress_view_content_classes', $classes);
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_content_class_output', $output);
    }

    /**
     * Retrieves the post classes
     *
     * @param string|array $classes Additional post classes
     *
     * @return string
     */
    public function postClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes[] = 'content-node';

        $classes = apply_filters('novusopress_view_post_classes', get_post_class($classes));
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_post_class_output', $output);
    }

    /**
     * Retrieves the sidebar classes
     *
     * @param string|array $classes Additional sidebar classes
     *
     * @return string
     */
    public function sidebarClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes[] = sprintf('sidebar-%s', $this->template);
        $classes[] = 'sidebar';
        $classes[] = 'col-sm-4 col-md-3';

        $classes = apply_filters('novusopress_view_sidebar_classes', $classes);
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_sidebar_class_output', $output);
    }

    /**
     * Retrieves the footer classes
     *
     * @param string|array $classes Additional footer classes
     *
     * @return string
     */
    public function footerClass($classes = [])
    {
        $classes = !is_array($classes) ? explode(' ', $classes) : $classes;
        $classes[] = 'footer-area';

        $i = 0;
        if (is_active_sidebar('footer-one')) $i++;
        if (is_active_sidebar('footer-two')) $i++;
        if (is_active_sidebar('footer-three')) $i++;
        if (is_active_sidebar('footer-four')) $i++;

        switch ($i) {
            case 1:
                $classes[] = 'col-sm-12';
                break;
            case 2:
                $classes[] = 'col-sm-6';
                break;
            case 3:
                $classes[] = 'col-sm-4';
                break;
            case 4:
                $classes[] = 'col-sm-6 col-md-3';
                break;
            default:
                break;
        }

        $classes = apply_filters('novusopress_view_footer_classes', $classes);
        $output = sprintf('class="%s"', implode(' ', $classes));

        return apply_filters('novusopress_view_footer_class_output', $output);
    }

    /**
     * Retrieves the markup for the navbar search form
     *
     * @param string $btnColor The button color class
     *
     * @return string
     */
    protected function getNavbarSearchForm($btnColor)
    {
        $action = esc_url(home_url('/'));
        $label = __('Search for', 'novusopress');
        $placeholder = __('Search', 'novusopress');
        $form = <<<EOT

            <form id="navbar-search-form" method="get" action="$action" class="navbar-form navbar-right" role="search">
                <label for="nav_input_search" class="sr-only">$label</label>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button id="nav-searchsubmit" type="submit" class="btn btn-$btnColor">
                            <span class="fa fa-search"></span>
                        </button>
                    </div>
                    <input id="nav_input_search" type="search" class="form-control" name="s" placeholder="$placeholder">
                </div>
            </form><!-- #navbar-search-form -->
EOT;

        return $form;
    }
}
