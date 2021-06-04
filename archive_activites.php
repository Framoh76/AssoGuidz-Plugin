<?php
/* Template Name: archive_activites */ 
?>
<?php
get_header();

$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1',
);

$posts = get_posts($type);
//var_dump($posts);

echo "<table class='table'>";
	?>

	<tr>
		<th>Lien</th>
		<th>Nom de l'activité</th>
		<th>Sous-titre</th>
		<th>Prix</th>
		<th>Image</th>
		<th>Visibilité</th>
		<th>Contenu</th>
		<th>Date d'expiration</th>
	</tr>

	<?php
foreach ($posts as $post) {
	if ($post->post_title != "add") {
		echo "<tr>";
		
			echo '<td><a href="' . get_permalink() . '?action=display"><span class="dashicons dashicons-external"></span></a></td>';

			echo '<th><a href="' . get_permalink() . '?action=edit">' .  $post->post_title . "</a></th>";

			if (!empty(get_post_meta($post->ID, 'sub_title', true))) {
				echo "<td>" . get_post_meta($post->ID, 'sub_title', true) . "</td>";
			} else {
				echo "<td>Pas de sous-titre</td>";
			}

			if (!empty(get_post_meta($post->ID, 'prix', true))) {
				echo "<td>" . get_post_meta($post->ID, 'prix', true) . "</td>";
			} else {
				echo "<td>Pas de prix</td>";
			}

			if (!empty(get_post_meta($post->ID, 'image', true))) {
				echo '<td><img src="' . get_post_meta($post->ID, 'image', true) . '" alt="image" width="200" height="200"></td>';
			} else {
				echo "<td>Pas d'image</td>";
			}

			if (get_post_meta($post->ID, 'visibility', true) == "true") {
				echo '<td><input type="checkbox" id="visibility" name="visibility" checked disabled </td>';
			} else {
				echo '<td><input type="checkbox" id="visibility" name="visibility" disabled </td>';
			}

			echo "<td>" . $post->post_content . "</td>";

			if (!empty(get_post_meta($post->ID, 'expiration', ""))) {
				echo "<td>" . get_post_meta($post->ID, 'expiration', true) . "</td>";
			} else {
				echo "<td>Pas de date d'expiration</td>";
			}
		echo "</tr>";
	}
}
echo "</table>";

echo '<button class="btn btn-light"><a href="' . get_site_url() . '/activite/add/?action=edit"> Ajouter une activite </a></button>';
echo '</body>'; 
echo '</html>';
?>
