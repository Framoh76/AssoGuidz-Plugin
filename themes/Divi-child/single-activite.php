<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */


if( $_REQUEST['action'] == "edit" ) {
	// verification si user est connecté....
	$ID = get_current_user_id();
	if ( $ID == null) {
		$redirect = add_query_arg( 'redirect_to', get_permalink( $post->ID ), wp_login_url() );
					// redirect to the login page and then to the requested page...
		wp_redirect( $redirect );
		exit();
	}
	$main_menu_id = getMainMenuId(); // recupere le main menu ID et si n'existe pas....MESSAGE d'erreur...
	// echo "<br>MAIN MENU ID:".$main_menu_id;
	
	$ID_CONFERENCE = getMenuItemId( $main_menu_id, MENU_CONFERENCE); // 1141;
	if( $ID_CONFERENCE == null ) {
		echo '<br><br><font color="red">Le sous menu '.MENU_CONFERENCE.' doit etre créé avec URL: /produits?type_activite</font></br>';
		die;
	}

	$ID_CONF_EN_SALLE = getMenuItemId( $main_menu_id, MENU_CONF_EN_SALLE); // 1141;
	if( $ID_CONF_EN_SALLE == null ) {
		echo '<br><br><font color="red">Le sous menu '.MENU_CONF_EN_SALLE.' doit etre créé comme sous menu de Conférences avec URL: /produits?type_activite=conference</font></br>';
		die;
	}
	$ID_VISIOCONF = getMenuItemId( $main_menu_id, MENU_VISIOCONF); // 1142;
	if( $ID_VISIOCONF == null ) {
		echo '<br><br><font color="red">Le sous menu '.MENU_VISIOCONF.' doit etre créé comme sous menu de Conférences avec URL: /produits?type_activite=visio_conference</font></br>';
		die;
	}
	$ID_VISIT_INSITU = getMenuItemId( $main_menu_id, MENU_VISIT_INSITU); // 1143;
	if( $ID_VISIT_INSITU == null ) {
		echo '<br><br><font color="red">Le sous menu '.MENU_VISIT_INSITU.' doit etre créé avec URL: /produits?type_activite=visite_in_situ</font></br>';
		die;
	}
	
	if( $ID_CONF_EN_SALLE == null || $ID_VISIOCONF == null || $ID_VISIT_INSITU == null) {
		echo '<br><br><font color="red">Les sous menu ne sont pas créés, merci de créé Conférences et Visite Insitu</font></br>';
		die;
	}
	$type = array(
		'numberposts' 	=> '-1',
		'post_status'	=> 'any',
		'post_type' 	=> 'activite',
		'orderby' 		=> 'title',
		'order' 		=> 'ASC',
		'post_status'    => 'any'
	);
	$activites = get_posts($type);
	
	// echo "<br><br>";
	add_activite_to_menu( $ID_VISIT_INSITU, $activites, $main_menu_id, 'visite_in_situ', $titleOrigin);
	add_activite_to_menu( $ID_CONF_EN_SALLE, $activites, $main_menu_id, 'conference', $titleOrigin);
	add_activite_to_menu( $ID_VISIOCONF, $activites, $main_menu_id, 'visio_conference', $titleOrigin);
}

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




/*
// Cree ou renvoit les ID des MENU de type Conférence, Visio-conférence, Visite In situ, ...
function add_menu_item($item_post_title, $main_menu_id, $submenu_id, $typeActivite) {
	// Si un menu existe on return l'id du menu
	// echo "<br>add_menu_item".$main_menu_id.":".$item_post_title. " SubMenu de :".$submenu_id;
	if (wp_get_nav_menu_items($main_menu_id)) {
		foreach (wp_get_nav_menu_items($main_menu_id) as $item) {
			// echo $item->title.";";
			if ( ($item->title === $item_post_title) ) {
				$id = $item->ID;
				// echo "ID:".$id;
				return $id;
			}
		}
	}

	// Si n'existe pas créer l'item et return l'id
	$id = "0"; // Creer un nouveau Item de menu (conference, visio,....)
	$item_id = wp_update_nav_menu_item($main_menu_id, $id, array(
		'menu-item-title' => $item_post_title,
		'menu-item-object' => 'post',
		'menu-item-parent-id' => $submenu_id,
		'menu-item-url' => get_site_url() . "/produits?type_activite=" . $typeActivite,
		'menu-item-status' => 'publish'
	));
	if ( is_wp_error( $item_id ) ) {
		echo 'ERROR: ' . $item_id->get_error_message();
	}
	echo "<br> Creation ".$item_post_title.":".$item_id. " SubMenu de :".$submenu_id;
	echo "<hr>";
	return $item_id;
}
*/



