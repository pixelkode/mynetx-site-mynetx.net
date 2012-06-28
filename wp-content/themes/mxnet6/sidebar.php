</div><!--content end-->
<div id="sidebar">
    <?php
    $options = get_option('philna_options');
    if ($options['showcase_content'] && (
            ($options['showcase_registered'] && $user_ID) ||
            ($options['showcase_commentator'] && !$user_ID && isset($_COOKIE['comment_author_' . COOKIEHASH])) ||
            ($options['showcase_visitor'] && !$user_ID && !isset($_COOKIE['comment_author_' . COOKIEHASH]))
    )
    ) :
        ?>
        <?php if (!is_bot()): ?>
        <div class="sidebar-top sidebar_notice">
            <?php if ($options['showcase_caption']) : ?>
            <?php if ($options['showcase_title'])
            {
                echo "<h3>";
                echo($options['showcase_title']);
                echo "</h3>";
            } ?>
            <?php endif; ?>
            <div id="sidebar_notice">
                <?php echo($options['showcase_content']); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    <?php
    if (is_single()):
    ?>
    <div class="widget">
	<p class="published"><?php the_time(__('F jS, Y', 'philna')) ?></p>
	<p class="post-author"><a href="<?php echo get_author_posts_url(get_the_author_ID());?>"><?php the_author();?></a></p>
    </div>
    <?php
    endif;
    ?>
<?php /*
    <div class="widget social-share">
        <?php
        if (function_exists('kc_add_social_share')) {
        ?>
	<div class="fb">
		<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo rawurlencode($strUri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&amp;layout=box_count&amp;show_faces=false&amp;action=like&amp;font=segoe+ui&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
	</div>

	<div class="gp">
		<g:plusone size="tall"></g:plusone>
	</div>

	<div class="tw">
		<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="mynetx" data-lang="<?php _e('en'); ?>">Tweet</a>
	</div>			
	<div style="clear:both"></div>
        <?php
        }
        ?>
    </div>
*/ ?>


    <?php
    if (is_single())
{
    $current_post = $post->ID;
}
    $my_query = new WP_Query('posts_per_page=5');

    if ($my_query->have_posts()): ?>
        <div class="sidebar-top widget_posts">
            <div class="sidebar-imgteaser">
                <?php $i = -1; while ($my_query->have_posts()): $my_query->the_post();
                if (is_single() && $current_post == $post->ID)
                {
                    continue;
                }
                $i++; ?>
                <div class="excerpt-clickable sy sy-<?php echo $i; ?>"
                     title="<?php echo strip_tags(get_the_excerpt()); ?>">
                    <div <?php

                        $strImage = get_the_post_thumbnail($post->ID, 'medium');
                        if (stristr($strImage, 'src="'))
                        {
                            $intStart = strpos($strImage, 'src="') + 5;
                            $intStop = strpos($strImage, '"', $intStart + 5);
                            $strImage = substr($strImage, $intStart, $intStop - $intStart);
                        }
                        if ($strImage)
                        {
                            echo ' style="background-image: url(' . $strImage . ')"';
                        }

                        ?>>
                        <h2><a href="<?php the_permalink(); ?>" class="target"
                               rel="bookmark"><?php the_title(); ?></a></h2>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        endif;
    ?>
</div>

