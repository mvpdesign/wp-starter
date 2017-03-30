<?php
/*
 * Simple Header Nav
 */
function mvp_header_nav() {
    wp_nav_menu(array(
        'theme_location'  => 'header-menu',
        'menu'            => '',
        'container'       => 'div',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => new MVP_Nav_Walker
    ));
}

/*
 * Simple Footer Nav
 */
function mvp_footer_nav() {
    wp_nav_menu(array(
        'theme_location'  => 'footer-menu',
        'menu'            => '',
        'container'       => 'div',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => new MVP_Nav_Walker
    ));
}


/*
 * Register MVP Navigation
 */
function register_mvp_menu() {
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => 'Header Menu',
        'footer-menu' => 'Footer Menu'
    ));
}
add_action('init', 'register_mvp_menu'); // Add HTML5 Blank Menu



/*
 * Remove the <div> surrounding the dynamic navigation to cleanup markup
 */
function my_wp_nav_menu_args($args = '') {
    $args['container'] = false;
    return $args;
}

add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args');



// -----------------------------------------------------------------------------
// CLEAN UP THE WORDPRESS NAVIGATION

/*
 * Remove Injected classes, ID's and Page ID's from Navigation <li> items
 * except for those in the array
 */
function custom_wp_nav_menu($var) {
    return is_array($var) ? array_intersect($var, array(
		// List of allowed menu classes
		'current_page_item',
		'current_page_parent',
		'current_page_ancestor'
		)
	) : '';
}

// Remove Navigation <li> injected classes (Commented out by default)
add_filter('nav_menu_css_class', 'custom_wp_nav_menu', 100, 1);

// Remove Navigation <li> injected ID (Commented out by default)
add_filter('nav_menu_item_id', 'custom_wp_nav_menu', 100, 1);

// Remove Navigation <li> Page ID's (Commented out by default)
add_filter('page_css_class', 'custom_wp_nav_menu', 100, 1);



/*
 * Remove empty classes
 */
function strip_empty_classes($menu) {
    $menu = preg_replace('/ class="""/','',$menu);
    return $menu;
}

add_filter ('wp_nav_menu','strip_empty_classes');



/*
 * Add classes to anchor in footer menu
 */
// function footer_walker_nav_menu_start_el($item_output, $item, $depth, $args) {
//     $menu_locations = get_nav_menu_locations();
//
//     if ( has_term($menu_locations['footer-menu'], 'nav_menu', $item) ) {
//        $item_output = preg_replace('/<a /', '<a class="u-subtleLink u-hideLink" ', $item_output, 1);
//     }
//
//     return $item_output;
// }
// add_filter('walker_nav_menu_start_el', 'footer_walker_nav_menu_start_el', 10, 4);
