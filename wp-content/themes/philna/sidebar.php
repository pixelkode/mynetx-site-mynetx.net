</div><!--content end-->
<div id="sidebar">
<?php
$options = get_option('philna_options');
if( $options['showcase_content'] && (
($options['showcase_registered'] && $user_ID) ||
($options['showcase_commentator'] && !$user_ID && isset($_COOKIE['comment_author_'.COOKIEHASH])) ||
($options['showcase_visitor'] && !$user_ID && !isset($_COOKIE['comment_author_'.COOKIEHASH]))
) ) :
?>
<?php if(!is_bot()):?>
<div class="sidebar-top sidebar_notice">
<?php if($options['showcase_caption']) : ?>
<?php if($options['showcase_title']){ echo "<h3>";echo($options['showcase_title']);echo "</h3>";}?>
<?php endif; ?>
<div id="sidebar_notice">
<?php echo($options['showcase_content']); ?>
</div>
</div>
<?php endif;?>
<?php endif;?>

<?php
if(is_single())
	$current_post = $post->ID;
$my_query = new WP_Query('posts_per_page=5');

if($my_query->have_posts()): ?>
<div class="sidebar-top widget_posts">
<div class="sidebar-imgteaser">
<?php $i = -1; while($my_query->have_posts()): $my_query->the_post(); if(is_single() && $current_post == $post->ID) continue; $i++; ?>
<div class="excerpt-clickable sy sy-<?php echo $i; ?>" title="<?php echo strip_tags(get_the_excerpt()); ?>"
<?php

$strImage = get_the_post_thumbnail($post->ID, 'medium');
if(stristr($strImage, 'src="')) {
	$intStart = strpos($strImage, 'src="') + 5;
	$intStop = strpos($strImage, '"', $intStart + 5);
	$strImage = substr($strImage, $intStart, $intStop - $intStart);
}
if($strImage)
	echo ' style="background-image: url('.$strImage.')"';

?>>
<h2><a href="<?php the_permalink(); ?>" class="target" rel="bookmark"><?php the_title(); ?></a></h2>
</div>
<?php endwhile; ?>
</div>
</div>
<?php
endif;
?>

<div class="widget">

<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('east_sidebar')):?>

	<div class="widget_">
		<h3><?php _e('Categories','philna')?></h3>
			<ul>
			<?php wp_list_categories('optioncount=0&depth=3&title_li='); ?>
			</ul>
	</div>
	<div class="widget_">
		<h3><?php _e('Links','philna')?></h3>
			<ul>
			<?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
			</ul>
	</div>

<?php endif;//east_sidebar ?>

<div class="fixed"></div>
</div>
</div>

