<?php get_header(); ?>

<?php

if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !isset($_SESSION['hide_language_bar'])) {
	$strAccepted = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	if(stristr($strAccepted, ';')) {
		$strAccepted = substr($strAccepted, 0, strpos($strAccepted, ';'));
	}
	$arrAccepted = explode(',', $strAccepted);
	$arrAvailable = icl_get_languages('skip_missing=1');
	if(is_array($arrAvailable)) {
		foreach($arrAccepted as $strAccepted) {
			$boolAcceptedAvailable = false;
			foreach($arrAvailable as $arrAvailableLanguage) {
				$strCode = $arrAvailableLanguage['language_code'];
				if(in_array($strCode,
						array($strAccepted, substr($strAccepted, 0, strpos($strAccepted, '-'))))) {
					if($arrAvailableLanguage['active'])
						break 2;
					$boolAcceptedAvailable = true;
					break;
				}
			}
			if($boolAcceptedAvailable) {
				global $wpdb;
				$boolIsRtl = in_array($strCode, array('ar'));
				$strMessage = 'This article is also available in <a href="%s">English</a>.';
				if($strCode != 'en')
					$strMessage = $wpdb->get_var("
						SELECT st.value
						FROM {$wpdb->prefix}icl_strings s, {$wpdb->prefix}icl_string_translations st
						WHERE s.value = '$strMessage'
						AND s.id = st.string_id
						AND st.language = '{$arrAvailableLanguage['language_code']}'");
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

if(have_posts()) : while(have_posts()) : the_post(); ?>

<div class="post">

<?php the_title(
	'<h1 class="post-title entry-title">'.get_the_post_badge('', ' ').get_the_post_source_icon('', ' '),
	'</h1>'); ?>

	<div class="info">
	<?php edit_post_link(__('Edit', 'philna'), '<span class="editpost">', '</span>'); ?>
	<span class="comments-link addcomment"><a href="#respond" title="<?php _e('Add a comment', 'philna') ?>"><?php _e('Add a comment', 'philna') ?></a></span>
	<span class="published"><?php the_time(__('F jS, Y', 'philna')) ?></span>
	<span class="post-author"><a href="<?php echo get_author_posts_url(get_the_author_ID());?>"><?php the_author();?></a></span>
	</div>

<?php /* ?>

	<?php if(defined('READSPEAK_UID')) { ?>
		<div class="entry-read"><a href="http://wr.readspeaker.com/webreader/webreader.php?cid=<?php echo READSPEAK_UID; ?>&amp;t=blog_free&amp;title=readspeaker&amp;url="
		class="post-listen"
		onclick="readpage(this.href+escape(document.location.href),1); return false"
		><?php _e('Listen'); ?></a></div>
	<?php } ?>

	<div class="entry-share">
	<a href="http://twitter.com/share" class="share twitter twitter-share-button" data-count="horizontal" data-via="mynetx" data-lang="<?php _e('en'); ?>">&nbsp;</a>
	<a name="fb_share" class="share fb" type="button_count" href="http://www.facebook.com/sharer.php"><?php _e('Share', 'philna'); ?></a>
	</div>

	<div id="WR_1" class="imgnoload"></div>

<?php */ ?>

	<div class="entry-content">
		<!-- RSPEAK_START -->
	<?php the_content(__('Continue reading','philna') . ' ' . the_title('"', '"', false)); ?>
	</div>

	<div class="entry-meta">
	<span class="cat-links"><?php the_category(', '); ?></span>
	<?php the_tags('<span class="tag-links">', ', ', '</span>');?>
	<?php the_post_source('<span class="source">', '</span>'); ?>
	</div>

</div>

	<?php endwhile; ?>

<?php comments_template('', true); ?>

<?php else: ?>

<p class="no-data"><?php _e('Sorry, no posts matched your criteria.','philna'); ?></p>

<?php endif; ?>
<?php if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera')):?>
<div id="postnavi">
<span class="prev"><?php next_post_link('%link') ?></span>
<span class="next"><?php previous_post_link('%link') ?></span>
<div class="fixed"></div>
</div>
<?php endif?>
<?php get_footer(); ?>
