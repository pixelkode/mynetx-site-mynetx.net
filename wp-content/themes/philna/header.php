<?php
$options = get_option('philna_options');
if($options['feed'] && $options['feed_url']) {
	if (substr(strtoupper($options['feed_url']), 0, 7) == 'HTTP://') {
	$feed = $options['feed_url'];
	} else {
	$feed = 'http://' . $options['feed_url'];
	}
$feed = __($feed);
} else {
$feed = get_bloginfo('rss2_url');
}
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php _e('en'); ?>" dir="<?php _e('ltr'); ?>">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<link rel="stylesheet" href="<?php echo cdn(get_bloginfo('stylesheet_url')); ?>" type="text/css" media="screen" />
<?php if(__('ltr') == 'rtl') { ?>
<link rel="stylesheet" href="<?php echo cdn(get_bloginfo('stylesheet_directory').'/rtl.css'); ?>" type="text/css" media="screen" />
<?php } ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e('RSS 2.0 - posts', 'philna'); ?> RSS Feed" href="<?php echo $feed; ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e('RSS 2.0 - all comments', 'philna'); ?>" href="<?php bloginfo('comments_rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if(is_single()): ?>
<link rel="canonical" href="<?php echo get_permalink($post->ID);?>" />
<meta name="description" content="<?php the_post(); echo trim(strip_tags(get_the_excerpt())); rewind_posts(); ?>" />
<meta name="keywords" content="<?php
the_post();
$strTags = '';
foreach(get_the_tags() as $objTag) $strTags .= $objTag->name . ', ';
echo substr($strTags, 0, -2);
rewind_posts(); ?>" />
<?php endif; ?>
<meta name="application-name" content="<?php bloginfo('name'); ?>" />
<meta name="msapplication-tooltip" content="<?php bloginfo('description'); ?>" />
<meta name="msapplication-task" content="name=<?php _e('Home','philna')?>;action-uri=<?php echo get_option('home'); ?>;icon-uri=http://mynetx.net/favicon.ico" />
<?php
$arrPages = get_pages('depth=1&title_li=0&sort_column=menu_order&hierarchical=0');
foreach($arrPages as $objPage) {
	if($objPage->post_parent > 0)
		continue;
	echo '<meta name="msapplication-task" content="name='.$objPage->post_title.';action-uri='.get_option('home').$objPage->post_name.';icon-uri=http://mynetx.net/favicon.ico" />'."\n";
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
<h1><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
<div><?php bloginfo('description'); ?></div>
</div>


<div id="header-right">
<div id="WLSearchBoxDiv">
<?php if($options['google_cse'] && $options['google_cse_cx']) : ?>
<form action="http://www.google.com/cse" method="get">
<div>
<input type="text" class="textfield" name="q" size="24" value=""/>
<input type="hidden" name="cx" value="<?php echo $options['google_cse_cx']; ?>" />
<input type="hidden" name="ie" value="UTF-8" />
</div>
</form>
<?php else : ?>
<form action="<?php bloginfo('home'); ?>" method="get">
<div class="searchform" id="WLSearchBoxPlaceholder">
<input type="text" id="WLSearchBoxInput" class="textfield search-input bing" name="s" size="24" value="<?php _e('Search mynetx.net'); ?>" />
<input type="submit" id="WLSearchBoxButton" style="display: none" />
</div>
</form>
<?php endif; ?>
</div>

<div id="feed">
<?php if($options['feed_email'] && $options['feed_url_email']) : ?>
<a id="mailfeed" class="pop-small" href="<?php printf(htmlspecialchars($options['feed_url_email']),
		str_replace('http://feeds.feedburner.com/', '', __('http://feeds.feedburner.com/mynetx')),
		str_replace('-', '_', __('en-US'))
	);; ?>" title="<?php _e('E_mail Feed','philna')?>">E_mail Feed</a>
<?php endif;?>
<a id="rssfeed" href="<?php echo $feed ;?>" title="<?php _e('Rss Feed','philna')?>">Rss Feed</a>
</div>
</div>
</div>
<div id="nav">

<ul id="menus">
<li class="home <?php if(is_home()) echo "current_page_item";?>"><a href="<?php echo get_option('home'); ?>"><?php _e('Home','philna')?></a></li>
<?php wp_list_pages('depth=1&title_li=0&sort_column=menu_order'); ?>
</ul>
</div>


<div id="container">

<!--[if lt IE 8]> <div style=' clear: both; height: 59px; padding:15px 80px 15px; position: relative;'> <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div> <![endif]-->

<div id="content">
<script type="text/javascript">
if(location.href.indexOf("#!") > -1)
	document.getElementById("container").style.visibility = "hidden";
</script>

<?php if(in_array(__('en'), array('en', 'de'))): ?>
<div class="notice">
<?php _e('Want to become a Windows Live Insider?'); ?>&nbsp;&nbsp;
<a href="http://eepurl.com/ca2dr" class="insider pop-small"><?php
	_e('Subscribe to our monthly newsletter now! &raquo;'); ?></a>
</div>
<?php endif; ?>
