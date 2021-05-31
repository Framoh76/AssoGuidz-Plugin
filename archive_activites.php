<?php
/* Template Name: archive_activites */ 
?>
<!doctype html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
</body>
<?php
$type = array(
	'post_status'    => 'any',
	'post_type' 	=> 'activite'
);

$posts = get_posts($type);
//var_dump($posts);

echo "<table class='table'>";
	?>

	<tr>
		<th>Nom de la activite</th>
		<th>Sous-titre</th>
		<th>Prix</th>
		<th>Image</th>
		<th>Visible</th>
		<th>Lien</th>
		<th>Modifier</th>
		<th>Contenu</th>
		<th>Date d'expiration</th>
	</tr>

	<?php
foreach ($posts as $post) {
	if ($post->post_title != "add") {
		echo "<tr>";
			echo "<th>" . $post->post_title . "</th>";

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

			echo '<td><a href="' . get_permalink() . '?action=display">LIEN</a></td>';
			echo '<td><a href="' . get_permalink() . '?action=edit">EDIT</a></td>';

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