/* Ajoute l'activité au menu ($menu_id) */
function add_activite_to_menu($parent_menu_id, $activites, $menu_id, $typeActivite, $titleOrigin) {
	// echo "<BR>ADD_ACTIVITE_TO_MENU<br>";
	foreach ($activites as $activite) {
		// N'affiche pas l'activité add (Qui permet d'ajouter une activité)
		$idActivite = 0; // Id de l'item du menu a ajouter
		
		if ($activite->post_name != "add") {
			// Récupère le type d'activité du post
			$type = get_the_terms($activite->ID, 'type_activite');
			// Vérification du type de l'activité
			if ( $type[0]->slug == $typeActivite) {
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
				// echo "<br>   ID:".$idActivite;
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
					}
				}
			}
		}
	}
}

function add_WOOCOMERCE_Product( $action, $productId, $title, $description, $price) {
	if( $action == '1') {
		$post_id = wp_insert_post( array(
			'post_title' => $title,
			'post_content' => $description,
			'post_status' => 'publish',
			'post_type' => "product",
		) );

		wp_set_object_terms( $post_id, 'simple', 'product_type' );
		update_post_meta( $post_id, '_price', $price );
		
		return $post_id;
	}
	else {
		$postProduct = get_post( $productId);
		if( $postProduct != null) {
			echo "ID PROD:".$postProduct->ID;
			$post_id = wp_update_post( array(
				'ID' => $postProduct->ID,
				'post_title' => $title,
				'post_content' => $description,
				'post_status' => 'publish',
				'post_type' => "product",
			) );

			wp_set_object_terms( $post_id, 'simple', 'product_type' );
			update_post_meta( $post_id, '_price', $price );
			return $post_id;
		}
		else {
			echo "ERROORRRR 502.cant find Product";
			die;
		}
	}
}

const NOMBRE_PROGRAMME = 10;
if (!isset($_POST['new'])) {
	get_header();
}

$id_post = url_to_postid( $_SERVER['REQUEST_URI'] );
$post = get_post( $id_post);

