<?php
/*Ce fichier fait partie deSuperDIVIASSO, Divi child theme.

Toutes les fonctions de ce fichier seront chargées avant les fonctions de thème parent.
En savoir plus sur https://codex.wordpress.org/Child_Themes.

Remarque : cette fonction charge la feuille de style parent avant, puis la feuille de style du thème enfant
(laissez-le en place à moins que vous sachiez ce que vous faites.)
*/

if ( ! function_exists( 'suffice_child_enqueue_child_styles' ) ) {
	function SuperDIVIASSO_enqueue_child_styles() {
	    // loading parent style
	    wp_register_style(
	      'parente2-style',
	      get_template_directory_uri() . '/style.css'
	    );

	    wp_enqueue_style( 'parente2-style' );
	    // loading child style
	    wp_register_style(
	      'childe2-style',
	      get_stylesheet_directory_uri() . '/style.css'
	    );
	    wp_enqueue_style( 'childe2-style');
	 }
}
add_action( 'wp_enqueue_scripts', 'SuperDIVIASSO_enqueue_child_styles' );

/*Écrivez ici vos propres fonctions */

function getOrders() {
		global $sitepress, $wpdb;

		// get the adjusted post if WPML is active
		if ( isset( $sitepress ) && method_exists( $sitepress, 'get_element_trid' ) && method_exists( $sitepress, 'get_element_translations' ) ) {
			$trid         = $sitepress->get_element_trid( $post_id, 'post_product' );
			$translations = $sitepress->get_element_translations( $trid, 'product' );
			$post_id      = Array();
			foreach ( $translations as $lang => $translation ) {
				$post_id[] = $translation->element_id;
			}
		}

		// Query the orders related to the product

		$order_statuses        = array_map( 'esc_sql', (array) get_option( 'wpcl_order_status_select', array( 'wc-completed' ) ) );
		$order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
		$post_id               = array_map( 'esc_sql', (array) $post_id );
		$post_string           = "'" . implode( "', '", $post_id ) . "'";
// // AND o.post_status IN ( $order_statuses_string )
			
			
		$item_sales = $wpdb->get_results( $wpdb->prepare(
			"SELECT o.ID as order_id, oi.order_item_id,  oim.meta_value AS product_id FROM
			{$wpdb->prefix}woocommerce_order_itemmeta oim
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
			ON oim.order_item_id = oi.order_item_id
			INNER JOIN $wpdb->posts o
			ON oi.order_id = o.ID
			WHERE oim.meta_key = %s
			AND o.post_type NOT IN ('shop_order_refund')
			ORDER BY o.ID DESC",
			'_product_id'
		) );
/*		echo "SELECT o.ID as order_id, oi.order_item_id,  oim.meta_value AS product_id FROM
			{$wpdb->prefix}woocommerce_order_itemmeta oim
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
			ON oim.order_item_id = oi.order_item_id
			INNER JOIN $wpdb->posts o
			ON oi.order_id = o.ID
			WHERE oim.meta_key = '_product_id'
			AND o.post_type NOT IN ('shop_order_refund')
			ORDER BY o.ID DESC
			";
*/
		return $item_sales;
}


function sendEmails( $to, $subject, $body) {

	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$headers[] = 'From: Association Guidz <assoguidz@gmail.com>';
	
	// $to = "francois@fmosys.fr";
	// echo "sendmail to: ".$to. " subject:".$subject. " <br>".$body;
	
	$ret =  wp_mail( $to, $subject, $body, $headers );
	
	if ( is_wp_error( $ret ) ) {
		echo 'ERROR: ' . $ret->get_error_message();
	} 
	return $ret;

}


const 	CONST_VISIOCONFERENCE	= 'visio_conference';
const 	CONST_CONFERENCE		= 'conference';
const 	CONST_VISIT_INSITU		= 'visite_in_situ';

function TypeActivite( $type) {
	
	switch( $type) {
		case CONST_CONFERENCE:
			return "Conférence";
		case CONST_VISIOCONFERENCE:
			return "Visio Conférence";
		case CONST_VISIT_INSITU:
			return "Visite In Situ";
	}
}

function compare_date( $a,$b) {
	$t1 = strtotime($a);
    $t2 = strtotime($b);
    return $t2 - $t1;
}

function tri_date( $activites){
	$activites_sorted = array();
	$tb_tri = array();
	foreach($activites as $activite) {
		$date = '';
		if (!empty(get_post_meta($activite->ID, 'date', ""))) {
			$date = get_post_meta($activite->ID, 'date', "");
		}
		$tb_tri[$date[0]] = $activite->ID;
	}
	uksort( $tb_tri, 'compare_date');
	foreach( $tb_tri as $id_acti=>$value) {
		foreach($activites as $activite) {
			if( $activite->ID == $value) {
				$activites_sorted[] = $activite;
				break;
			}
		}
	}
	return $activites_sorted;
}

