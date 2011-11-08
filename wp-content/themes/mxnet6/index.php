<?php get_header();
$options = get_option('philna_options'); ?>
<?php if ($options['notice'] && $options['notice_content'] && !is_bot()) : ?>
<div id="notice">
    <?php echo($options['notice_content']); ?>
</div>
<?php endif; ?>
<?php

if (have_posts())
{
    the_post();
    $intLatestPostTime = get_the_time('U');
    rewind_posts();
    define('IS_OUTDATED', time() - $intLatestPostTime > 28 * 86400 && $_SERVER['REQUEST_URI'] == '/');
    if (IS_OUTDATED)
    {
        echo '<div class="notice not-updated">' .
             __('This website has not been updated recently. You will find up-to-date news on the <a href="http://mynetx.net/">English version</a>.', 'philna') .
             '<br />' .
             __('You want to participate?', 'philna') . ' <a href="#">' .
             __('Send a mail to get more info &raquo;', 'philna') .
             '</a></div>';
    }
    else
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_SESSION['hide_language_bar']))
        {
            $strAccepted = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            if (stristr($strAccepted, ';'))
            {
                $strAccepted = substr($strAccepted, 0, strpos($strAccepted, ';'));
            }
            $arrAccepted = explode(',', $strAccepted);
            $arrAvailable = icl_get_languages('skip_missing=0');
            if (is_array($arrAvailable))
            {
                foreach ($arrAccepted as $strAccepted)
                {
                    $boolAcceptedAvailable = false;
                    foreach ($arrAvailable as $arrAvailableLanguage)
                    {
                        $strCode = $arrAvailableLanguage['language_code'];
                        if (in_array($strCode,
                                     array($strAccepted, substr($strAccepted, 0, strpos($strAccepted, '-'))))
                        )
                        {
                            if ($arrAvailableLanguage['active'])
                            {
                                break 2;
                            }
                            $boolAcceptedAvailable = true;
                            break;
                        }
                    }
                    if ($boolAcceptedAvailable)
                    {
                        global $wpdb;
                        $boolIsRtl = in_array($strCode, array('ar'));
                        $strMessage = 'This website is also available in <a href="%s">English</a>.';
                        if ($strCode != 'en')
                        {
                            $strMessage = $wpdb->get_var("
                                SELECT st.value
                                FROM {$wpdb->prefix}icl_strings s, {$wpdb->prefix}icl_string_translations st
                                WHERE s.value = '$strMessage'
                                AND s.id = st.string_id
                                AND st.language = '{$arrAvailableLanguage['language_code']}'");
                        }
                        /*
                              echo '<div id="notice" class="language-bar '.($boolIsRtl ? 'rtl' : 'ltr').'">'.
                                  sprintf($strMessage, $arrAvailableLanguage['url']).
                                  '</div>';
                              */
                        break;
                    }
                }
            }
        }
    }
}
if (have_posts()) : $i = -1;
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
            <?php the_excerpt(); ?>
        </div>

        <div class="entry-meta">
        </div>

    </div>

    <?php if ($i == 2) : ?>
        <div class="post ad">
            <?php displayAd(336, 0); ?>
        </div>
        <?php endif; ?>

    <?php endwhile; ?>

<?php else: ?>

<p class="no-data"><?php _e('Sorry, no posts matched your criteria.', 'philna'); ?></p>

<?php endif; ?>

<?php get_footer(); ?>