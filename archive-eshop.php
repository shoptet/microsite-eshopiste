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

use function Eshopiste\Helpers\get_post_meta_value_min_max;

global $wp_query;

$context = Timber::get_context();

$context['posts'] = new Timber\PostQuery();
$context['search_query'] = get_search_query();
$context['found_posts'] = $wp_query->found_posts;
$context['all_categories'] = Timber::get_terms('eshop_category');
$context['age_choices'] = get_field_object('field_5a43bd098e708')['choices']; // used post_name instead of post_excerpt
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
$context['checked_ages'] = isset($_GET[ 'age' ]) ? $_GET[ 'age' ] : [];
$context['selected_orderby'] = isset($_GET[ 'orderby' ]) ? $_GET[ 'orderby' ] : null;

// pass range and start values to context
foreach (['price', 'turnover', 'traffic'] as $query) {
	$context[ $query . '_range' ] = get_post_meta_value_min_max( get_queried_object()->name, $query );
	$context[ $query . '_start'] = [
		isset($_GET[ $query . '_min' ]) ? $_GET[ $query . '_min' ] : $context[$query . '_range']['min'],
		isset($_GET[ $query . '_max' ]) ? $_GET[ $query . '_max' ] : $context[$query . '_range']['max'],
	];
}

Timber::render( 'archive-eshop.twig', $context );
