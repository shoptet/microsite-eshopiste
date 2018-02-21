<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use function Eshopiste\Helpers\get_post_meta_value_min_max;
use function Eshopiste\Helpers\get_post_meta_sum;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['breadcrumbs'] = [
	$post->title => '',
];

if ( !is_front_page() ){
  Timber::render( array( 'page-' . $post->post_name . '.twig', 'single.twig' ), $context );
  return;
}

/**
 * Homepage
 */

$posts_for_sale_query = [
  'post_type' => 'eshop',
  'posts_per_page' => 6,
  'meta_query' => [[ 'key' => 'type', 'value' => 0 ]],
];

$posts_for_invest_query = [
  'post_type' => 'eshop',
  'posts_per_page' => 3,
  'meta_query' => [
    'relation' => 'OR',
    [ 'key' => 'type', 'value' => 1 ],
    [ 'key' => 'type', 'value' => 2 ],
  ],
];

$posts_for_buyer_query = [
  'post_type' => 'post',
  'posts_per_page' => -1,
  'cat' => 10,
];

$posts_for_seller_query = [
  'post_type' => 'post',
  'posts_per_page' => -1,
  'cat' => 11,
];

// map post ids to timber post objects
$advice_posts = [];
if ( isset( get_fields('options')['homepage_posts_ids'] ) ) {
  foreach ( get_fields('options')['homepage_posts_ids'] as $post_id ) {
    $advice_posts[] = new TimberPost( $post_id );
  }
}

$context['type_choices'] = [
	0 => 'Prodej',
	2 => 'Investice',
];

if ( isset( get_fields('options')['homepage_category_for_sale_1'] ) ) {
  $context['term_for_sale_1'] = new TimberTerm( get_fields('options')['homepage_category_for_sale_1'] );
}

if ( isset( get_fields('options')['homepage_category_for_sale_2'] ) ) {
  $context['term_for_sale_2'] = new TimberTerm( get_fields('options')['homepage_category_for_sale_2'] );
}

if ( isset( get_fields('options')['homepage_category_for_sale_2'] ) ) {
  $context['term_for_invest'] = new TimberTerm( get_fields('options')['homepage_category_for_invest'] );
}

$context['turnover_range'] = get_post_meta_value_min_max( 'eshop', 'turnover' );
$context['posts_for_sale'] = new Timber\PostQuery( $posts_for_sale_query );
$context['posts_for_invest'] = new Timber\PostQuery( $posts_for_invest_query );
$context['advice_posts'] = $advice_posts;
if ( get_fields('options')['homepage_testimonial'] ) {
  $context['testimonials'] = get_fields('options')['homepage_testimonial'];
}
$context['posts_for_buyer'] = new Timber\PostQuery( $posts_for_buyer_query );
$context['posts_for_seller'] = new Timber\PostQuery( $posts_for_seller_query );

$eshop_count = wp_count_posts( 'eshop' );
$context['eshop_count'] = $eshop_count->publish + $eshop_count->draft;

$context['total_turnover'] = get_post_meta_sum( 'eshop', 'turnover' );

$eshop_bid_count = wp_count_posts( 'eshop_bid' );
$context['eshop_bid_count'] = $eshop_bid_count->publish + $eshop_bid_count->draft;

Timber::render( 'home.twig', $context );
