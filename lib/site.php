<?php

use function Eshopiste\Helpers\get_eshop_category_link;
use function Eshopiste\CPT\get_cpt_eshop_args;
use function Eshopiste\CPT\get_cpt_bid_args;
use function Eshopiste\CPT\get_cpt_eshop_taxonomy_args;

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
		register_post_type( 'eshop', get_cpt_eshop_args() );
		register_post_type( 'eshop_bid', get_cpt_bid_args() );
	}

	function register_taxonomies() {
		register_taxonomy( 'eshop_category', array( 'eshop' ), get_cpt_eshop_taxonomy_args() );
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
		$context['header_menu'] = new Timber\Menu( 'header-menu' );
		$context['footer_menu'] = new Timber\Menu( 'footer-menu' );
		$context['site'] = $this;
		$context['options'] = get_fields('options');
		$context['current_url'] = Timber\URLHelper::get_current_url();
		$context['archive_link'] =  get_post_type_archive_link( 'eshop' );
		$context['all_categories'] = Timber::get_terms('eshop_category');
		$context['footer_text'] = get_fields('options')['footer_text'];
		$context['query'] = ( isset($_GET['q']) ? $_GET['q'] : null );
		$context['is_user_logged_in'] = is_user_logged_in();
		$context['user'] = wp_get_current_user();
		$context['login_url'] = wp_login_url();
		$context['logout_url'] = wp_logout_url();
		$context['registration_url'] = wp_registration_url();
		$context['admin_url'] = admin_url();
		$context['admin_profile_url'] = admin_url( 'profile.php' );
		$context['admin_eshops_url'] = admin_url( 'edit.php?post_type=eshop' );
		$context['new_eshop_url'] = admin_url( 'post-new.php?post_type=eshop' );
		return $context;
	}

	function add_to_twig( $twig ) {
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('display_url', new Twig_SimpleFilter('display_url', [$this, 'display_url']));
		$twig->addFilter('static_assets', new Twig_SimpleFilter('static_assets', array($this, 'static_assets')));
		$twig->addFilter('is_new', new Twig_SimpleFilter('is_new', array($this, 'is_post_new')));
		$twig->addFilter('category_link', new Twig_SimpleFilter('category_link', array($this, 'category_link')));
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

	function is_post_new( $post ) {
		$today = new DateTime();
		$post_date = new DateTime($post->date('Y-m-d'));
		$interval = $today->diff($post_date);

		return $interval->days <= 30;
	}

	function category_link( $term ) {
		return get_eshop_category_link( $term->id );
	}

}

new EshopisteSite();
