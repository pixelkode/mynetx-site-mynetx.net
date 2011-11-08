<?php /*
Example template for use with WPML (WP Multilingual, http://wpml.org)
Author: mitcho (Michael Yoshitaka Erlewine)
*/

if (function_exists("icl_register_string")) {
  icl_register_string("Yet Another Related Posts Plugin","related posts header","Related Posts");
  icl_register_string("Yet Another Related Posts Plugin","no related posts message","No related posts.");
}

?>
<!-- RSPEAK_STOP -->
<?php wp_link_pages(); ?>

<?php if ($related_query->have_posts()):?>

<h3><?php echo (function_exists("icl_t") ? icl_t("Yet Another Related Posts Plugin","related posts header","Related Posts") : "Related Posts") ?></h3>
	<ul>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
<?php endif; ?>