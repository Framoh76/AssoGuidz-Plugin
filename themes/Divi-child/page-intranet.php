<?php

if ( is_front_page() ) {

}

// var_dump( $_REQUEST);

if (isset($_REQUEST['form_action'])) {
	$nonce = $_REQUEST['idguidz'];
	if ( ! wp_verify_nonce( $nonce, 'my-nonce' ) ) {
		die( __( 'Pour accéder à cette page, vous ne devez pas être connecté à wordpress, merci de vous déconnecter', 'textdomain' ) ); 
	} 

	if ($_REQUEST['form_action'] == 'sendidguidz' ) {
		$email = $_POST['email'];
		$listeGuides = $wpdb->get_results("SELECT private_id, first_name, last_name FROM wp_participants_database where email = '" . $email. "'");
		foreach ($listeGuides as $guide) {
			$private_guidz_id = $guide->private_id;
			$last_name = $guide->last_name;
			$first_name = $guide->first_name;

			// $email = "info@fmosys.fr";
			
			$subject = "Votre ID GuidZ";
			$content = "Bonjour ".$first_name." ".$last_name." ,<br><br>
			vous avez fait une demande pour retrouver votre ID GuidZ, vous le trouverez ci-dessous: <br>
			".$private_guidz_id;

			$ret = sendEmails( $email, $subject, stripslashes( $content));
			// echo "SENT".$ret;
		}

		?>
		<br><br>Bonjour, un email vient de vous être envoyé à l'adresse renseignée.
		<br/><br/>
<?php 
		echo '<a href="'. get_site_url() . '/intranet">Continuer</a>';
	}
	else {

		if ($_REQUEST['form_action'] == "getid") {
			?>
			<form action="" method="post" class="needs-validation">
				<input type="hidden" name="form_action" value="sendidguidz">
				<?php
					$nonce = wp_create_nonce( 'my-nonce' );
				?>
				<input type="hidden" name="idguidz" value="<?php echo $nonce; ?>">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-10">
							<h2>Mon ID Guidz</h2>
								<div class="form-group">
									<div class="row">
										<div class="col-2">
											<label for="email">Email*: </label>
										</div>
										<div class="col">
											<input class="form-control" type="text" id="email" name="email" value="<?php echo $email ?>" required>
										</div>
									</div>
								</div>
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-10">
							<input class="form-control" type="submit" value="Envoyer">
						</div>
					</div>
				</div>
			</form>
			<br/><br/>
	<?php 
		}
	}

	if ($_POST['form_action'] == "save") {
		if ( isset($_POST['email']) && isset($_POST['guidzid'] )) {
			$email = $_POST['email'];
			$guidzid = $_POST['guidzid'];
			// echo "EMAIL:".$email;
			$listeGuides = $wpdb->get_results("SELECT private_id, first_name, last_name FROM wp_participants_database where email = '" . $email. "' AND private_id = '" . $guidzid. "'");
			
			foreach ($listeGuides as $guide) {
				$private_guidz_id = $guide->private_id;
				$last_name = $guide->last_name;
				$first_name = $guide->first_name;
				
				echo "<p>Bonjour <strong>". ucfirst($first_name)." ". ucfirst($last_name)."</strong></p>";
				echo "<p>Votre ID GUIDz :<strong>". $private_guidz_id."</strong></p>";
				echo "<br>";
				echo "<p><strong>Votre historique</strong>    ";
				echo '<form>';
				echo '<input id="impression" name="impression" type="button" onclick="imprimer_page()" value="Imprimer cette page" />';
				echo '</form>';
				echo '</p>';

				$orders = getOrdersNew();

				echo '<table border="1">';
				echo "<tr>";
				echo '<th style="text-align:left;">Activités</th>';
				echo '<th width="150" style="text-align:left;">Date d\'achat</th>';
				echo '<th width="150" style="text-align:left;">Prix payé</th>';
				echo "</tr>";

				foreach( $orders as $order) {
					if( $order->billing_email == $email) {
						echo "<tr>";
					//	echo "<td>".$customer->display_name.":".$customer->user_email.":".$customer->user_login."</td>";
						echo "<td>". str_replace("adminguidz", "", $order->order_items)."</td>";
						$datePaid = (new dateTime($order->paid_date))->format('d-m-Y');
						echo "<td>". $datePaid."</td>";
						echo "<td>". $order->order_total."€</td>";
						echo "</tr>";
						
						// var_dump( $order);
					}
				}
				echo "</table>";


/*
				foreach( $orders as $order) {
					// if( $product_id == $order->product_id) {
					$orderProduct = new WC_Order($order->order_id);
					$status =  $orderProduct->get_status(); 
					
					
					// echo "<br>DATE COMPLETED:" .$orderProduct->get_date_completed();
					// echo "<br>DATE PAIEMENT:" .$orderProduct->get_date_paid();
					if( ($orderProduct->get_date_completed() != '') && ($orderProduct->get_date_paid() != '')) { // SI COMPLET ET PAYE!!!!
						$dateCreated = (new dateTime($orderProduct->get_date_created()))->format('d-m-Y');
						$dateCompleted = (new dateTime($orderProduct->get_date_completed()))->format('d-m-Y');
						$datePaid = (new dateTime($orderProduct->get_date_paid()))->format('d-m-Y');
						
						$customer = $orderProduct->get_user();
						// var_dump( $customer); die;
					//	echo '<tr><td colspan="3">CUSTOMER EMAIL : '.$customer->user_email."</td><td>".$customer->user_login."</tr>";
					if( !isset( $_REQUEST['all']) && $customer->user_email == $email) {
							// echo "<p>CUSTOMER USER : ".$customer->display_name."</p>";
							// echo "<p>Product ID : ".$orderProduct->ID."</p>";
							// $status .= wc_bookings_get_status_label( $bookingReservation->get_status() ) .",";

							foreach ( $orderProduct->get_items() as $item_id => $item ) {
								echo "<tr>";
							//	echo "<td>".$customer->display_name.":".$customer->user_email.":".$customer->user_login."</td>";
								echo "<td>". $item->get_name()."</td>";
								echo "<td>". $datePaid."</td>";
								echo "<td>". $item->get_total()."€</td>";
								echo "<td>". __( $status, 'woocommerce' )."</td>";
								// echo "<td>". $item->get_quantity()."</td>";
								// echo "<td>". $item->get_subtotal()."</td>";
								// echo "<td>". $orderProduct->get_discount_total()."</td>";
								echo "</tr>";
								// var_dump( $item); die;
							}
							$costTotal += $total;
							$countCustomer++;
						}
					}
				}
				echo "</table>";
*/
			}


		}
		die;
	}
}

else {

	get_header();
?>
	<form action="" method="post" class="needs-validation">
		<input type="hidden" name="form_action" value="save">
		<?php
			$nonce = wp_create_nonce( 'my-nonce' );
			// wp_nonce_field( 'gudiz-id_' );
		?>
		<input type="hidden" name="idguidz" value="<?php echo $nonce; ?>">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-10">
					<h2>Connexion</h2>
						<div class="form-group">
							<div class="row">
								<div class="col-2">
									<label for="email">Email*: </label>
								</div>
								<div class="col">
									<input class="form-control" type="text" id="email" name="email" value="<?php echo $email ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col-2">
									<label for="sub_title">ID Guidz: </label>
								</div>
								<div class="col">
									<input class="form-control" type="text" id="guidzid" name="guidzid" value="">
								</div>
							</div>
						</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-10">
					<input class="form-control" type="submit" value="Connexion">
				</div>
			</div>

			<div class="row justify-content-center">
				<div class="col-10">
					<a href="?form_action=getid&idguidz=<?php echo $nonce; ?>" >Je n'ai pas mon ID Guidz....</a>
				</div>
			</div>
			
		</div>
	</form>
	<br/><br/>

<?php 
}
?>
