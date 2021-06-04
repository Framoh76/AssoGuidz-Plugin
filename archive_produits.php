<?php
/* Template Name: archive_produits */ 
get_header();

$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1'
);

//isset($_GET['type_activite']) ? $_GET['type_activite'] : 'any'

$produits = get_posts($type);
//var_dump($produits);
//get_post_meta($post->ID, 'reserver', true) != null
?>

<div class="container">
	<div class="row">

		<?php
		foreach($produits as $produit) {
			$termsTypeActivite = get_the_terms($produit->ID, 'type_activite'); 
			if ( $termsTypeActivite != null) {
				$type_activite = $termsTypeActivite[0]->name;
			}

			//echo "Id: " . $produit->ID . "<br />";
			//echo "Titre : " . $produit->post_title . "<br />";
			//echo "Type d'activité : " . $type_activite . "<br />";
			//echo "Type d'activité demandé : " . $_GET['type_activite'] . "<br />";

			if ($type_activite == $_GET['type_activite'] && $produit->post_name != "add") {
			?>

				<div class="col-4">
					<h1 style="text-align: center"><?php echo $produit->post_title ?></h1>
					<p><?php echo $produit->post_content ?></p>
					<p></p>
					<div class="row">
						<div class="col-6">
							<a href="<?php echo $produit->guid ?>">Détails</a>
						</div>
						<div class="col-6">
							<button id="add_to_cart">Add to cart</button>
						</div>
					</div>
				</div>

			<?php
			}
		}
		?>

	</div>
</div>