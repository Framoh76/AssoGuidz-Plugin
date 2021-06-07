<?php
/* Template Name: archive_produits */ 
get_header();

$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1'
);

//isset($_GET['type_activite']) ? $_GET['type_activite'] : 'any'

$activites = get_posts($type);
//var_dump($produits);
//get_post_meta($post->ID, 'reserver', true) != null
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
			//echo "Type d'activité demandé : " . $_GET['type_activite'] . "<br />";

			if ($type_activite == $_GET['type_activite'] && $activite->post_name != "add") {
			?>

				<div class="col-4">
					<h1 style="text-align: center"><?php echo $activite->post_title ?></h1>
					<p><?php echo $activite->post_content ?></p>
					<p></p>
					<div class="row">
						<div class="col-6">
							<a href="<?php echo $activite->guid ?>">Détails</a>
						</div>
						<div class="col-6">
							<?php 
								$id_produit = get_post_meta($activite->ID, 'id_product', true);
								$produit = get_post($id_produit);
							?>
							<a 
								href="?add-to-cart=<?php echo $produit->ID ?>" 
								data-quantity="1"
								class="button product_type_simple add_to_cart_button ajax_add_to_cart"
								data-product_id==<?php echo $produit->ID ?>	
								rel="nofollow">Ajouter au panier
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