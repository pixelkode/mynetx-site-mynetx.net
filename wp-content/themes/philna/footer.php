<?php get_sidebar(); $options = get_option('philna_options');?>
</div><!--container end-->
<div class="fixed"></div>
<div id="languages">
<?php $arrLangs = icl_get_languages('skip_missing=0');
	if(is_array($arrLangs)): ?>
<a href="#" id="language-selector" class="section-trigger">
<img src="<?php foreach($arrLangs as $arrLang){if($arrLang['active'])echo cdn($arrLang['country_flag_url']);}
	?>" width="18" height="12" alt="" />
</a>
<ul class="section-box" style="display: none">
<?php foreach($arrLangs as $arrLang) { ?>
<li><a class="a_languagesLink<?php
 if($arrLang['active']) echo ' a_languagesLink_active';
 echo in_array($arrLang['language_code'], array('ar', 'he')) ? ' rtl' : ' ltr';
 ?>" href="<?php echo $arrLang['url']; ?>"
 id="language-switchto-<?php echo $arrLang['language_code']; ?>"
 <?php if($arrLang['active']) echo 'style="font-weight: bold" onclick="return false"'; ?>>
 <?php echo $arrLang['native_name'].' ('.$arrLang['language_code'].')'; ?></a> </li>
<?php }
	endif; ?>
</ul>
</div>
</div><!--page end-->
</div><!--wall end-->
</div>
<div id="footerbg">
<div id="footer">
<div id="foot-content">
<span id="totop"><a href="#header">Top</a></span>
<div id="copyright">
<?php printf(__('Copyright &copy; %s', 'philna'), '2008-'.date('Y')); ?>
 <?php bloginfo('name'); ?> <?php echo $options['footer_content']; ?>
</div>
<div id="themeinfo">
<?php _e('Theme', 'philna'); ?> <a href="http://philna.com/">PhilNa</a> &middot;
<?php printf(__('Logo by %s', 'philna'), '<a href="http://twitter.com/blessedguy" style="color:#609">Mauro J&uacute;nior</a>'); ?> &middot;
<?php printf(__('Multilingual by %s', 'philna'), '<a href="http://wpml.org/" style="color:#21759b"><span style="color:#d54e21;">WP</span>ML</a>'); ?> &middot;
<?php printf(__('Hosted by %s', 'philna'), '<a href="http://www.jublowebsolutions.com/" style="color:#666"><span style="color:#548dd4">Jublo</span>WebSolutions</a>'); ?> &middot;
<?php printf(__('Licensed under %s', 'philna'), '<a href="'.__('http://creativecommons.org/licenses/by-sa/3.0/us/').'" title="'.__('Creative Commons Attribution-Share Alike 3.0 United States License').'" style="color:#9C9D00">Creative Commons</a>'); ?>
</div>
<?php global $user_level;if($user_level>8): ?>
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
<?php endif; ?>
</div>
</div>
</div>

<script type="text/javascript">/*<![CDATA[*/
var _e = {
EntireWeb: '<?php _e('Entire Web'); ?>',
host: '<?php echo $_SERVER['HTTP_HOST']; ?>',
is404: <?php echo is_404() ? 'true' : 'false'; ?>,
isHome: <?php echo is_home() ? 'true' : 'false'; ?>,
isSingle: <?php echo is_single() ? 'true' : 'false'; ?>,
lc: '<?php _e('en'); ?>',
ltr: <?php echo __('ltr') == 'ltr' ? 'true' : 'false'; ?>,
mkt: '<?php _e('en-US'); ?>',
<?php /* presence: <?php echo PRESENCE_STATUS; ?>,*/ ?>
sPrefill: '<?php _e('Search mynetx.net'); ?>',
userlevel: <?php echo $user_level; ?>,
WLE: '<?php _e('Windows Live, enhanced.'); ?>'
};

var re_name_tag = "<?php
global $current_user;
get_currentuserinfo();
echo $user_ID ? $current_user->display_name : ''; ?>";
<?php
if(isset($GLOBALS['player'])) {
	$strList = 'var arrPlayers = [';
	for($i = 0; $i < count($GLOBALS['player']); $i++) {
		$strList .= '"'.$GLOBALS['player'][$i].'", ';
	}
	$strList = substr($strList, 0, -2).'];';
	echo $strList;
} ?>

/*]]>*/</script>
<?php foreach(array(
'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js',
//'/wp-content/themes/philna/js/jquery.address.js',
'/wp-content/themes/philna/js/reinvigorate.js',
//'/wp-content/themes/philna/js/google-analyticator.js',
'/wp-content/themes/philna/js/main.js',
'/wp-content/themes/philna/js/yinheli.js'
) as $strFile): ?>
<script type="text/javascript" src="<?php echo $strFile; ?>"></script>
<?php endforeach; ?>
<?php wp_footer(); ?>

<div id="header-az">
<div id="header-az1">
<?php
displayAd(468, 1);
?>
</div>
<div id="header-az2">
<?php
displayAd(468, 2);
?>
</div>
</div>
</body>
</html>
