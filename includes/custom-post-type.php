<?php

class RegisterPostType {
 	private $single = '';
  	private $plural = '';
  	private $slug 	= '';
	private $taxonomy = false;
	private $icon = 'dashicons-admin-post';

	function RegisterPostType() {
		$this->__construct();
	}

	function __construct($args) {
		$this->single = $args['singular'];
		$this->plural = $args['plural'];
		$this->slug = $args['slug'];
		$this->taxonomy = $args['taxonomy'] ? $args['taxonomy'] : $this->taxonomy;
		$this->icon = $args['icon'] ? $args['icon'] : $this->icon;

		# Place your add_actions and add_filters here
		add_action( 'init', array( &$this, 'init' ) );
		add_action('init', array(&$this, 'add_post_type'));

		# Add Post Type to Search
		add_filter('pre_get_posts', array( &$this, 'query_post_type') );

		# Add Custom Taxonomies
		if ($this->taxonomy) {
			add_action( 'init', array( &$this, 'add_taxonomies'), 0 );
		}
	}

  	function init($options = null){
  		if ($options) {
	    	foreach ($options as $key => $value) {
	      		$this->$key = $value;
	    	}
    	}
  	}

	function add_post_type() {
    	$labels = array(
      		'name' => __($this->plural, 'post type general name'),
      		'singular_name' => _x($this->single, 'post type singular name'),
      		'add_new' => _x('Add ' . $this->single, $this->single),
      		'add_new_item' => __('Add New ' . $this->single),
      		'edit_item' => __('Edit ' . $this->single),
      		'new_item' => __('New ' . $this->single),
      		'view_item' => __('View ' . $this->single),
      		'search_items' => __('Search ' . $this->plural),
      		'not_found' =>  __('No ' . $this->plural . ' Found'),
      		'not_found_in_trash' => __('No ' . $this->plural . ' found in Trash'),
      		'parent_item_colon' => ''
    	);

	    $options = array(
			'labels' => $labels,
	      	'public' => true,
	      	'publicly_queryable' => true,
	      	'show_ui' => true,
	      	'query_var' => true,
	      	'rewrite' => true,
			'menu_icon' => $this->icon,
	      	'capability_type' => 'post',
	      	'hierarchical' => false,
	      	'has_archive' => true,
	      	'menu_position' => null,
	      	'supports' => array(
	      		'title',
	      		'editor',
	      		'thumbnail',
	      		'comments',
                'page-attributes'
	      	),
	    );

	    register_post_type($this->slug, $options);
  	}

	function query_post_type($query) {
		if (is_category() || is_tag()) {
	    	$post_type = get_query_var('post_type');

			if ($post_type) {
		  		$post_type = $post_type;
			} else {
		  		$post_type = array($this->slug); // replace cpt to your custom post type
	  		}

	  		$query->set('post_type',$post_type);

			return $query;
	  	}
	}

	function add_taxonomies() {
		register_taxonomy($this->taxonomy, array($this->slug), array(
		    'hierarchical' => true,
		    'labels' => array(
		    	'name' => __( $this->taxonomy ),
		    	'singular_name' => __( $this->taxonomy . 's' ),
		    	'all_items' => __( 'All ' . $this->taxonomy . 's' ),
		    	'add_new_item' => __( 'Add ' . $this->taxonomy )
		  	),
		  	'public' => true,
		    'query_var' => true,
		    'rewrite' => array(
		    	'slug' => strtolower($this->taxonomy)
		    )
		));
	}
}

// https://developer.wordpress.org/resource/dashicons/#plus

// EXAMPLE
// new RegisterPostType(array(
// 	'singular' => 'Case Study',
// 	'plural' => 'Case Studies',
// 	'slug' => 'case_studies',
// 	'icon' => 'dashicons-awards',
// 	'taxonomy' => 'Investment Types'
// ));
