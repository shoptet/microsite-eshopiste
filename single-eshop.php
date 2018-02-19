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


Timber::render( 'single-eshop.twig', $context );
