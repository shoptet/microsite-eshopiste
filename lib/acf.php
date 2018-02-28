<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page([
		'menu_title' 	=> 'Šablona',
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> 'Obecná správa',
		'menu_title' 	=> 'Obecné',
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> 'Nastavení domovské stránky',
		'menu_title' 	=> 'Homepage',
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> 'Nastavení seznamu e-shopů',
		'menu_title' 	=> 'Seznam e-shopů',
		'parent_slug' => 'theme-settings',
	]);

	acf_add_options_sub_page([
		'page_title' 	=> 'Nastavení mailingu',
		'menu_title' 	=> 'Mailing',
		'parent_slug' => 'theme-settings',
	]);

}
