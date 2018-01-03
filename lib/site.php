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
		parent::__construct();
	}

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
		$context['foo'] = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('display_url', new Twig_SimpleFilter('display_url', [$this, 'display_url']));
		return $twig;
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
