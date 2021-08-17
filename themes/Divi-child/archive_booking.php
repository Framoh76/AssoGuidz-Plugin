<?php
/* Template Name: archive_booking */ 

$ID = get_current_user_id();
if ( $ID == null) {
	$redirect = add_query_arg( 'redirect_to', get_permalink( $post->ID ), wp_login_url() );
                // redirect to the login page and then to the requested page...
    wp_redirect( $redirect );
	exit();
}

// var_dump( $_REQUEST);

// get_header();

//setOrders();

?>

<script type="text/javascript">
function imprimer_page(){
  window.print();
}
</script>


<?php 

if( isset( $_REQUEST['presence'])) {

	$listUsers = explode( "||", $_REQUEST['liste']);
	// var_dump( $listUsers);
	$titlePresence = $_REQUEST['subject'];
	
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-12">';

	echo "<h1>Liste des inscrits</h1>";
	echo "<h2>".$titlePresence."</h2>";
	
	echo '<table style="width:100%">';
	echo '<tr><th width="250">Nom Prénom</th><th width="200">email</th><th width="200">téléphone</th><th width="200">Présence</th></tr>';
	
	foreach( $listUsers as $user) {
		$detailUsers = explode( ";", $user);
		echo "<tr>";

		foreach( $detailUsers as $userDetail) {
			echo "<td>".$userDetail."</td>";
		}
			echo "<td>&nbsp;</td>";
		
		echo "</tr>";
	}
	echo "</table>";
	$countInscrits = count( $listUsers)-1;
	echo "<p><b>".$countInscrits. " inscrits</b></p>";
	
	echo '<form>';
	echo '<input id="impression" name="impression" type="button" onclick="imprimer_page()" value="Imprimer cette page" />';
	echo '</form>';

	
	echo '<br><p><a href="javascript:history.back();">retour</a></p>';

	echo '</div>';
	echo '</div>';
	echo '</div>';

	die;

}
else {
	if( isset( $_REQUEST['sendmail'])) {
		
		$listUsersEmails = explode( ";", $_REQUEST['to']);
		$totalEmailSent = 0;
		$countEmail = 0;
		
		foreach( $listUsersEmails as $emailUser) {
			if( $emailUser != '') {
				$countEmail ++;
				$ret = sendEmails( $emailUser, $_REQUEST['subject'], stripslashes( $_REQUEST['content']));
				
				$totalEmailSent += $ret;
			}
		}
		echo "<h2>".$totalEmailSent . " emails envoyées sur ". $countEmail."</h2>";
		
	}
}


if( isset( $_REQUEST['export_liste'])) {
	$idActivite = $_REQUEST['activite_id']; 
	// echo "ID:".$idActivite;
	
	$titre_activite = "";
	$activite = get_post( $idActivite);
	$titre_activite = $activite->post_title;
	
	$termsTypeActivite = get_the_terms($idActivite, 'type_activite'); 
	if ( $termsTypeActivite != null) {
		$type_activite = $termsTypeActivite[0]->name;
	}
	
	echo '<div class="container">';

	echo '<div class="row">';
	echo '<div class="col-12">';
	echo "<h1>Réservations pour  : ". $type_activite. ": <b>". $titre_activite."</b></h1>";
	$lieu = get_post_meta($idActivite, 'lieu', true);
	$date = get_post_meta($idActivite, 'date', true);
	$dateF = DateTime::createFromFormat( 'Y-m-d', $date);
	if( $dateF){
		$date = $dateF->format('d-m-Y');
	}
	$sujet_mail = $activite->post_title. " - ".$date;

	$horaire = get_post_meta($idActivite, 'horaire', true);
	echo "<h3>".ucfirst($lieu)." , le ". $date. " à ". $horaire."</h3>";
	
	$titre_presence = $titre_activite. ucfirst($lieu)." , le ". $date. " à ". $horaire;
	
	echo '</div>';
	echo '</div>';

	$list_users_ids = $_REQUEST['liste_users_ids'];
	$usersIds = explode( ",", $list_users_ids );
	
	echo '<table style="width:100%">';
	echo '<tr><th width="250" style="text-align:left;">Nom Prénom</th><th width="200" style="text-align:left;">Email</th><th width="200" style="text-align:left;">Coordonnées</th></tr>';

	$listUsersEmails = "";
	$listPresence = "";
	
	foreach( $usersIds as $userId) {
		if( $userId != '') {
			$user_customer = get_user_by('id', $userId);
			$userName = $user_customer->display_name;
			$listPresence .= $userName.";";
			
			$userEmail = $user_customer->user_email;
			$listUsersEmails .= $userEmail.";";
			
			$listPresence .= $userEmail.";";
			$phoneOnly = true;
			$listPresence .= getParticipantDetails( $userEmail, $phoneOnly);
			
			$listPresence .= "||";
			
			echo '<tr>';
			echo '<td>';
			echo $userName;
			echo '</td>';
			echo '<td>';
			echo $userEmail;
			echo '</td>';
			echo '<td>';
			echo getParticipantDetails( $userEmail);
			echo '</td>';
			echo '</tr>';
		}
	}
	echo '</table>';
	?>
	<br><b>Exporter la liste de présence</b>
	<form id="presence" method="POST" action="" > 
		<input type="hidden" id="presence" name="presence" value="1">
		<input type="hidden" id="subject" name="subject" value="<?php echo $titre_activite; ?>">
		<input type="hidden" id="liste" name="liste" value="<?php echo $listPresence; ?>">
		<button id="export" name="export">Exporter</button>
	</form>


	<br><b>Envoyez un email à tous les inscrits</b>
	<form id="sendmail" method="POST" action="" > 
		<input type="hidden" id="sendmail" name="sendmail" value="1">
		<input type="hidden" id="subject" name="subject" value="<?php echo $sujet_mail; ?>">
		<input type="hidden" id="to" name="to" value="<?php echo $listUsersEmails; ?>">
	
	  <?php 
		echo '<div class="row">';
		echo '<div class="col-9">';
	
			$name = 'content';
			$settings =   array(
				'wpautop' => true, // use wpautop?
				'media_buttons' => false, // show insert/upload button(s)
				'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
				'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
				'tabindex' => '',
				'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
				'editor_class' => '', // add extra class(es) to the editor textarea
				'teeny' => false, // output the minimal editor config used in Press This
				'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
				'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()

				'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
			);
			
			wp_editor($content, $name , $settings);
			
		echo '</div>';
		echo '<div class="col-3"><button id="send" name="send">Envoyer</button>';
		echo '</div>';
		
		?>
		<script> 
		tinymce.init({
		menubar:false,
		statusbar:false,
		selector: "textarea",
		plugins: [
		],
		toolbar: "styleselect | bold italic | alignleft aligncenter alignright alignjustify"
		}); 
		</script>
	</form>
	<?php 
	
	echo "<br><br><b>Liste des adresses mails : </b><br>".$listUsersEmails;
	echo '</div>';
}
else {
	
	$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1'
	);

	//isset($_REQUEST['type_activite']) ? $_REQUEST['type_activite'] : 'any'

	$activites = get_posts($type);

