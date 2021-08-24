<?php
/**
* Plugin Name: Assoguidz form
* Plugin URI: 
* Description: Sample activites plugin
* Version: 1.0
* Author: FMOSys
* Author URI: 
**/

add_action( 'init', 'wpm_custom_post_type', 0 );

function wpm_custom_post_type() {
    $labels = array(
        'name'                => _x( 'Activités', 'Post Type General Name' ),
        'singular_name'       => _x( 'Activité', 'Post Type Singular Name' ),
        'menu_name'           => __( 'Activités' ),
        'all_items'           => __( 'Toutes les activités' ),
        'view_item'           => __( 'Voir les activités' ),
        'add_new_item'        => __( 'Ajouter une activité' ),
        'add_new'             => __( 'Ajouter une activité' ),
        'edit_item'           => __( "Editer l'activité" ),
        'update_item'         => __( "Modifier l'activité" ),
        'search_items'        => __( 'Rechercher une activité' ),
        'not_found'           => __( 'Non trouvée'),
        'not_found_in_trash'  => __( 'Non trouvée dans la corbeille'),
    );
    $args = array(
        'label'               => __( 'Activité'),
        'description'         => __( 'Toutes les activités'),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
        'show_in_rest' => true,
        'show_in_ui' => true,
        'hierarchical' => false,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'activite'),
    );
    register_post_type('activite', $args );


	$url = 'dashicons-book-alt';  // See: https://developer.wordpress.org/resource/dashicons/#twitter
	$position = 0;
	add_menu_page('Activités', 'Activités', 'read', 'archives-des-activites', 'addActivitesMenuInAdmin', $url, $position);
}


function addActivitesMenuInAdmin() {
	
	// echo '<h1><a href="/archives-des-activites/">Activités</a></h1>';
	header( "location: /archives-des-activites");
}


//On crée 1 taxonomie personnalisée
add_action( 'init', 'wpm_add_taxonomies', 0 );

function wpm_add_taxonomies() {
    $label_type_activite = array(
        'name' => _x( "Type d'activité", 'taxonomy general name'),
        'singular_name' => _x( "Type d'activité", 'taxonomy singular name'),
        'search_items' => __( "Chercher un type d'activité"),
        'all_items'	=> __( 'Tous les type activités'),
        'edit_item'	=> __( "Modifier le type d'activité"),
        'update_item' => __( "Mettre à jour le type d'activié"),
        'add_new_item' => __( "Ajouter un nouveau type d'activité"),
        'new_item_name' => __( "Nom du type d'activité"),
        'separate_items_with_commas' => __( 'Séparer les actvités avec une virgule'),
        'menu_name' => __( "Type d'activité"),
    );

    $type_activite = array(
        'hierarchical'      => false,
        'labels'            => $label_type_activite,
        'show_ui'           => false,  // Pas d'affichage dans le menu admin....
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'date' ),
    );

    register_taxonomy( 'type_activite', 'activite', $type_activite );
}


function script() {
   // wp_enqueue_style( 'style', plugin_dir_url('') . 'AssoGuidz-Plugin/app.css');
   // wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
   // wp_enqueue_style('style', 'https://assoguidz.com/wp-includes/css/dashicons.min.css?ver=5.7.1');
    wp_enqueue_script("app", plugin_dir_url('') . 'AssoGuidz-Plugin/app.js');

    wp_enqueue_script("js", plugin_dir_url('') . 'AssoGuidz-Plugin/js/tinymce/tinymce.min.js');
    wp_enqueue_editor();
}

add_action( 'wp_enqueue_scripts', 'script');

