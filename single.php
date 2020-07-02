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

 use function Eshopiste\Helpers\truncate;

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

$context['meta_description'] = truncate( strip_tags( $post->content ), 200 );

$context['breadcrumbs'] = [
	$post->title => '',
];

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
