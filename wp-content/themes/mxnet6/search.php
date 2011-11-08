<?php get_header(); ?>
<?php $options = get_option('philna_options'); ?>
<div class="position"><?php _e('Search Results', 'philna'); ?><strong>
    <?php printf(__('Keyword: &#8216;%1$s&#8217;', 'philna'), wp_specialchars($s, 1)); ?></strong>
</div>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="post">
    <h2 class="post-title entry-title">
        <?php the_post_badge('', ' '); ?>
        <?php the_post_source_icon('', ' '); ?>
        <?php the_title('<a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
    </h2>

    <div class="info">
        <span class="cat-links"><?php the_category(', '); ?></span>
        <span class="published"><?php the_time(__('F jS, Y', 'philna')) ?></span>
    </div>

    <div class="entry-content excerpt-clickable">
        <?php the_post_thumbnail(array(75, 75), array('class' => 'alignright thm')); ?>
        <?php the_excerpt(); ?>
    </div>

    <div class="entry-meta">
    </div>

</div>

<?php endwhile; ?>

<?php else: ?>

<p class="no-data"><?php _e('Sorry, no posts matched your criteria.', 'philna'); ?></p>

<?php endif; ?>
<?php get_footer(); ?>
