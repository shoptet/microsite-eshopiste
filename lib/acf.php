<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'menu_title' 	=> 'Šablona',
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Obecná správa',
		'menu_title' 	=> 'Obecné',
		'parent_slug' => 'theme-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení domovské stránky',
		'menu_title' 	=> 'Homepage',
		'parent_slug' => 'theme-settings',
	));

}
