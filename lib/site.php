<?php

use Eshopiste\Helpers;

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	return;
}

Timber::$dirname = array('templates', 'views');

class EshopisteSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		parent::__construct();
	}

	function register_post_types() {
		$labels = array(
			'name' => __( 'E-shopy', '' ),
			'singular_name' => __( 'E-shop', '' ),
			'menu_name' => __( 'E-shopy', '' ),
			'all_items' => __( 'Všechny e-shopy', '' ),
			'add_new' => __( 'Přidat nový', '' ),
			'add_new_item' => __( 'Přidat nový e-shop', '' ),
			'edit_item' => __( 'Upravit e-shop', '' ),
			'new_item' => __( 'Nový e-shop', '' ),
			'view_item' => __( 'Zobrazit e-shop', '' ),
			'view_items' => __( 'Zobrazit e-shopy', '' ),
			'search_items' => __( 'Vyhledat e-shop', '' ),
			'not_found' => __( 'Nebyl nalezen žádný e-shop', '' ),
			'not_found_in_trash' => __( 'V koši nebyl nalezen žádný e-shop', '' ),
			'archives' => __( 'Archiv e-shopů', '' ),
			'items_list' => __( 'Výpis e-shopů', '' ),
		);
		$args = array(
			'label' => __( 'E-shopy', '' ),
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'has_archive' => 'eshopy',
			'show_in_menu' => true,
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'rewrite' => array( 'slug' => 'eshop', 'with_front' => true ),
			'query_var' => true,
			'menu_icon' => 'dashicons-cart',
			'supports' => array( 'title' ),
		);
		register_post_type( 'eshop', $args );
	}

	function register_taxonomies() {
		$labels = array(
			'name' => __( 'Kategorie', '' ),
			'singular_name' => __( 'Kategorie', '' ),
		);
		$args = array(
			'label' => __( 'Kategorie', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => false,
			'label' => 'Kategorie',
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'eshop_category', 'with_front' => true, ),
			'show_admin_column' => false,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => false,
		);
		register_taxonomy( 'eshop_category', array( 'eshop' ), $args );
	}

	function load_styles() {
		/* Load styles and add a cache-breaking URL parameter */

		$fileName = '/assets/main.css';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_style( 'main', $fileUrl, [], filemtime($filePath), 'all' );
	}

	function load_scripts() {
		/* Load scripts and add a cache-breaking URL parameter */

		$fileName = '/assets/vendor.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'vendor', $fileUrl, [], filemtime($filePath), true );

		$fileName = '/assets/main.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'main', $fileUrl, ['vendor'], filemtime($filePath), true );

	}

	function add_to_context( $context ) {
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		$context['options'] = get_fields('options');
		$context['current_url'] = Timber\URLHelper::get_current_url();
		$context['archive_link'] =  get_post_type_archive_link( 'eshop' );
		return $context;
	}

	function add_to_twig( $twig ) {
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('display_url', new Twig_SimpleFilter('display_url', [$this, 'display_url']));
		$twig->addFilter('static_assets', new Twig_SimpleFilter('static_assets', array($this, 'static_assets')));
		return $twig;
	}

	function static_assets( $filePath ) {
	  return $this->theme->link . '/assets/' . $filePath;
	}

	function display_url( $url ) {
		// Romove protocol
	  if (substr( $url, 0, 7 ) === 'http://') {
			$url = substr( $url, 7 );
	  } else if (substr( $url, 0, 8 ) === 'https://') {
			$url = substr( $url, 8 );
		} else if (substr( $url, 0, 2 ) === '//') {
		 	$url = substr( $url, 2 );
	 	}
		// Remove www subdomain
		/*
		if (substr( $url, 0, 4 ) === 'www.') {
			$url = substr( $url, 4 );
		}
		*/
		// Remove last slash
		if (substr( $url, -1 ) === '/') {
			$url = substr( $url, 0, -1 );
		}

	  return $url;
	}

}

new EshopisteSite();
