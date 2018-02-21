<?php

namespace Eshopiste\CPT;

/**
 * Returns e-shop custom post type arguments
 */
function get_cpt_eshop_args(): array
{
  $labels = [
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
  ];
  $args = [
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
  ];
  return $args;
}

/**
 * Returns e-shop custom post type taxonomy arguments
 */
function get_cpt_eshop_taxonomy_args(): array
{
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
  return $args;
}

/**
 * Returns bid custom post type arguments
 */
function get_cpt_bid_args(): array
{
  $labels = [
    'name' => __( 'Nabídky', '' ),
    'singular_name' => __( 'Nabídka', '' ),
    'menu_name' => __( 'Nabídky', '' ),
    'all_items' => __( 'Všechny nabídky', '' ),
    'add_new' => __( 'Přidat novou', '' ),
    'add_new_item' => __( 'Přidat novou nabídku', '' ),
    'edit_item' => __( 'Upravit nabídku', '' ),
    'new_item' => __( 'Nová nabídka', '' ),
    'view_item' => __( 'Zobrazit nabídku', '' ),
    'view_items' => __( 'Zobrazit nabídky', '' ),
    'search_items' => __( 'Vyhledat nabídku', '' ),
    'not_found' => __( 'Nebyla nalezena žádná nabídka', '' ),
    'not_found_in_trash' => __( 'V koši nebyla nalezena žádná nabídka', '' ),
    'archives' => __( 'Archiv nabídek', '' ),
    'items_list' => __( 'Výpis nabídek', '' ),
  ];
  $args = [
    'label' => __( 'Nabídky', '' ),
    'labels' => $labels,
    'description' => '',
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true,
    'show_in_rest' => false,
    'rest_base' => '',
    'has_archive' => false,
    'show_in_menu' => true,
    'exclude_from_search' => true,
    'capability_type' => 'post',
    'capabilities' => array(
      'create_posts' => 'do_not_allow',
    ),
    'map_meta_cap' => true,
    'hierarchical' => false,
    'rewrite' => false,
    'query_var' => true,
    'menu_icon' => 'dashicons-testimonial',
    'supports' => array( 'title' ),
  ];
  return $args;
}
