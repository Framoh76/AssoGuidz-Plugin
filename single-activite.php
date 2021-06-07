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
			// echo "ID PROD:".$idProduct;
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

		if ($_POST['new']) {
			$my_post_array = array(
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			$id = wp_insert_post($my_post_array);
			$post = get_post($id);
		} else {
			$my_post_array = array(
				'ID'			=> $post->ID,
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			$id = wp_update_post($my_post_array, true);
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

		update_post_meta( $id, 'programme_count', $nb_programme);
		
		$productId = get_post_meta($post->ID, 'id_product', true);
		
		$idProduct = add_WOOCOMERCE_Product( $_POST['new'], $productId, $_POST['title'], $_POST['content'], $_POST['prix']);

		update_post_meta( $id, 'id_product', $idProduct);

		function add_menu_item($item_post_title, $main_menu_id, $menu_id, $typeActivite) {
			// Si un menu existe on return l'id du menu
			if (wp_get_nav_menu_items($menu_id)) {
				foreach (wp_get_nav_menu_items($menu_id) as $item) {
					if ($item->title === $item_post_title) {
						$id = $item->ID;
					}
				}
			}

			// Si n'existe pas créer l'item et return l'id
			$item_id = wp_update_nav_menu_item($menu_id, $id, array(
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
		if (get_post_meta($post->ID, 'reserver', true) != null)
			$reserver = get_post_meta($post->ID, 'reserver', true);
		if (get_post_meta($post->ID, 'lieu', true) != null)
			$lieu = get_post_meta($post->ID, 'lieu', true);
		if (get_post_meta($post->ID, 'date', true) != null)
			$date = get_post_meta($post->ID, 'date', true);
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
	}
	if (isset($post) && $_REQUEST['action'] == "display" || !isset($_REQUEST['action'])) { //Page display ?>

		<div class="container">
			<div class="row">
				<div class="col-6">
					<?php
					if ($title != "")
						echo '<h1 class="titre">' . $title . '</h1>';

					if ($sub_title != "")
						echo '<h2 class="sous_titre">' . $sub_title . '</h2>';

					if ($conferencier != "")
						echo '<h3 class="conferencier">' . $conferencier . '</h3>';

					if ($date != "")
						echo '<p class="date">' . $date . '</p>';

					if ($duree != "")
						echo '<p class="horaire">' . $horaire . '</p>';
					
					if ($prix != "")
						echo '<p class="prix">Tarif ' . $prix . '€</p>';

					if ($lieu != "")
						echo '<p class="lieu">' . $lieu . '</p>';

					if ($cancel != "")
						echo '<p class="annulation">' . $cancel . '</p>';

					if ($content != "")
						echo '<p class="contenu">' . $content . "</p>";

					if ($quantite != "") {
						echo 'Réservation restante: ' . $quantite;
					}
					?>
				</div>

				<div class="col-6">
					<img class="img_activite" src="<?php echo $image ?>">
				</div>
			</div>
			<div class="row">
				<div class="col-12"><h1>Programme</h1></div>
					<?php
					for ($i=0; $i < NOMBRE_PROGRAMME; $i++) {
						if ($programme[$i]['p_title'] != "") {
					?> 
						<div class="row">
							<div class="col-6">	<?php
								echo "<h3 class='p_title'>" . $programme[$i]['p_title'] . "</h3>";
								echo "<p class='p_content'>" . $programme[$i]['p_content'] . "</p>";
								?>
							</div>
							<div class="col-6">
							<?php
								echo '<img class="img_programme" src="' . $programme[$i]['p_image'] . '">';
							?>
							</div>
						</div>
					<?php } } ?>
					<hr>
				</div>
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
										<input class="form-control" type="text" id="title" name="title" value="<?php echo $title ?>" required>
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
												'media_buttons' => true, // show insert/upload button(s)
												'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
												'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
												'tabindex' => '',
												'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
												'editor_class' => '', // add extra class(es) to the editor textarea
												'teeny' => false, // output the minimal editor config used in Press This
												'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
												'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()

												'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
											);
											
											wp_editor($content, $name , $settings);
										?>
									</div>
								</div>

								<label for="visibility">Visibilité : </label> 

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
										<label for="prix">Prix: </label> 
									</div>
									<div class="col-2">
										<input class="form-control" type="number" id="prix" name="prix" value="<?php echo $prix ?>">
									</div>
									<div class="col-2">
										<label for="image">Quantité:</label>
									</div>
									<div class="col-2">
										<input class="form-control" type="number" name="quantite" id="quantite" value="<?php echo $quantite ?>">
									</div>
									<div class="col-2">
										<label for="image">Réservé:</label>
									</div>
									<div class="col-2">
										<input class="form-control" type="number" name="reserver" id="reserver" value="<?php echo $reserver ?>">
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
										<label for="date">Date: </label> 
									</div>
									<div class="col-4">
										<input class="form-control" type="text" id="date" name="date" value="<?php echo $date ?>">
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
								<div class="row">
									<div class="col-2">
										<label for="type_activite">Type d'activité</label>
									</div>
									<div class="col">
										<select id="type_activite" name="type_activite" class="form-control" required>
											<option value="">Veuillez choisir le type de votre activité</option>
											<option value="conference" <?php if ($type_activite == 'conference') echo ' selected '; ?>">Conférence en salle</option>
											<option value="visio_conference" <?php if ($type_activite == 'visio_conference') echo ' selected '; ?>">Visio-conférence</option>
											<option value="visite_in_situ" <?php if ($type_activite == 'visite_in_situ') echo ' selected '; ?>">Visite in situ</option>
										</select>
									</div>
								</div>
							</div>
					</div>
				</div>
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
									'media_buttons' => true, // show insert/upload button(s)
									'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
									'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
									'tabindex' => '',
									'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
									'editor_class' => '', // add extra class(es) to the editor textarea
									'teeny' => false, // output the minimal editor config used in Press This
									'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
									'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()

									'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
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