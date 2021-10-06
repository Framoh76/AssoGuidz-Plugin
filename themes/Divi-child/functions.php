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
	      get_template_directory_uri() . '/style.css?id=122'
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
			
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

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

function getOrdersNew( ) {
	global $wpdb;
	
	$sql = "
	SELECT
        p.ID as order_id,
        p.post_date,
        max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
        max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
        max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
        max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
        max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
        max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
        max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
        max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
        max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
        max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
        max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
        max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
        max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
        max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
        max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
        max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
        max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,
        max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
        ( select group_concat( order_item_name separator '<br>' ) from wp_woocommerce_order_items where order_id = p.ID ) as order_items
    FROM
        wp_posts p 
        join wp_postmeta pm on p.ID = pm.post_id
        join wp_woocommerce_order_items oi on p.ID = oi.order_id
    WHERE
        post_type = 'shop_order' and ".
     //   post_date BETWEEN '2018-07-01' AND '2021-08-01' and
     "   post_status = 'wc-completed' 
    group by
        p.ID  
	ORDER BY `billing_email` ASC, paid_date DESC
	";
	
	$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
	// echo $sql;
	$item_sales = $wpdb->get_results( $sql);
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


const 	MENU_CONFERENCE		= "Conférences"; // 1140
const 	MENU_CONF_EN_SALLE 	= "Conférences en salle"; // 1141
const 	MENU_VISIOCONF		= "Visio conférences"; // 1142;
const 	MENU_VISIT_INSITU	= "Visites in situ"; // 1143;


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
    return $t1 - $t2;
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


// ID of the menu item with name $title
function getMenuItemId( $main_menu_id, $title) {
	//echo "<br>".$title;
	foreach (wp_get_nav_menu_items($main_menu_id) as $item) {
		// echo "MEENU".$item->title;
		if ( strtolower($item->title) === strtolower($title)) {
			$id = $item->ID;
			// echo "<br>".$title."ID:".$id;
			return $id;
		}
	}
	return null;
}


// ID of the main primary menu
function getMainMenuId() {
	// Récupération de l'ID du menu primary de wordpress qui doit s'appeler "Principal"...
	$menu = get_term_by('name', 'Principal', 'nav_menu');
	if( !$menu ) {
		echo '<br><br><font color="red">Le menu principal de Wordpress doit se nommer "Principal" pour que cela fonctionne...Merci de modifier le nom du menu</font></br>';
		die;
	}
	$menu_id = $menu->term_id;  // $menu_id contient l'ID du menu qui doit s'appeler "Principal"
	return $menu_id;
}






/* RELOAD ACTIVITES dans le menu tous les jours...... */
function activite_menu_desactivate() {
    wp_clear_scheduled_hook( 'activite_menu_cron' );
}
 
add_action('init', function() {
    add_action( 'activite_menu_cron', 'activite_menu_run_cron' );
    register_deactivation_hook( __FILE__, 'activite_menu_desactivate' );
 
    if (! wp_next_scheduled ( 'activite_menu_cron' )) {
        wp_schedule_event( time(), 'daily', 'activite_menu_cron' );
        // wp_schedule_event( time(), 'hourly', 'activite_menu_cron' );
    }
});
 

function activite_menu_run_cron() {
    // Rechargement du menu en fonction des dates des activités....
	$type = array(
		'numberposts' 	=> '-1',
		'post_status'	=> 'any',
		'post_type' 	=> 'activite',
		'orderby' 		=> 'date',
		'order' 		=> 'ASC',
		'post_status'    => 'any'
	);

	$activites = get_posts($type); // Toutes les activites....
	$menu_id = getMainMenuId();  // $menu_id contient l'ID du menu qui doit s'appeler "Principal"

	$activites = tri_date( $activites); // TRI par date....
	
	$deletedAct = 'AUCUNE ';
	
	// On supprime toutes les activités des menus.....
	foreach ($activites as $activite) {
		// N'affiche pas l'activité add (Qui permet d'ajouter une activité)
		$idActivite = 0; // Id de l'item du menu a ajouter
		
		if ($activite->post_name != "add") {
			if (wp_get_nav_menu_items($menu_id)) {
				foreach (wp_get_nav_menu_items($menu_id) as $item) {
					// if ($item->title == $activite->post_title ||  $titleOrigin == $activite->post_title) {
					if ($item->title == $activite->post_title ) {
						$idActivite = $item->ID;	// Item de menu deja existant...
						wp_delete_post( $idActivite);
						$deletedAct .= $idActivite.";";
					}
				}
			}
		}
	}
	
	$main_menu_id = getMainMenuId(); // recupere le main menu ID et si n'existe pas....MESSAGE d'erreur...

	$ID_CONF_EN_SALLE = getMenuItemId( $main_menu_id, MENU_CONF_EN_SALLE); // 1141;
	$ID_VISIOCONF = getMenuItemId( $main_menu_id, MENU_VISIOCONF); // 1142;
	$ID_VISIT_INSITU = getMenuItemId( $main_menu_id, MENU_VISIT_INSITU); // 1143;


	// Puis on les ajoute....
	$deletedAct .= 'AUCUNE ';
	foreach ($activites as $activite) {
		// N'affiche pas l'activité add (Qui permet d'ajouter une activité)
		$idActivite = 0; // Id de l'item du menu a ajouter
		
		if ($activite->post_name != "add") {
			// Récupère le type d'activité du post
			$type = get_the_terms($activite->ID, 'type_activite');
			switch( $type[0]->slug) {
				case CONST_VISIOCONFERENCE: 
					$parent_menu_id = $ID_VISIOCONF;
					break;
				case CONST_CONFERENCE: 
					$parent_menu_id = $ID_CONF_EN_SALLE;
					break;
				case CONST_VISIT_INSITU: 
					$parent_menu_id = $ID_VISIT_INSITU;
					break;
			}
			echo "type:".$type[0]->slug." PARE:".$parent_menu_id;
			// echo "<br>ACTIVITE:".$activite->post_title;
			// Si un menu existe on return l'id du menu
			if (wp_get_nav_menu_items($menu_id)) {
				foreach (wp_get_nav_menu_items($menu_id) as $item) {
					// if ($item->title == $activite->post_title ||  $titleOrigin == $activite->post_title) {
					if ($item->title == $activite->post_title ) {
						$idActivite = $item->ID;	// Item de menu deja existant...
					}
				}
			}
			echo "<br>   ID:".$idActivite;
			// echo "  ID activité".$activite->ID;
			$visibility = get_post_meta($activite->ID, 'visibility', true);
			$date_expiration = get_post_meta($activite->ID, 'expiration', true);
			$count_jour = -100000;
			if( $date_expiration != '') {
				$datetime_expiration = date_create($date_expiration);
				
				$date_aujourdhui = date('Y-m-d', time());
				$datetime_aujourdhui = date_create($date_aujourdhui);

				$interval = date_diff($datetime_expiration, $datetime_aujourdhui);
				$count_jour = $interval->format('%r%a'); // %r (negative and positive) %a(jour)
			}
			// echo " VISI: ".$visibility; 
			// echo " COUNT JOUR: ".$count_jour; 
			
			if( $count_jour < 0 && $visibility == "true") {
				// echo " ----> VISIBLE ".$idActivite.":".$activite->post_title. $visibility.":".$count_jour;
				

				$ret = wp_update_nav_menu_item($menu_id, $idActivite, array(
				'menu-item-title' => $activite->post_title,
				'menu-item-object' => 'post',
				'menu-item-parent-id' => $parent_menu_id,
				'menu-item-url' => $activite->guid,
				'menu-item-status' => 'publish'
				));
				// echo "  GUID:    ".$activite->guid."   RET:".$ret;
			}
			else {
				//  echo " ----> INVVISIBLE ".$idActivite.":".$activite->post_title. $visibility.":".$count_jour; 
				if( $idActivite != 0) {
					// echo "DELETE POST".$idActivite;
					wp_delete_post( $idActivite);
					$deletedAct = $idActivite.";";
				}
			}
		}
	}

	sendEmails( 'info@fmosys.fr', 'AssoGuidz: reload menu...', 'reload en cours....DELETE: '.$deletedAct );
}


function countParticipants( $activite) {
	$activite_id = $activite->ID;
	
	$product_id = get_post_meta($activite->ID, 'id_product', true);
	$produit = get_post($product_id);
	
	$customer = array();
	$customerNames = '';
	$status = '';
	$costTotal = 0;
	$countCustomer = 0;
	$liste_ids = "";
	$orders = getOrders();
	
	foreach( $orders as $order) {
		if( $product_id == $order->product_id) {
			$orderProduct = new WC_Order($order->order_id);
			
			if( ($orderProduct->get_date_completed() != '') && ($orderProduct->get_date_paid() != '')) { // SI COMPLET ET PAYE!!!!
				$countCustomer++;
			}
		}
	}
	return $countCustomer;
}