?>

<div class="container">
	<div class="row">

		<?php
		
		
		foreach($activites as $activite) {
			$termsTypeActivite = get_the_terms($activite->ID, 'type_activite'); 
			if ( $termsTypeActivite != null) {
				$type_activite = $termsTypeActivite[0]->name;
			}

			//echo "Id: " . $activite->ID . "<br />";
			//echo "Titre : " . $activite->post_title . "<br />";
			//echo "Type d'activité : " . $type_activite . "<br />";
			//echo "Type d'activité demandé : " . $_REQUEST['type_activite'] . "<br />";

			if ($type_activite == $_REQUEST['type_activite'] && $activite->post_name != "add") {
			?>

				<div class="col-4">
					<h1 style="text-align: center"><?php echo $activite->post_title ?></h1>
					<p><?php echo $activite->post_content ?></p>
					<p></p>
					<div class="row">
						<div class="col-6">
							<a href="<?php echo $activite->guid ?>"><span class="dashicons dashicons-info"></span></a>
						</div>
						<div class="col-6">
							<?php 
								$id_produit = get_post_meta($activite->ID, 'id_product', true);
								$produit = get_post($id_produit);
							?>
							<a 
								href="?add-to-cart=<?php echo $produit->ID ?>&type_activite=<?php echo $_REQUEST['type_activite'];?>" 
								data-quantity="1"
								class="button product_type_simple add_to_cart_button ajax_add_to_cart"
								data-product_id==<?php echo $produit->ID ?>	
								rel="nofollow"><span class="dashicons dashicons-cart"></span>
							</a>
						</div>
						
					</div>
				</div>

			<?php
			}
		}
		
		
		
		?>

	</div>
</div>


	<table class='table' style="width:100%" border="1">
			<tr>
				<th style="text-align:left;">Activité</th>
			<!--	<th>Nombre</th>  -->
			<!--	<th>Status</th>  -->
				<th style="text-align:left;">Réservé par</th>
				<th style="text-align:left;">Total Payé</th>
				<th style="text-align:left;">Nb personnes</th>
			</tr>

<?php 
// var_dump($GLOBALS['wc_bookings']);

