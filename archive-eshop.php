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

$context = Timber::get_context();

$context['posts'] = new Timber\PostQuery();
$context['all_categories'] = Timber::get_terms('eshop_category');
$context['type_choices'] = [
	'Koupit e-shop',
	'Půjčit e-shopu',
	'Investovat do e-shopu',
];
$context['checked_categories'] = isset($_GET[ 'category' ]) ? $_GET[ 'category' ] : [];
$context['checked_types'] = isset($_GET[ 'type' ]) ? $_GET[ 'type' ] : [];

Timber::render( 'archive-eshop.twig', $context );