if (isset($_POST['form_action'])) {
	if ($_POST['form_action'] == "save") {
		
		// Post publique ou privée ?
		if (isset($_POST['visibility']))
			$publication = "publish";
		else 
			$publication = "private";
		
		// echo "NEW:".$_POST['new'];
		
		if ($_POST['new']) {
			$my_post_array = array(
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			$id = wp_insert_post($my_post_array);
			// echo "  INSERTED:".$id ;
			$post = get_post($id);
			// echo "  POST ID:".$post->ID;
			
		} else {
			$my_post_array = array(
				'ID'			=> $_POST['activite_id'],
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			// echo " UPDATE POST".$_POST['activite_id']. " PUBLI:".$publication;
			$id = wp_update_post($my_post_array, true);
			echo " -->UPDATED: ".$id;
			$post = get_post($id);
		}

		// Update
		update_post_meta( $id, 'sub_title', $_POST['sub_title']);
		update_post_meta( $id, 'prix', $_POST['prix']);

		if (!function_exists( 'wp_handle_upload' )) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		if ($_FILES['image']['name'] != '') { 
			$uploadedfile = $_FILES['image'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			if ( $movefile ) {
				update_post_meta( $id, 'image', $movefile['url']);
				$wp_filetype = $movefile['type'];
				$attachment = array(
					'guid' => $wp_upload_dir['url'] . '/' . basename($uploadedfile['name']),
					'post_mime_type' => $wp_filetype,
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploadedfile['name'])),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_id = wp_insert_attachment( $attachment, $uploadedfile['name'], $id);
				$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadedfile );
			} else {
				echo "Un problème est survenue";
			}
		}

		if (isset($_POST['visibility']))
			update_post_meta( $id, 'visibility', 'true');
		else
			update_post_meta( $id, 'visibility', 'false');

		$id_current_activite = $id;
		update_post_meta( $id, 'cancel', $_POST['cancel']);
		update_post_meta( $id, 'expiration', $_POST['expiration']);
		update_post_meta( $id, 'conferencier', $_POST['conferencier']);
		update_post_meta( $id, 'quantite', $_POST['quantite']);
		update_post_meta( $id, 'reserver', $_POST['reserver']);
		update_post_meta( $id, 'lieu', $_POST['lieu']);
		update_post_meta( $id, 'date', $_POST['date']);
		update_post_meta( $id, 'horaire', $_POST['horaire']);
		wp_set_post_terms( $id, $_POST['type_activite'], 'type_activite');

		$nb_programme = 0;
/*
		for ($i=0; $i < NOMBRE_PROGRAMME; $i++) { 
			if (isset($_POST['p_title_' . $i]) && isset($_POST['p_content_' . $i]) && isset($_FILES['p_image_' . $i])){
				update_post_meta( $id, 'programme_title_' . $i, $_POST['p_title_' . $i]);
				update_post_meta( $id, 'programme_content_' . $i, $_POST['p_content_' . $i]);

				if ($_FILES['p_image_' . $i]['name'] != '') { 
					$uploadedfile = $_FILES['p_image_' . $i];

					$upload_overrides = array( 'test_form' => false );
					$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
					if ( $movefile ) {
						update_post_meta( $id, 'programme_image_' . $i, $movefile['url']);
						$wp_filetype = $movefile['type'];
						$attachment = array(
							'guid' => $wp_upload_dir['url'] . '/' . basename($uploadedfile['name']),
							'post_mime_type' => $wp_filetype,
							'post_title' => preg_replace('/\.[^.]+$/', '', basename($uploadedfile['name'])),
							'post_content' => '',
							'post_status' => 'inherit'
						);
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						$attach_id = wp_insert_attachment( $attachment, $uploadedfile['name'], $id);
						$attach_data = wp_generate_attachment_metadata( $attach_id, $uploadedfile );
					} else {
						echo "Un problème est survenue";
					}
				}

				if ($_POST['p_title_' . $i] != '') {
					$nb_programme++;
				}	
			}
		}
*/
		update_post_meta( $id, 'programme_count', $nb_programme);
		
		$productId = get_post_meta($id, 'id_product', true);
			// echo " ADD WOO".$_POST['new']. " PID". $productId;

		$idProduct = add_WOOCOMERCE_Product( $_POST['new'], $productId, $_POST['title'], $_POST['content'], $_POST['prix']);

		// echo " update_post_meta ". $id_current_activite . "  ". $idProduct;
		update_post_meta( $id_current_activite , 'id_product', $idProduct);

		$titleOrigin = $_POST['title'];


		$type = array(
			'numberposts' 	=> '-1',
			'post_status'	=> 'any',
			'post_type' 	=> 'activite',
			'orderby' 		=> 'date',
			'order' 		=> 'ASC',
			'post_status'    => 'any'
		);

		$activites = get_posts($type);
		$menu_id = getMainMenuId();  // $menu_id contient l'ID du menu qui doit s'appeler "Principal"

		switch( $_POST['type_activite']) {
			case CONST_CONFERENCE:
				add_activite_to_menu($ID_CONF_EN_SALLE, $activites, $main_menu_id, CONST_CONFERENCE, $titleOrigin);
				break;
			case CONST_VISIOCONFERENCE:
				add_activite_to_menu( $ID_VISIOCONF, $activites, $main_menu_id, CONST_VISIOCONFERENCE, $titleOrigin);
				break;
			case CONST_VISIT_INSITU:
				add_activite_to_menu( $ID_VISIT_INSITU, $activites, $main_menu_id, CONST_VISIT_INSITU, $titleOrigin);
				break;
		}
		// die;
		header('Location: '. get_site_url() . '/archives-des-activites/');
	}
}

if ($post) {
	if ($post->post_title != 'add') {
		$title = $post->post_title;
		if (get_post_meta($post->ID, 'sub_title', true) != null)
			$sub_title = get_post_meta($post->ID, 'sub_title', true);
		if (get_post_meta($post->ID, 'prix', true) != null)
			$prix = get_post_meta($post->ID, 'prix', true);
		if (get_post_meta($post->ID, 'image', true) != null)
			$image = get_post_meta($post->ID, 'image', true);
		if (get_post_meta($post->ID, 'visibility', true) != null)
			$visibility = get_post_meta($post->ID, 'visibility', true);
		if (get_post_meta($post->ID, 'expiration', true) != null)
			$expiration = get_post_meta($post->ID, 'expiration', true);
		if (get_post_meta($post->ID, 'cancel', true) != null)
			$cancel = get_post_meta($post->ID, 'cancel', true);
		if (get_post_meta($post->ID, 'conferencier', true) != null)
			$conferencier = get_post_meta($post->ID, 'conferencier', true);
		if (get_post_meta($post->ID, 'quantite', true) != null)
			$quantite = get_post_meta($post->ID, 'quantite', true);
		
		$quantiteBooked = countParticipants( $post);
		
		if (get_post_meta($post->ID, 'lieu', true) != null)
			$lieu = get_post_meta($post->ID, 'lieu', true);
		if (get_post_meta($post->ID, 'date', true) != null) {
			$date = get_post_meta($post->ID, 'date', true);
			if( $_REQUEST['action'] != "edit" ) {
				$dateF = DateTime::createFromFormat( 'Y-m-d', $date);
				if( $dateF)
					$date = $dateF->format('d-m-Y');
			}
		}
		if (get_post_meta($post->ID, 'horaire', true) != null)
			$horaire = get_post_meta($post->ID, 'horaire', true);
		
		$termsTypeActivite = get_the_terms($post->ID, 'type_activite'); 
		if ( $termsTypeActivite != null) {
			$type_activite = $termsTypeActivite[0]->name;
		}

		$content = $post->post_content;
	
		$programme = array();
	
		for ($i=0; $i < NOMBRE_PROGRAMME; $i++) { 
			$programme[] = [
				'p_title' => get_post_meta($post->ID, 'programme_title_' . $i, true),
				'p_content' => get_post_meta($post->ID, 'programme_content_' . $i, true),
				'p_image' => get_post_meta($post->ID, 'programme_image_' . $i, true)
			];
		}

		$id_produit = get_post_meta($post->ID, 'id_product', true);
		$produit = get_post($id_produit);
	}
	if (isset($post) && $_REQUEST['action'] == "display" || !isset($_REQUEST['action'])) { //Page display ?>

		<div id="et-main-area">
			<div id="main-content">
			<article>
				<div class="entry-content">
					<div id="et-boc" class="et-boc">
						<div class="et-l et-l--post">
							<div class="et_builder_inner_content et_pb_gutters3">
								<div class="et_pb_section et_pb_section_0 et_section_regular" >
									<div class="et_pb_row et_pb_row_0">
										<div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
											<div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
												<div class="et_pb_text_inner"></div>
											</div> <!-- .et_pb_text -->
										</div> <!-- .et_pb_column -->
									</div> <!-- .et_pb_row -->
									<div class="et_pb_row et_pb_row_2">
										<div class="et_pb_column et_pb_column_1_2 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough">
											<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
												<div class="et_pb_text_inner">
												<?php
													if ($title != "")
														echo '<h2>' . $title . '</h2>';

													if ($sub_title != "")
														echo '<h2>' . $sub_title . '</h2>';

													if ($content != "")
														echo '<p>' . $content . "</p>";

													echo '<p>';
													if ($conferencier != "")
														echo '<strong>Conférencier.e : </strong>' . $conferencier . '<br>';

													if ($date != "")
														echo '<strong>Date : </strong>' . $date . '<br>';

													if ($horaire != "")
														echo '<strong>Horaire : </strong>' . $horaire . '<br>';
													
													if ($prix != "")
														echo '<strong>Tarif : </strong>' . $prix . '€<br>';

													if ($lieu != "")
														echo '<strong>Lieu : </strong>' . $lieu . '<br>';

													if ($cancel != "") {
														echo '<span style="text-decoration: underline; color: #e02b20;">' . $cancel . '</span><br>';
													}

													if ($quantite != "") {
														$quantite -= $quantiteBooked;
														echo '<strong>Places restantes : </strong>' . $quantite.'<br>';
													}
													echo '</p>';
												?>
												</div> <!-- .et_pb_text -->
												<?php 
												
												$date_aujourdhui = date('Y-m-d', time());
												$datetime_aujourdhui = date_create($date_aujourdhui);
												$datetime_date = date_create($date);
												$interval = date_diff($datetime_date, $datetime_aujourdhui);
												$count_jour = $interval->format('%r%a'); // %r (negative and positive) %a(jour)
												// echo $count_jour;

												if ($cancel == "") { 
												  if( $count_jour <= 0) {
													if( $quantite >= 0) {
												?>
												<div class="et_pb_button_module_wrapper et_pb_button_0_wrapper  et_pb_module ">
													<a class="et_pb_button et_pb_button_0 et_pb_bg_layout_light" href="?add-to-cart=<?php echo $produit->ID ?>&type_activite=<?php echo $_GET['type_activite'];?>" 
															data-quantity="1"
															data-product_id==<?php echo $produit->ID ?>	
															rel="nofollow">Réserver
													</a>

													<!-- <a class="et_pb_button et_pb_button_0 et_pb_bg_layout_light" href="https://assoguidz.com/boutique/visite-guidee-2020-2021/jardin-tuileries/">Réserver </a> -->
												</div>
													<?php }
													}
												  } ?>
											</div>
										</div> <!-- .et_pb_column -->
										<div class="et_pb_column et_pb_column_1_2 et_pb_column_3  et_pb_css_mix_blend_mode_passthrough et-last-child">
											<div class="et_pb_module et_pb_image et_pb_image_0">
												<?php if( $image != '') { ?>
												<span class="et_pb_image_wrap "><img class="img_activite" src="<?php echo $image ?>" title=<?php echo $title ?> alt=<?php echo $title ?> height="auto" width="auto" data-recalc-dims="1"></span>
												<?php } ?>
											</div>
										</div> <!-- .et_pb_column -->
									</div> <!-- .et_pb_row -->
									<div class="et_pb_row et_pb_row_3">
										<div class="et_pb_column et_pb_column_4_4 et_pb_column_4  et_pb_css_mix_blend_mode_passthrough et-last-child">
											<div class="et_pb_module et_pb_divider et_pb_divider_0 et_pb_divider_position_ et_pb_space"><div class="et_pb_divider_internal"></div></div>
										</div> <!-- .et_pb_column -->
									</div> <!-- .et_pb_row -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</article>
			</div>
		</div>
	<?php
	} else if (isset($post) && $_REQUEST['action'] == "edit") { ?>
		<form action="" method="post" class="needs-validation" enctype="multipart/form-data">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-10">
						<h2>Conférence</h2>
							<div class="form-group">
								<div class="row">
									<div class="col-2">
										<label for="title">Titre*: </label>
									</div>
									<div class="col">
										<input type="hidden" id="activite_id" name="activite_id" value="<?php echo $post->ID ?>" required>
										<input type="hidden" id="title_origin" name="title_origin" value="<?php echo $title ?>" required>
										<input class="form-control" type="text" id="title" name="title" value="<?php echo $title ?>" required>
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="type_activite">Type d'activité</label>
									</div>
									<div class="col">
										<select id="type_activite" name="type_activite" class="form-control" required>
											<option value="">Veuillez choisir le type de votre activité</option>
											<option value="conference" <?php if ($type_activite == CONST_CONFERENCE) echo ' selected '; ?>">Conférence en salle</option>
											<option value="visio_conference" <?php if ($type_activite == CONST_VISIOCONFERENCE) echo ' selected '; ?>">Visio-conférence</option>
											<option value="visite_in_situ" <?php if ($type_activite == CONST_VISIT_INSITU) echo ' selected '; ?>">Visite in situ</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="sub_title">Sous-titre: </label>
									</div>
									<div class="col">
										<input class="form-control" type="text" id="sub_title" name="sub_title" value="<?php echo $sub_title ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="conferencier">Conférencier: </label> 
									</div>
									<div class="col">
										<input class="form-control" type="text" id="conferencier" name="conferencier" value="<?php echo $conferencier ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="content">Description: </label> 
									</div>
									<div class="col">
										<?php 
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
										?>
										<script> 
										tinymce.init({
										menubar:false,
										statusbar:false,
										selector: "textarea",
										plugins: [
										],
										toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
										}); 
										</script>
									</div>
								</div>

								<label for="visibility">Visible sur le site : </label> 

								<?php
									if ($post->post_title == 'add') {
										echo '<input type="checkbox" id="visibility" name="visibility" value="true" checked>';
									} else {
										if ($visibility == "true") {
											echo '<input type="checkbox" id="visibility" name="visibility" value="true" checked>';
										}
										else {
											echo '<input type="checkbox" id="visibility" name="visibility" value="true" >';
										}
									}
								?>

								<div class="row">
									<div class="col-2">
										<label for="prix">Annulation: </label> 
									</div>
									<div class="col">
										<input class="form-control" type="text" id="cancel" name="cancel" value="<?php echo $cancel ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="image">Image:</label>
									</div>
									<div class="col-10">
										<input class="form-control" type="file" name="image" id="image">
										<?php if ($image != "") { ?>
											<div class="col-3">
												<img src="<?php echo $image ?>" width="100" height="100">
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="prix">Prix*: </label> 
									</div>
									<div class="col-2">
										<input class="form-control" type="number" id="prix" name="prix" value="<?php echo $prix ?>" required>
									</div>
									<div class="col-2">
										<label for="image">Quantité*:</label>
									</div>
									<div class="col-2">
										<input class="form-control" type="number" name="quantite" id="quantite" value="<?php echo $quantite ?>" required>
									</div>
									<div class="col-2">
										<label for="image">Réservé:</label>
									</div>
									<div class="col-2">
										<input class="form-control" type="number" name="reserver" id="reserver" value="<?php echo $quantiteBooked ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="lieu">Lieu: </label> 
									</div>
									<div class="col-10">
										<input class="form-control" type="text" id="lieu" name="lieu" value="<?php echo $lieu ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="date">Date*: </label> 
									</div>
									<div class="col-4">
										<input class="form-control" type="date" id="date" name="date" value="<?php echo $date ?>"  required>
									</div>
									<div class="col-2">
										<label for="horaire">Horaire:</label>
									</div>
									<div class="col-4">
										<input class="form-control" type="text" name="horaire" id="horaire" value="<?php echo $horaire ?>">
									</div>
								</div>
								<div class="row">
									<div class="col-2">
										<label for="expiration">Date d'expiration: </label>
									</div>
									<div class="col">
										<input class="form-control" type="date" id="expiration" name="expiration" value="<?php echo $expiration ?>">
									</div>
								</div>
								
							</div>
					</div>
				</div>
				<!--
				<div class="row justify-content-center">
					<div class="col-10">				
						<h2>Programme (Max 10) <span onclick="showProgramme()" id="showProgramme" class="dashicons dashicons-plus-alt"></span> </h2>
						<input type="hidden" id="programme_count" value="<?php echo get_post_meta($post->ID, 'programme_count' , true) != null ? get_post_meta($post->ID, 'programme_count' , true) : 0; ?>">
						<?php for ($i=0; $i < NOMBRE_PROGRAMME; $i++) { ?>
						<div id="programme_<?php echo $i ?>" class="programme_<?php echo $i ?>">
							<label for="p_title">Titre : </label>
							<input class="form-control" type="text" id="<?php echo "p_title_" . $i; ?>" name="<?php echo "p_title_" . $i; ?>" value="<?php echo $programme[$i]['p_title']; ?>">
							<label for="p_content">Description : </label>
							<?php 
							
								$name = "p_content_" . $i;
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
								wp_editor($programme[$i]['p_content'], $name, $settings);
							?>
							
							<div class="row">
								<div class="col-2">
									<label for="p_image_<?php echo $i ?>">Image:</label>
								</div>
								<div class="col-5">
									<input class="form-control" type="file" name="p_image_<?php echo $i ?>" id="p_image_<?php echo $i ?>">
									<?php if ($programme[$i]['p_image'] != "") { ?>
										<div class="col-3">
											<img src="<?php echo $programme[$i]['p_image'] ?>" width="100" height="100">
										</div>
									<?php } ?>
								</div>
							</div>
							<hr>
						</div>
						<?php } ?>
					</div>
				</div>
				-->
				<div class="row justify-content-center">
					<div class="col-10">
						<?php if ($post->post_name == "add") { ?>
							<input type="hidden" name="new" value="1">
						<?Php } else { ?>
							<input type="hidden" name="new" value="0">
						<?php
						}
						?>
						<input type="hidden" name="form_action" value="save">
						<input class="form-control" type="submit" value="Enregistrer">
					</div>
				</div>
			</div>
		</form>
		<script type="text/javascript">
			afficheProgramme();
		</script>
		<br/><br/>
	<?php
	}
echo '</body>';
echo '</html>';
}