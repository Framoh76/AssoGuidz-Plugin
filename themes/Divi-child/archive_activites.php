<?php
/* Template Name: archive_activites */ 
?>
<?php
$ID = get_current_user_id();
if ( $ID == null) {
	$redirect = add_query_arg( 'redirect_to', get_permalink( $post->ID ), wp_login_url() );
                // redirect to the login page and then to the requested page...
    wp_redirect( $redirect );
	exit();
}

get_header();


is_add_activite_created();

// Test si on a bien une activité avec le slug "add": nécessaire pour créer a minima un nouveau activité
function is_add_activite_created() {
	
	$type = array(
		'numberposts' 	=> '-1',
		'post_status'	=> 'any',
		'post_type' 	=> 'activite',
		'orderby' 		=> 'date',
		'order' 		=> 'ASC',
		'post_status'    => 'any'
	);
	$activites = get_posts($type);
	foreach ($activites as $activite) {
		if ($activite->post_name == "add") {
			return true;
		}
	}
	
	echo '<br><br><font color="red">Il est nécessaire de crééer une Activité avec le slug "add", Merci de créér cette activité</font></br>';
	die;
}


$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1',
);

$activites = get_posts($type);
$activites = tri_date( $activites);

//var_dump($activites);
?>
		<table class='table' style="width:100%">
			<tr>
				<th style="text-align:left;">Lien</th>
				<th style="text-align:left;">Nom de l'activité</th>
				<th style="text-align:left;">Date</th>
				<th style="text-align:left;">Sous-titre</th>
				<th style="text-align:left;">Prix</th>
				<th style="text-align:left;">Image</th>
				<!-- <th>Contenu</th> -->
				<th style="text-align:left;">Type d'activité</th>
				<th style="text-align:left;">Réservations</th>
				<th style="text-align:left;">Visibile <br>sur le site</th>
				<th style="text-align:left;">Date d'expiration</th>
			</tr>

	<?php
			foreach ($activites as $activite) {
				if ($activite->post_title != "add") {
					echo "<tr>";
					
						echo '<td><a href="/activite/' . $activite->post_name . '?action=display"><span class="dashicons dashicons-external"></span></a></td>';

						echo '<th><a href="/activite/' . $activite->post_name . '?action=edit">' .  $activite->post_title . "</a></th>";

						if (!empty(get_post_meta($activite->ID, 'date', ""))) {
							$date = get_post_meta($activite->ID, 'date', true);
							$dateF = DateTime::createFromFormat( 'Y-m-d', $date);
							if( $dateF){
								$date = $dateF->format('d-m-Y');
								echo "<td>" . $date . "</td>";
							}
							else {
								echo "<td>" . "</td>";
							}
							
						} else {
							echo "<td>-</td>";
						}

						if (!empty(get_post_meta($activite->ID, 'sub_title', true))) {
							echo "<td>" . get_post_meta($activite->ID, 'sub_title', true) . "</td>";
						} else {
							echo "<td>-</td>";
						}

						if (!empty(get_post_meta($activite->ID, 'prix', true))) {
							echo "<td>" . get_post_meta($activite->ID, 'prix', true) . "€</td>";
						} else {
							echo "<td>gratuit</td>";
						}

						if (!empty(get_post_meta($activite->ID, 'image', true))) {
							echo '<td><img src="' . get_post_meta($activite->ID, 'image', true) . '" alt="image" width="100" height="100"></td>';
						} else {
							echo "<td>-</td>";
						}

						// echo "<td>" . $activite->post_content . "</td>";

						//Type
						$termsTypeActivite = get_the_terms($activite->ID, 'type_activite'); 
						if ( $termsTypeActivite != null) {
							$type_activite = $termsTypeActivite[0]->name;
							echo '<td>' . $type_activite . '</td>';
						} else {
							echo '<td></td>';
						}

						$quantity = get_post_meta($activite->ID, 'quantite', true);
						if ( $quantity > 0) {
							$reserved = 2;
							$customer_email = '';
							$user_id = -1;
							$product_id = get_post_meta($activite->ID, 'id_product', true);
							$reserved = wc_customer_bought_product($customer_email, $user_id, $product_id); 
							
							// reserve_product( $product_id);
							
							if( $reserved == '') $reserved = '0';
							$reservation = $reserved . "/" . $quantity;
							echo '<td>'.$reservation.'</td>';
						} else {
							echo '<td>-</td>';
						}

						if (get_post_meta($activite->ID, 'visibility', true) == "true") {
							echo '<td><input type="checkbox" id="visibility" name="visibility" checked disabled </td>';
						} else {
							echo '<td><input type="checkbox" id="visibility" name="visibility" disabled </td>';
						}

						if (!empty(get_post_meta($activite->ID, 'expiration', ""))) {
							$date = get_post_meta($activite->ID, 'expiration', true);
							$dateF = DateTime::createFromFormat( 'Y-m-d', $date);
							if( $dateF) {
								
								$date = $dateF->format('d-m-Y');
								echo "<td>" . $date . "</td>";
							}
						} else {
							echo "<td>jamais</td>";
						}
					echo "</tr>";
				}
			}
		?>
		</table>
		<button class="btn btn-light"><a href="<?php get_site_url(); ?>/activite/add/?action=edit"> Ajouter une activite </a></button>
		<button class="btn btn-light"><a href="<?php get_site_url(); ?>/reservations"> Voir  les réservations </a></button>
	</body>
</html>


<?php 

/* Function pour reserver manuellement une activité.... */
function reserve_product( $product_id, $user_id = '', $date_reservation = '2021-06-01') {
	
	$new_booking_data = array('product_id'  => $product_id, 'user_id'=>$user_id, 'start_date'=>$date_reservation, 'end_date'=>$date_reservation );

	// Create it
	$new_booking = get_wc_booking( $new_booking_data );
	$new_booking->create( $status );

	return $new_booking;
}

add_action( 'woocommerce_booking_in-cart_to_paid', 'auto_create_followup_booking' );
add_action( 'woocommerce_booking_unpaid_to_paid', 'auto_create_followup_booking' );
add_action( 'woocommerce_booking_confirmed_to_paid', 'auto_create_followup_booking' );

?>
