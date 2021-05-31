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
get_header();
const NOMBRE_PROGRAMME = 10;
?>

<body onload="initElement();">
<?php
$post = get_post();

if (isset($_POST['form_action'])) {
	if ($_POST['form_action'] == "save") {
		// Post publique ou privée ?
		if (isset($_POST['visibility']))
			$publication = "publish";
		else 
			$publication = "private";

		if ($_POST['new']) {
			$my_post = array(
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			$id = wp_insert_post($my_post);
		} else {
			$my_post = array(
				'ID'			=> $post->ID,
				'post_title'    => wp_strip_all_tags($_POST['title']),
				'post_content'  => $_POST['content'],
				'post_status'   => $publication,
				'post_author'   => 'author',
				'post_type' 	=> 'activite'
			);
			$id = $post->ID;
			wp_update_post($my_post);
		}

		// Update
		update_post_meta( $id, 'sub_title', $_POST['sub_title']);
		update_post_meta( $id, 'prix', $_POST['prix']);

		if (!function_exists( 'wp_handle_upload' )) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
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
		update_post_meta( $id, 'horraire', $_POST['horraire']);
		wp_set_post_terms( $id, $_POST['type_activite'], 'type_activite');
		
		for ($i=0; $i < NOMBRE_PROGRAMME; $i++) { 
			if (isset($_POST['p_title_' . $i]) && isset($_POST['p_content_' . $i]) && isset($_FILES['p_image_' . $i])){
				update_post_meta( $id, 'programme_title_' . $i, $_POST['p_title_' . $i]);
				update_post_meta( $id, 'programme_content_' . $i, $_POST['p_content_' . $i]);


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
		}
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
		if (get_post_meta($post->ID, 'horraire', true) != null)
			$horraire = get_post_meta($post->ID, 'horraire', true);
		if (wp_get_post_terms($id, 'type_activite') != null)
			$type_activite = wp_get_post_terms($post->ID, 'type_activite');
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
						echo '<p class="horraire">' . $horraire . '</p>';
					
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
					<img src="<?php echo $image ?>" width="100%">
				</div>

				<div class="col-12"><h1>Programme</h1></div>
				<?php
				for ($i=0; $i < NOMBRE_PROGRAMME; $i++) {
					if ($programme[$i]['p_title'] != "") {
				?> 
					<div class="col-6">	<?php
						echo "<h3 class='p_title'>" . $programme[$i]['p_title'] . "</h3>";
						echo "<p class='p_content'>" . $programme[$i]['p_content'] . "</p>";
						?>
					</div>
					<div class="col6">
					<?php
						echo '<img src="' . $programme[$i]['p_image'] . '">';
					?>
											 
					</div>
				<?php } } ?>
				<hr>
			</div>
		</div>
	<?php
	} else if (isset($post) && $_REQUEST['action'] == "edit") { ?>
		<form action="" method="post" enctype="multipart/form-data">
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
										<label for="content">Conférencier: </label> 
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
											$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
											
											$name = "p_content_" . $i;
											wp_editor(
												'',
												$name,
												array(
													'media_buttons' => false,
													'tinymce'       => false,
													'quicktags'     => $quicktags_settings,
												)
											);
										?>
									</div>
								</div>

								<label for="visibility">Visibilité : </label> 

								<?php
								if ($visibility == "true") {
									echo '<input type="checkbox" id="visibility" name="visibility" value="true" checked>';
								}
								else {
									echo '<input type="checkbox" id="visibility" name="visibility" value="true" >';
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
										<label for="image">Reservé:</label>
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
										<label for="horraire">Horraire:</label>
									</div>
									<div class="col-4">
										<input class="form-control" type="text" name="horraire" id="horraire" value="<?php echo $horraire ?>">
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
										<select id="type_activite" name="type_activite" class="form-control">
											<option>Veuillez choisir le type de votre activité</option>
											<option value="conference" <?php if ($type_activite[0]->name == 'conference') echo ' selected '; ?>">Conférence</option>
											<option value="visio_conference" <?php if ($type_activite[0]->name == 'visio_conference') echo ' selected '; ?>">Visio-conférence</option>
											<option value="visite_in_situ" <?php if ($type_activite[0]->name == 'visite_in_situ') echo ' selected '; ?>">Visite in stitu</option>
										</select>
									</div>
								</div>
							</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-10">
						<h2>Programme (Max 10) <span id="showProgramme" class="dashicons dashicons-plus-alt"></span> </h2>
						<?php for ($i=0; $i < NOMBRE_PROGRAMME; $i++) { ?>
						<div id="programme_<?php echo $i ?>" class="programme_<?php echo $i ?>">
							<label for="p_title">Titre : </label>
							<input class="form-control" type="text" id="<?php echo "p_title_" . $i; ?>" name="<?php echo "p_title_" . $i; ?>" value="<?php echo $programme[$i]['p_title']; ?>">
							<label for="p_content">Description : </label>
							<?php 
								$settings = array( 
									'wpautop' => false, 
									'media_buttons' => false, 
									'quicktags' => array(
										'buttons' => 'strong,em,del,ul,ol,li,block,close'
									)
								);
								$name = "p_content_" . $i;
								wp_editor('', $name, $settings);
							?>
							<textarea class="form-control" id="<?php echo "p_content_" . $i; ?>" name="<?php echo "p_content_" . $i; ?>" value=""><?php echo $programme[$i]['p_content']; ?></textarea>
							<div class="row">
								<div class="col-2">
									<label for="p_image_<?php echo $i ?>">Image:</label>
								</div>
								<div class="col-10">
									<input class="form-control" type="file" name="p_image_<?php echo $i ?>" id="p_image_<?php echo $i ?>">
									<?php if ($image != "") { ?>
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
						<?php if ($post->post_title == "add") { ?>
							<input type="hidden" name="new" value="1">
						<?Php } else { ?>
							<input type="hidden" name="new" value="0">
						<?php
						}
						?>
						<input type="hidden" name="form_action" value="save">
						<input class="form-control" type="submit">
					</div>
				</div>
			</div>
		</form>
	<?php
	}
echo '</body>';
echo '</html>';
}