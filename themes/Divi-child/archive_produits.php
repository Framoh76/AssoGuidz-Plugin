<?php
/* Template Name: archive_produits */ 

if (isset($_GET['add-to-cart'])) {
	if ($_GET['add-to-cart'] != '') {
		header('Location: ' . get_site_url() . "/produits/?type_activite=" . $_GET['type_activite']);
	}
}
$__LEN_ACTIVITE= 300;

get_header();

$type = array(
	'post_status'    => 'publish',
	'post_type' 	=> 'activite',
	'numberposts' 	=> '-1',
	'orderby' 		=> 'title',
	'order' 		=> 'ASC',

);

//isset($_GET['type_activite']) ? $_GET['type_activite'] : 'any'

$activites = get_posts($type);
//var_dump($produits);
//get_post_meta($post->ID, 'reserver', true) != null
?>
		

		<div id="et-main-area">
			<div id="main-content">
			<article>
				<div class="entry-content">
					<div id="et-boc" class="et-boc">
						<div class="et-l et-l--post">
							<div class="et_builder_inner_content et_pb_gutters3">
								<div class="et_pb_section et_pb_section_0 et_section_regular" >
									<div class="et_pb_row et_pb_row_2">
										<div class="et_pb_column et_pb_column_1_2 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough">
											<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
												<div class="et_pb_text_inner">
													<a href="<?php echo get_site_url()?>/panier">
														Mes réservations 
														<?php 
															global $woocommerce;
															echo $woocommerce->cart->get_cart_total() . " (" . $woocommerce->cart->cart_contents_count . " articles)";
														?>
													</a>
												</div>
											</div>
										</div>
									</div>

								<?php
								foreach($activites as $activite) {
									$termsTypeActivite = get_the_terms($activite->ID, 'type_activite'); 
									if ( $termsTypeActivite != null) {
										$type_activite = $termsTypeActivite[0]->name;
									}
									if ($type_activite == $_GET['type_activite'] && $activite->post_name != "add" || 
										($_GET['type_activite'] == '' && ($type_activite == 'conference' || $type_activite == 'visio_conference') && $activite->post_name != "add")) { 
										
										$countActivites++;
									?>
										<?php $spacer = false;
											if( $countActivites % 4 == 0) {
											$spacer = true;
										}
										?>
										<?php if( $countActivites == 1 ) { ?>
										<div class="et_pb_row et_pb_row_2"><?php // echo "ROW:".$countActivites; ?>
										<?php } ?>
											<div class="et_pb_column et_pb_column_1_2 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough">
												<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
													<div class="et_pb_text_inner">
														<h2 style="text-align: center"><?php echo $activite->post_title ?></h2>
														<p><a href="<?php echo $activite->guid."?action=display"; ?>"><?php // echo apply_filters( 'get_the_excerpt', $activite->post_content, $post );   
														echo the_short_content($activite->post_content, $__LEN_ACTIVITE); ?></p>
														<p><?php 
															$date = $activite->date;
															$dateF = DateTime::createFromFormat( 'Y-m-d', $date);
															if( $dateF)
																$date = $dateF->format('d-m-Y');
														
															echo $date; ?></p>
														<span class="dashicons dashicons-plus"></span></a>
														<?php 
															$id_produit = get_post_meta($activite->ID, 'id_product', true);
															$produit = get_post($id_produit);
															$image = get_post_meta($activite->ID, 'image', true);
															$title = $activite->post_title;
														?>
														<a 
															href="?add-to-cart=<?php echo $produit->ID ?>&type_activite=<?php echo $_GET['type_activite'];?>" 
															data-quantity="1"
															class="button product_type_simple add_to_cart_button ajax_add_to_cart"
															data-product_id==<?php echo $produit->ID ?>	
															rel="nofollow"><span class="dashicons dashicons-cart"></span>
														</a>
														<span style="color:#2ea3f2;"><strong><?php echo " - " .TypeActivite($type_activite); ?></strong></span>
													</div>
												</div>
												<?php if($image) { ?>
												<div class="et_pb_module et_pb_image et_pb_image_0">
													<span class="et_pb_image_wrap "><img class="img_activite" src="<?php echo $image ?>" title=<?php echo $title ?> alt=<?php echo $title ?> height="auto" width="auto" data-recalc-dims="1">
													</span>
												</div>
												<?php } ?>
											</div>
										<?php 
										$Endspacer = false;
										if( $countActivites % 2 == 0) {
											$Endspacer = true;
										}
										if( $Endspacer) { ?>
											</div>
										<?php } ?>
											
										<?php if( $Endspacer) { ?>
											<div class="et_pb_row et_pb_row_3">
												<div class="et_pb_column et_pb_column_4_4 et_pb_column_4  et_pb_css_mix_blend_mode_passthrough et-last-child">
													<div class="et_pb_module et_pb_divider et_pb_divider_0 et_pb_divider_position_ et_pb_space"><div class="et_pb_divider_internal"></div></div>
												</div> <!-- .et_pb_column -->
											</div> <!-- .et_pb_row -->
											
											<div class="et_pb_row et_pb_row_2">
										<?php } ?>
									<?php
									}
								}
								?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</article>
		</div>
	</div>
	
		</div>
		<div class="et_pb_row et_pb_row_3">
			<div class="et_pb_column et_pb_column_4_4 et_pb_column_4  et_pb_css_mix_blend_mode_passthrough et-last-child">
				<div class="et_pb_module et_pb_divider et_pb_divider_0 et_pb_divider_position_ et_pb_space"><div class="et_pb_divider_internal"></div></div>
			</div> <!-- .et_pb_column -->
		</div> <!-- .et_pb_row -->

		<div class="et_pb_row et_pb_row_2">
			<div class="et_pb_column et_pb_column_1_2 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough">
				<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
					<div class="et_pb_text_inner">
						<a href="<?php echo get_site_url()?>/panier">
							Mes réservations 
							<?php 
								global $woocommerce;
								echo $woocommerce->cart->get_cart_total() . " (" . $woocommerce->cart->cart_contents_count . " articles)";
							?>
						</a>
					</div>
				</div>
			</div>
		</div>
</div>


<?php 
function the_short_content( $content, $limit) {  
     /* sometimes there are &lt;p&gt; tags that separate the words, and when the tags are removed,   
     * words from adjoining paragraphs stick together.    
     * so replace the end &lt;p&gt; tags with space, to ensure unstickinees of words */  
       $content = str_replace('&lt;/p&gt;', ' ', $content);  
   $content = strip_tags($content);  
   $content = strip_shortcodes($content);  
   $ret = $content; /* if the limit is more than the length, this will be returned */  
   if (mb_strlen($content) >= $limit) {  
      $ret = mb_substr($content, 0, $limit);  
      // make sure not to cut the words in the middle:  
      // 1. first check if the substring already ends with a space  
      if (mb_substr($ret, -1) !== ' ') {  
         // 2. If it doesn't, find the last space before the end of the string  
         $space_pos_in_substr = mb_strrpos($ret, ' ');  
         // 3. then find the next space after the end of the string(using the original string)  
         $space_pos_in_content = mb_strpos($content, ' ', $limit);  
         // 4. now compare the distance of each space position from the limit  
         if ($space_pos_in_content - $limit <= $limit - $space_pos_in_substr) {  
            /* if the closest space is in the original string, take the substring from there*/  
            $ret = mb_substr($content, 0, $space_pos_in_content);  
         } else {  
            // else take the substring from the original string, but with the earlier (space) position   
            $ret = mb_substr($content, 0, $space_pos_in_substr);  
         }  
      }  
   }  
   return $ret . '...';  
}
?>