<?php get_header(); ?>

<?php $options = get_option('philna_options'); ?>


<div class="position"><?php _e('Archive:', 'philna'); ?>
    <strong>
        <?php
            // If this is a category archive
        if (is_category())
        {
            printf(__('&#8216;%1$s&#8217; Category', 'philna'), single_cat_title('', false));
            // If this is a tag archive
        }
        elseif (is_tag())
        {
            printf(__('Posts Tagged &#8216;%1$s&#8217;', 'philna'), single_tag_title('', false));
            // If this is a daily archive
        }
        elseif (is_day())
        {
            printf(__('%1$s', 'philna'), get_the_time(__('F jS, Y', 'philna')));
            // If this is a monthly archive
        }
        elseif (is_month())
        {
            printf(__('%1$s', 'philna'), get_the_time(__('F, Y', 'philna')));
            // If this is a yearly archive
        }
        elseif (is_year())
        {
            printf(__('%1$s', 'philna'), get_the_time(__('Y', 'philna')));
            // If this is an author archive
        }
        elseif (is_author())
        {
            _e('Author Archive', 'philna');
            // If this is a paged archive
        }
        elseif (isset($_GET['paged']) && !empty($_GET['paged']))
        {
            _e('Blog Archives', 'philna');
        }
        ?>
    </strong>
</div>


<?php if (have_posts()) : $i = -1;
    while (have_posts()) : $i++;
        the_post(); ?>

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
            <?php the_excerpt();?>
        </div>

        <div class="entry-meta">
        </div>

    </div>

    <?php if ($i == 2) : ?>
        <div class="post">
            <?php displayAd(336); ?>
        </div>
        <?php endif; ?>

    <?php endwhile; ?>

<?php else: ?>

<p class="no-data"><?php _e('Sorry, no posts matched your criteria.', 'philna'); ?></p>

<?php endif; ?>
<?php get_footer(); ?>