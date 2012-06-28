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
				echo '<div class="notice language-bar '.($boolIsRtl ? 'rtl' : 'ltr').'">'.
					'<img src="'.$arrAvailableLanguage['country_flag_url'].'" '.
					'width="18" height="12" />&nbsp; '.
					sprintf($strMessage, $arrAvailableLanguage['url']).
					'</div>';
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

	<div class="entry-content">
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

<?php get_footer(); ?>
