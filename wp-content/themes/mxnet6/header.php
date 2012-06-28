<?php
$options = get_option('philna_options');
if ($options['feed'] && $options['feed_url'])
{
    if (substr(strtoupper($options['feed_url']), 0, 7) == 'HTTP://')
    {
        $feed = $options['feed_url'];
    }
    else
    {
        $feed = 'http://' . $options['feed_url'];
    }
    $feed = __($feed);
}
else
{
    $feed = get_bloginfo('rss2_url');
}
?>
<!DOCTYPE HTML>
<html lang="<?php _e('en'); ?>" dir="<?php _e('ltr'); ?>">
<head>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php
    /*
     * Print the <title> tag based on what is being viewed.
     */
        global $page, $paged;

        wp_title('|', true, 'right');

        // Add the blog name.
        bloginfo('name');

        // Add the blog description for the home/front page.
        $site_description = get_bloginfo('description', 'display');
        if ($site_description && (is_home() || is_front_page()))
        {
            echo " | $site_description";
        }

        // Add a page number if necessary:
        if ($paged >= 2 || $page >= 2)
        {
            echo ' | ' . sprintf(__('Page %s', 'twentyten'), max($paged, $page));
        }

        ?></title>
    <link rel="stylesheet"
          href="<?php echo cdn(get_bloginfo('stylesheet_url')); ?>"
          type="text/css" media="screen" />
    <?php if (__('ltr') == 'rtl')
{
    ?>
    <link rel="stylesheet"
          href="<?php echo cdn(get_bloginfo('stylesheet_directory') . '/rtl.css'); ?>"
          type="text/css" media="screen" />
    <?php } ?>
    <link rel="alternate" type="application/rss+xml"
          title="<?php bloginfo('name'); ?> <?php _e('RSS 2.0 - posts', 'philna'); ?> RSS Feed"
          href="<?php echo $feed; ?>" />
    <link rel="alternate" type="application/rss+xml"
          title="<?php bloginfo('name'); ?> <?php _e('RSS 2.0 - all comments', 'philna'); ?>"
          href="<?php bloginfo('comments_rss2_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php if (is_single()): ?>
    <link rel="canonical" href="<?php echo get_permalink($post->ID);?>" />
    <meta name="description"
          content="<?php the_post(); echo trim(strip_tags(get_the_excerpt())); rewind_posts(); ?>" />
    <meta name="keywords" content="<?php
the_post();
        $strTags = '';
        foreach (get_the_tags() as $objTag)
        {
            $strTags .= $objTag->name . ', ';
        }
        echo substr($strTags, 0, -2);
        rewind_posts(); ?>" />
    <?php endif; ?>
    <meta name="application-name" content="<?php bloginfo('name'); ?>" />
    <meta name="msapplication-tooltip"
          content="<?php bloginfo('description'); ?>" />
    <meta name="msapplication-task"
          content="name=<?php _e('Home', 'philna')?>;action-uri=<?php echo get_option('home'); ?>;icon-uri=http://mynetx.net/favicon.ico" />
<?php
$arrPages = get_pages('depth=1&title_li=0&sort_column=menu_order&hierarchical=0');
    foreach ($arrPages as $objPage)
    {
        if ($objPage->post_parent > 0)
        {
            continue;
        }
        echo '<meta name="msapplication-task" content="name=' . $objPage->post_title . ';action-uri=' . get_option('home') . $objPage->post_name . ';icon-uri=http://mynetx.net/favicon.ico" />' . "\n";
    }
    ?>
    <meta name="msapplication-starturl" content="<?php bloginfo('url'); ?>/" />
    <?php wp_head(); ?>

    <?php /*if(defined('READSPEAK_UID')) : ?>
<script language="javascript" type="text/javascript" src="http://wr.readspeaker.com/webreader/webreader.js.php?cid=<?php echo READSPEAK_UID; ?>"></script>
<?php endif;*/ ?>
</head>
<body class="m<?php echo date('n'); ?>">

