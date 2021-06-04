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

// Liste des menues d'entête
$menu = get_term_by('name', 'menu', 'nav_menu');
$menu_id = $menu->term_id;

foreach ($mainTypes as $mainType) {

	if ($mainType == 'Visite in situ')
		$typeUrl = "visite_in_situ";
	else
		$typeUrl = "";

	// Ajoute les menus d'entête dans le menu principal
	$item_id = add_menu_item($mainType, $menu_id, $menu_id, $typeUrl);

	// Ajoute les sous-menus
	if ($mainType == 'Conférences') {

		$id = add_menu_item('Conférences en salle', $item_id, $menu_id, 'conference');

		add_post_item($id, $posts, $menu_id, 'conference');

		$id = add_menu_item('Visio conférence', $item_id, $menu_id, 'visio_conference');

		add_post_item($id, $posts, $menu_id, 'visio_conference');

	} else {
		
		add_post_item($item_id, $posts, $menu_id, 'visite_in_situ');

	}
}

wp_nav_menu( array( 
	'theme_location' => 'primary', 
	'container_class' => 'top-menu-nav' )
); 


function add_menu_item($item_post_title, $main_menu_id, $menu_id, $typeActivite) {
	// Si un menu existe on return l'id du menu
	if (wp_get_nav_menu_items($menu_id)) {
		foreach (wp_get_nav_menu_items($menu_id) as $item) {
			if ($item->title === $item_post_title) {
				$id = $item->ID;
				return $id;
			}
		}
	}

	// Si n'existe pas créer l'item et return l'id
	$item_id = wp_update_nav_menu_item($menu_id, 0, array(
		'menu-item-title' => $item_post_title,
		'menu-item-object' => 'post',
		'menu-item-parent-id' => $main_menu_id,
		'menu-item-url' => get_site_url() . "/produits?type_activite=" . $typeActivite,
		'menu-item-status' => 'publish'
	));

	return $item_id;
}

function add_post_item($main_menu_id, $posts, $menu_id, $typeActivite) {
	$id = 0;
	$add_item = true;

	foreach ($posts as $myPost) {
		// N'affiche pas l'activité add (Qui permet d'ajouter une activité)
		if ($myPost->post_name != "add") {
			// Récupère le type d'activité du post
			$type = get_the_terms($myPost->ID, 'type_activite');
			// Vérification du type de l'activité
			if ( $type[0]->slug == $typeActivite) {
				// Si un menu existe on return l'id du menu
				if (wp_get_nav_menu_items($menu_id)) {
					foreach (wp_get_nav_menu_items($menu_id) as $item) {
						if ($item->title == $myPost->post_title) {
							$id = $item->ID;
						}
					}
				}

				// Si il n'existe pas on le créer
				if ($add_item) {
					wp_update_nav_menu_item($menu_id, $id, array(
						'menu-item-title' => ($myPost->post_title),
						'menu-item-object' => 'post',
						'menu-item-parent-id' => $main_menu_id,
						'menu-item-url' => $myPost->guid,
						'menu-item-status' => 'publish'
					));
				}
			}
		}
	}
}
?>

