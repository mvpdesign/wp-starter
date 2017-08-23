<?php

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function mvp_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/mvp
	 * If you're building a theme based on MVP, use a find and replace
	 * to change 'mvp' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'mvp' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// Add Thumbnail Theme Support
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'large', 700, '', true ); // Large Thumbnail
	add_image_size( 'medium', 250, '', true ); // Medium Thumbnail
	add_image_size( 'small', 120, '', true ); // Small Thumbnail
	add_image_size( 'custom-size', 700, 200, true ); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'search-form',
		'gallery',
		'caption',
	) );

	/*
	 * Remove unused areas from the admin sidebar
	 */
	add_action('admin_menu', function() {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
	});
}

add_action( 'after_setup_theme', 'mvp_setup' );



/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Messerli_Kramer 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function mvp_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'mvp' ), get_the_title( get_the_ID() ) )
	);

	return ' &hellip; ' . $link;
}

add_filter( 'excerpt_more', 'mvp_excerpt_more' );



/**
 * Enqueue scripts and styles.
 */
function mvp_scripts() {
	wp_enqueue_style( 'theme', get_template_directory_uri() . '/assets/dist/styles.css', '1.0');

	if (!is_admin()) {
		// Deregister jQuery
		// And load a custom version in the footer
		// wp_deregister_script('jquery');

		// wp_enqueue_script('jquery', get_template_directory_uri() . '/assets/dist/jquery-custom.min.js', array(), '3.1.1', true);

		// The last parameter set to TRUE states that it should be loaded
		// in the footer.
		wp_register_script('app', get_template_directory_uri() . '/assets/dist/app.js', FALSE, '1.0', TRUE);
		wp_enqueue_script('app');
    }
}

add_action( 'wp_enqueue_scripts', 'mvp_scripts' );



/**
 * Remove everything related to the emoji support
 *
 * @since MVP Starter 1.0
*/
function disable_emojicons_tinymce( $plugins ) {
  	if ( is_array( $plugins ) ) {
    	return array_diff( $plugins, array( 'wpemoji' ) );
  	} else {
    	return array();
	}
}

function disable_wp_emojicons() {
	// all actions related to emojis
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// filter to remove TinyMCE emojis
	add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}

add_action( 'init', 'disable_wp_emojicons' );
add_filter( 'emoji_svg_url', '__return_false' );



/**
 * Allow for SVG's in Wordpress
 *
 * @since MVP Starter 1.0
*/
function allow_svg($filetype_ext_data, $file, $filename, $mimes) {
	if ( substr($filename, -4) === '.svg' ) {
		$filetype_ext_data['ext'] = 'svg';
		$filetype_ext_data['type'] = 'image/svg+xml';
	}

	return $filetype_ext_data;
}

function cc_mime_types( $mimes = array() ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}

add_filter( 'wp_check_filetype_and_ext', 'allow_svg', 100, 4);
add_filter( 'upload_mimes', 'cc_mime_types' );




/**
 * Remove Actions
 *
 * @since MVP Starter 1.0
*/

// Remove the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'rsd_link');

// Remove the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'wp_generator');

// Remove the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links_extra', 3);

// Remove the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'feed_links', 2);

// Remove link to index page
remove_action('wp_head', 'index_rel_link');

// Remove the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'wlwmanifest_link');

// remove_action('wp_head', 'rel_canonical');

// Remove random post link
remove_action('wp_head', 'start_post_rel_link', 10, 0);

// Remove parent post link
remove_action('wp_head', 'parent_post_rel_link', 10, 0);

// Remove the next and previous post links
// remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
// remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

/**
 * Remove Filters
 *
 * @since MVP Starter 1.0
*/
// Remove <p> tags from Excerpt altogether
remove_filter('the_excerpt', 'wpautop');