<div id="main">
    <div id="warp">
        <div id="page">

            <div id="header">
                <div id="title">
                    <h1>
                        <a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>
                    </h1>

                    <div><?php bloginfo('description'); ?></div>
                </div>
            </div>
            <div id="nav">

                <ul id="menus">
                    <!--<li class="home <?php if (is_home())
                    {
                        echo "current_page_item";
                    }?>">
                        <a href="<?php echo get_option('home'); ?>"><?php _e('Home', 'philna')?></a>
                    </li>-->
                    <li class="hotmail first">
                        <?php echo icl_link_to_element(30, 'category'); ?>
                    </li>
                    <li class="messenger">
                        <?php echo icl_link_to_element(3, 'category'); ?>
                    </li>
                    <li class="skydrive">
                        <?php echo icl_link_to_element(73, 'category', 'SkyDrive'); ?>
                    </li>
                    <li class="categories section">
                        <a href="#"
                           onclick="return false"><?php _e('Categories'); ?></a>
                        <ul class="section-box" style="display: none">
                            <?php wp_list_categories('optioncount=0&depth=3&title_li='); ?>
                        </ul>
                    </li>
                </ul>

                <ul id="menus2">
                    <li class="first">
                        <a href="<?php echo $feed;?>"><?php _e('RSS', 'philna'); ?></a>
                    </li>
                    <?php if ($options['feed_email'] && $options['feed_url_email']) : ?>
                    <li>
                        <a href="<?php printf(htmlspecialchars($options['feed_url_email']),
                                              str_replace('http://feeds.feedburner.com/', '', __('http://feeds.feedburner.com/mynetx')),
                                              str_replace('-', '_', __('en-US'))
                        ); ?>"
                           class="pop-small"><?php _e('Newsletter', 'philna'); ?></a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <?php echo icl_link_to_element(241, 'page'); ?>
                    </li>
                    <?php /* if (in_array(__('en'), array('en', 'de'))): ?>
                    <li>
                        <a href="http://eepurl.com/ca2dr" title="<?php
                           _e('Subscribe to our monthly newsletter now! &raquo;'); ?>"
                           class="insider pop-small"><?php _e('Newsletter'); ?></a>
                    </li>
                    <?php endif; */ ?>
                    <li id="languages" class="section">
                        <?php $arrLangs = icl_get_languages('skip_missing=0');
                        if (is_array($arrLangs)): ?>
                            <a href="#" id="language-selector">
                                <img src="<?php foreach ($arrLangs as $arrLang)
                                {
                                    if ($arrLang['active'])
                                    {
                                        echo cdn($arrLang['country_flag_url']);
                                    }
                                }
                                    ?>" width="18" height="12" alt="" />
                            </a>
                        <ul class="section-box" style="display: none">
                        <?php foreach ($arrLangs as $arrLang)
                            {
                                ?>
                                <li><a class="a_languagesLink<?php
                         if ($arrLang['active'])
                                {
                                    echo ' a_languagesLink_active';
                                }
                                    echo in_array($arrLang['language_code'], array('ar', 'he'))
                                            ? ' rtl' : ' ltr';
                                    ?>" href="<?php echo $arrLang['url']; ?>"
                                       id="language-switchto-<?php echo $arrLang['language_code']; ?>"
                                    <?php if ($arrLang['active'])
                                    {
                                        echo 'style="font-weight: bold" onclick="return false"';
                                    } ?>>
                                    <?php echo $arrLang['native_name'] . ' (' . $arrLang['language_code'] . ')'; ?></a>
                                </li>
                                                             <?php

                            }
                        endif; ?>
                    </ul>
                    </li>
                </ul>

                <form id="search" action="<?php bloginfo('home'); ?>">
                    <input type="text" id="search-input" name="s" size="24"
                           placeholder="<?php _e('Search'); ?>" />
                </form>
            </div>


            <div id="container">

                <!--[if lt IE 8]>
                <div style=' clear: both; height: 59px; padding:15px 80px 15px; position: relative;'>
                    <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img
                            src="http://www.theie6countdown.com/images/upgrade.jpg"
                            border="0" height="42" width="820"
                            alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a>
                </div> <![endif]-->

                <div id="content">
                    <script type="text/javascript">
                        if (location.href.indexOf("#!") > -1)
                        {
                            document.getElementById("container").style.visibility = "hidden";
                        }
                    </script>