// https://www.businessbloomer.com/woocommerce-easily-get-order-info-total-items-etc-from-order-object/
	$orders = getOrders();

	// var_dump( $orders);
	/*
	foreach( $orders as $order) {
		echo "<p>ORDER ID:".$order->order_id."</p>";
		echo "<p>ORDER ITEM ID:".$order->order_item_id."</p>";
		echo "<p>PRODUCT ID:".$order->product_id."</p>";
		
		$orderProduct = new WC_Order($order->order_id);
		// var_dump( $orderProduct);
		echo "<p>CUSTOMER: ".$orderProduct->get_user_id()."</p>";
		$user = $orderProduct->get_user();
//		var_dump( $user);
		echo "<p>CUSTOMER USER : ".$user->display_name."</p>";
		echo "<p>CUSTOMER NICE NAME : ".$user->user_nicename."</p>";
		echo "<p>CUSTOMER EMAIL : ".$user->user_email."</p>";
		
		echo "<br>DATE CREATION:" .$orderProduct->get_date_created();
		echo "<br>DATE COMPLETED:" .$orderProduct->get_date_completed();
		echo "<br>DATE PAIEMENT:" .$orderProduct->get_date_paid();

		foreach ( $orderProduct->get_items() as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			$product = $item->get_product();
			$product_name = $item->get_name();
			$quantity = $item->get_quantity();
			$subtotal = $item->get_subtotal();
			$total = $item->get_total();
			echo "<br>".$quantity . "   ".$product_name."   -> ".$total;
		}
		
		echo "<hr>";
	}
	*/
	
		
	foreach($activites as $activite) {
		if( $activite->post_name != "add" ) {
			$activite_id = $activite->ID;
			
			$product_id = get_post_meta($activite->ID, 'id_product', true);
			$produit = get_post($product_id);
			
			$customer = array();
			$customerNames = '';
			$status = '';
			$costTotal = 0;
			$countCustomer = 0;
			$liste_ids = "";
			
			foreach( $orders as $order) {
				if( $product_id == $order->product_id) {
					$orderProduct = new WC_Order($order->order_id);
					
					// echo "<br>DATE COMPLETED:" .$orderProduct->get_date_completed();
					// echo "<br>DATE PAIEMENT:" .$orderProduct->get_date_paid();
					if( ($orderProduct->get_date_completed() != '') && ($orderProduct->get_date_paid() != '')) { // SI COMPLET ET PAYE!!!!
						$customer = $orderProduct->get_user();
						// echo "<p>CUSTOMER USER : ".$customer->display_name."</p>";

						$liste_ids .= $customer->id.",";
						$userlogin = $customer->user_login;
						
						// $customerNames .= '<a href="/author/'. $userlogin. '">'.$customer->name.'</a>,';
						$customerNames .= '<a target="_blank" href="/wp-admin/user-edit.php?user_id='. $customer->id. '">'.$customer->display_name.'</a>,';
					//	$status .= wc_bookings_get_status_label( $bookingReservation->get_status() ) .",";

						foreach ( $orderProduct->get_items() as $item_id => $item ) {
							$quantity = $item->get_quantity();
							$subtotal = $item->get_subtotal();
							$total = $item->get_total();
						}
						$costTotal += $total;
						$countCustomer++;
					}
				}
			}
	?>
			<tr>
				<td><?php echo $activite->post_title ?></td>
			<!--	<td><?php echo $product_id ?></td>  -->
			<!--	<td><?php echo $status; ?></td>  -->
				<td><?php echo $customerNames;  ?>
				</td>
				<td><?php echo $costTotal.'€'; ?></td>
				<td><?php echo $countCustomer; ?></td>
				<td><button class="btn btn-light">
					<a target="_blank" href="<?php echo '/reservations?export_liste=1&activite_id='.$activite_id.'&product_id='. $product_id. '&liste_users_ids='.$liste_ids; ?>"> EXPORTER </a></button>
			</tr>
	<?php 
		}
	}
}


function setOrders() {
global $woocommerce;

  $address = array(
      'first_name' => 'guide2',
      'last_name'  => 'guide2',
      'company'    => '',
      'email'      => 'francois.mohier@neuf.fr',
      'phone'      => '0614415124',
      'address_1'  => '123 Main st.',
      'address_2'  => '104',
      'city'       => 'San Diego',
      'state'      => 'Ca',
      'postcode'   => '92121',
      'country'    => 'US'
  );

  // Now we create the order
  $order = wc_create_order();

  // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
  $order->add_product( get_product('813'), 1); // This is an existing SIMPLE product
  
  $order->set_address( $address, 'billing' );
  //
  $order->calculate_totals();
  $order->update_status("Completed", 'Imported order', TRUE);  
  
  $order->set_customer_id(3);
  // 789 
}



function getParticipantDetails( $userEmail, $phoneOnly=false) {
/* 	$record_id  = 1;
	$participant_values = Participants_Db::get_participant( $record_id );
	var_dump( $participant_values );
 */	
	$participantList = Participants_Db::get_id_list( array( 'filter' => 'email='.$userEmail ) );
	
	if( count( $participantList) == 0) {
		
		$details = "NC";
	}
	else {
		// var_dump( $participantList);
		$participantId = $participantList[0];
		// var_dump( $participantId);
		$participant_values = Participants_Db::get_participant( $participantId );
		// var_dump( $participant_values);
		// private_id"]=> string(7) "C63PZ9Z" ["first_name"]=> string(8) "Francois" ["last_name"]=> string(6) "Mohier" ["address"]=> string(15) "9 rue G. Braque" ["city"]=> string(8) "le havre" ["state"]=> string(0) "" ["country"]=> string(6) "France" ["zip"]=> string(5) "76600" ["phone"]=> string(14) "06 14 41 51 24
		$details = $participant_values["phone"]. " : ". $participant_values["private_id"]. " : ". $participant_values["address"]. ",". $participant_values["zip"]. " ".$participant_values["city"]; 
		if( $phoneOnly)
			$details = $participant_values["phone"];
	}
	
	return $details;
}

?>

<script type="tex