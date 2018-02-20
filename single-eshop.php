<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use function Eshopiste\Helpers\get_eshop_category_link;

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

$context['breadcrumbs'] = [];

// Add category to breadcrumbs if exists
if ( $post_category = $post->get_field('category') ) {
	$post_category_link = get_eshop_category_link($post_category->term_id);
	$context['breadcrumbs'][$post_category->name] = $post_category_link;
}

$context['breadcrumbs'][$post->title] = '';

$age_field = get_field_object('age');
$post_age_label = null;
if ( $age_field ) {
	$post_age_label = $age_field['choices'][$age_field['value']];
}

$context['properties'] = [
	[
		'name' => 'Hodnota e-shopu',
		'value' => $post->get_field('price'),
		'postfix' => 'Kč bez DPH',
		'icon' => 'price-tag.svg',
	],
	[
		'name' => 'Obrat za poslední rok',
		'value' => $post->get_field('turnover'),
		'postfix' => 'Kč',
		'icon' => 'up-trend.svg',
	],
	[
		'name' => 'EBITDA za poslední rok',
		'value' => $post->get_field('ebitda'),
		'postfix' => 'Kč',
		'icon' => 'bar-chart.svg',
	],
	[
		'name' => 'Stáří e-shopu',
		'value' => $post_age_label,
		'icon' => 'cake.svg',
	],
	[
		'name' => 'Majitel e-shopu',
		'value' => $post->get_field('eshop_owner'),
		'icon' => 'user.svg',
	],
	[
		'name' => 'Návštěvnost za poslední rok',
		'value' => $post->get_field('traffic'),
		'postfix' => 'UN',
		'icon' => 'line-chart.svg',
	],
];

Timber::render( 'single-eshop.twig', $context );
