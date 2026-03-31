<?php
function euf_enqueue_styles() {
    $theme = wp_get_theme();

    // Parent style
    wp_enqueue_style(
        'divi-style',
        get_template_directory_uri() . '/style.css',
        array(),
        $theme->get('Version')
    );

    // Child main style
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        array('divi-style'),
        $theme->get('Version')
    );

    // Fonts CSS
    wp_enqueue_style(
        'child-fonts',
        get_stylesheet_directory_uri() . '/css/fonts.css',
        array('child-style'),
        $theme->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'euf_enqueue_styles');

// Hide WordPress version
add_filter('the_generator', '__return_empty_string');

// Hide REST API links from site header
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('template_redirect', 'rest_output_link_header', 11);

/* DISABLE ALL COMMENTS */
add_action('admin_init', function () {
    global $pagenow;

    // Redirect any user trying to access comments page
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});