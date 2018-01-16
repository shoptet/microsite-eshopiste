<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

global $wp_query;

$context = Timber::get_context();

$context['posts'] = new Timber\PostQuery();
$context['found_posts'] = $wp_query->found_posts;
$context['archive_link'] =  get_post_type_archive_link( get_post_type() );
$context['all_categories'] = Timber::get_terms('eshop_category');
$context['type_choices'] = [
	0 => 'Koupit e-shop',
	1 => 'Půjčit e-shopu',
	2 => 'Investovat do e-shopu',
];
$context['order_choices'] = [
	'date_desc' => 'Nejnověji přidáno',
	'price_asc' => 'Nejlevnější',
	'price_desc' => 'Nejdražší',
	'turnover_asc' => 'Nejmenší obrat',
	'turnover_desc' => 'Největší obrat',
	'age_desc' => 'Nejstarší',
	'age_asc' => 'Nejmladší',
];
$context['checked_categories'] = isset($_GET[ 'category' ]) ? $_GET[ 'category' ] : [];
$context['checked_types'] = isset($_GET[ 'type' ]) ? $_GET[ 'type' ] : [];
$context['selected_orderby'] = isset($_GET[ 'orderby' ]) ? $_GET[ 'orderby' ] : null;

Timber::render( 'archive-eshop.twig', $context );
