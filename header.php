<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<?php 

$type = array(
	'numberposts' 	=> '-1',
	'post_status'	=> 'any',
	'post_type' 	=> 'activite',
	'orderby' 		=> 'date',
	'order' 		=> 'ASC',
	'post_status'    => 'any'
);

$mainTypes = array('Conférences', 'Visite in situ');

$posts = get_posts($type);

foreach ($mainTypes as $mainType) {

	$menu = get_term_by('name', 'menu', 'nav_menu');
	$menu_id = $menu->term_id;

	wp_update_nav_menu_item($menu_id, 0, array(
		'menu-item-title' => $mainType,
		'menu-item-object' => 'post',
		'menu-item-status' => 'publish',
		'menu-item-type' => 'activite',
	));

	if ($mainType == 'Conférences') {
		wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' => 'Conférences en salle',
			'menu-item-object' => 'post',
			'menu-item-status' => 'publish',
			'menu-item-type' => 'activite',
		));

		foreach ($posts as $post) {
			if ($post->post_title != "add") {
				$type = get_the_terms($post->ID, 'type_activite');
				if ( $type[0]->slug == 'conference') {
					wp_update_nav_menu_item($menu_id, 0, array(
						'menu-item-title' => $post->post_title,
						'menu-item-object' => get_permalink(),
						'menu-item-status' => 'publish',
						'menu-item-type' => 'activite',
					));
				}
			}
		}

		wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' => 'Visio conférence',
			'menu-item-object' => 'post',
			'menu-item-status' => 'publish',
			'menu-item-type' => 'activite',
		));

		foreach ($posts as $post) {
			if ($post->post_title != "add") {
				$type = get_the_terms($post->ID, 'type_activite');
				if ( $type[0]->slug == 'visio_conference') {
					wp_update_nav_menu_item($menu_id, 0, array(
						'menu-item-title' => $post->post_title,
						'menu-item-object' => get_permalink(),
						'menu-item-status' => 'publish',
						'menu-item-type' => 'activite',
					));
				}
			}
		}
	}

	foreach ($posts as $post) {
		if ($post->post_title != "add") {
			$type = get_the_terms($post->ID, 'type_activite');
			if ( $type[0]->slug == 'visite_in_situ') {
				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => $post->post_title,
					'menu-item-object' => get_permalink(),
					'menu-item-status' => 'publish',
					'menu-item-type' => 'activite',
				));
			}
		}
	}
}

wp_nav_menu( array( 
	'theme_location' => 'primary', 
	'container_class' => 'custom-menu-class' )
); 

?>

