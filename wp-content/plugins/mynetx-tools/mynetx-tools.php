<?php
/*
Plugin Name: mynetx Tools
Plugin URI: http://mynetx.net/
Description: Various tools in a custom plugin.
Version: 1.0.1
Author: mynetx Creations
Author URI: http://mynetx.net/
*/

add_option('cdnpaths_cdn_url', get_option('siteurl'));

function cdnpaths_rewrite($strText) {
	$strSource = get_option('siteurl').'/wp-';
	$strTarget = trim(get_option('cdnpaths_cdn_url')).'/wp-';
	$strText = str_replace($strSource, $strTarget, $strText);
	return $strText;
}

if(!function_exists('cdn')) {
	define('CDN_URL', get_option('cdnpaths_cdn_url'));
	function cdn($strPath) {
		return str_replace(get_option('siteurl'), CDN_URL, $strPath);
	}
	function cdnizer($strPath) {
		return $strPath;
		return cdn(get_option('siteurl').'/wp-content/plugins/mynetx-tools/cdnizer/'.rawurlencode($strPath));
	}
}

//add_action('the_content', 'cdnpaths_rewrite');

/********** WordPress Administrative ********/
add_action('admin_menu', 'cdnpaths_menu');

function cdnpaths_menu() {
	add_options_page('CDN paths', 'CDN paths', 8, __FILE__, 'cdnpaths_options');
}

function cdnpaths_options() {
if(isset($_POST['action']) && ( $_POST['action'] == 'cdnpaths_update_url')) {
	update_option('cdnpaths_cdn_url', $_POST['cdnpaths_cdn_url']);
}

?>
<div class="wrap">
<h2>CDN paths</h2>
<p>Many Wordpress plugins misbehave when linking to their JS or CSS files, and yet there is no filter to let your old posts point to a statics' site or CDN for images.<br />
Therefore this plugin replaces at any links into <code>wp-content</code> and <code>wp-includes</code> directories (except for PHP files) the <code>blog_url</code> by the URL you provide below.
That way you can either copy all the static content to a dedicated host or mirror the files at a CDN by <a href="http://knowledgelayer.softlayer.com/questions/365/How+does+Origin+Pull+work%3F" target="_blank">origin pull</a>.</p>
<p><strong style="color: red">WARNING:</strong> Test some static urls e.g., http://static.mydomain.com/wp-includes/js/prototype.js<br/>
to ensure your CDN service is fully working before saving changes.</p>
<form method="post" action="">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="cdnpaths_cdn_url">off-site URL</label></th>
<td><input type="text" name="cdnpaths_cdn_url" id="cdnpaths_cdn_url" value="<?php echo get_option('cdnpaths_cdn_url'); ?>" size="64" /></td>
<td><span class="setting-description">The new URL to be used in place of <?php echo get_option('siteurl'); ?> for rewriting.</span></td>
</tr>
</table>
<input type="hidden" name="action" value="cdnpaths_update_url" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
</p>
</form>
</div>
<?php
}

defined('AJAX') or define('AJAX', isset($_POST['ajax']));

function mynetxtools_adcontent($strText) {
	if(AJAX)
		return $strText;
	return str_replace('<!--ad-->', displayAd(336, 0, true), $strText);
}

function mynetxtools_plugins_url($full_url, $path=NULL, $plugin=NULL) {
	return cdn($full_url);
}

function mynetxtools_curl_get_content($strUrl) {
	$ch = curl_init($strUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	return curl_exec($ch);
}

function mynetxtools_getjpegsize($img_loc) {
    $handle = fopen($img_loc, "rb") or die("Invalid file stream.");
    $new_block = NULL;
    if(!feof($handle)) {
        $new_block = fread($handle, 32);
        $i = 0;
        if($new_block[$i]=="\xFF" && $new_block[$i+1]=="\xD8" && $new_block[$i+2]=="\xFF" && $new_block[$i+3]=="\xE0") {
            $i += 4;
            if($new_block[$i+2]=="\x4A" && $new_block[$i+3]=="\x46" && $new_block[$i+4]=="\x49" && $new_block[$i+5]=="\x46" && $new_block[$i+6]=="\x00") {
                // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                $block_size = hexdec($block_size[1]);
                while(!feof($handle)) {
                    $i += $block_size;
                    $new_block .= fread($handle, $block_size);
                    if($new_block[$i]=="\xFF") {
                        // New block detected, check for SOF marker
                        $sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
                        if(in_array($new_block[$i+1], $sof_marker)) {
                            // SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
                            $size_data = $new_block[$i+2] . $new_block[$i+3] . $new_block[$i+4] . $new_block[$i+5] . $new_block[$i+6] . $new_block[$i+7] . $new_block[$i+8];
                            $unpacked = unpack("H*", $size_data);
                            $unpacked = $unpacked[1];
                            $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                            $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                            return array($width, $height);
                        } else {
                            // Skip block marker and read block size
                            $i += 2;
                            $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                            $block_size = hexdec($block_size[1]);
                        }
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }
    return FALSE;
}

function mynetxtools_post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
	if(is_array($size))
		$size = 'thumbnail';
	// look if post meta is already set
	if($strImage = get_post_meta($post_id, 'stickyimage-'.$size, true)) {
		$arrImage = explode(',', $strImage);
		$strImage = $arrImage[0];
		if(count($arrImage) > 1) {
			$attr['width'] = trim($arrImage[1]);
			$attr['height'] = trim($arrImage[2]);
		}
	}
	else {
                if ($size != 'medium') {
                        return '';
                }
		// otherwise check for FB or flickr images in content
		$wp_query = new WP_Query();
		$wp_query->query('p=' . $post_id);
		while($wp_query->have_posts()) {
			$wp_query->the_post();
			$arrMatch = array();
			$strContent = get_the_content();
			if(preg_match('/http:\/\/www\.facebook\.com\/photo\.php\?[&=0-9a-z]+/', $strContent, $arrMatch)) {
				if($strEmbed = wp_oembed_get($arrMatch[0])) {
					$strContent = $strEmbed;
				}
			}
			if(preg_match('/src="http:\/\/.+\.ak\.fbcdn\.net\/.+\/\d+_(\d+)_.+_.\.jpg/', $strContent, $arrMatch)) {
				$objData = json_decode(file_get_contents(
					'https://graph.facebook.com/'.$arrMatch[1].
					'?access_token=156634014382989|18452a0b398e354512825436-551053805|NLZg3kjgZbZyRwB3zSMfqo8pzHo'));
				$arrImages = $objData->images;
				array_shift($arrImages);
				$arrImage = $size == 'medium' ? $arrImages[count($arrImages) - 3] : $arrImages[count($arrImages) - 1];
				$strImage = $arrImage->source;
				$attr['width'] = $arrImage->width;
				$attr['height'] = $arrImage->height;
				add_post_meta($post_id, 'stickyimage-'.$size,
					$strImage.','.$arrImage->width.','.$arrImage->height, true);
			}
			elseif(preg_match('/src="http:\/\/fbcdn\-sphotos\-.\.akamaihd\.net\/.+\/\d+_(\d+)_.+_.\.jpg/', $strContent, $arrMatch)) {
				$objData = json_decode(file_get_contents(
					'https://graph.facebook.com/'.$arrMatch[1].
					'?access_token=156634014382989|18452a0b398e354512825436-551053805|NLZg3kjgZbZyRwB3zSMfqo8pzHo'));
				$arrImages = $objData->images;
				array_shift($arrImages);
				$arrImage = $size == 'medium' ? $arrImages[count($arrImages) - 3] : $arrImages[count($arrImages) - 1];
				$strImage = $arrImage->source;
				$attr['width'] = $arrImage->width;
				$attr['height'] = $arrImage->height;
				add_post_meta($post_id, 'stickyimage-'.$size,
					$strImage.','.$arrImage->width.','.$arrImage->height, true);
			}
			elseif(preg_match('/src="http:\/\/farm\d+\.static\.flickr\.com\/.+\.jpg/', get_the_content(), $arrMatch)) {
				$strImage = substr($arrMatch[0], 4, -4);
				if(substr($strImage, -2, 1) == '_')
					$strImage = substr($strImage, 0, -2);
				$chrsize = $size == 'medium' ? 'm' : 't';
				$strImage .= '_'.$chrsize.'.jpg';
				$arrSize = mynetxtools_getjpegsize($strImage);
				$attr['width'] = $arrSize[0];
				$attr['height'] = $arrSize[1];
				add_post_meta($post_id, 'stickyimage-'.$size,
					$strImage.','.$arrSize[0].','.$arrSize[1], true);
			}
			elseif(preg_match('/src=\"https?:\/\/.+\.googleusercontent\.com\/.+(\/s\d+\/).+\.(jpg|gif|png)/', get_the_content(), $arrMatch)) {
//TODO
				$strImage = substr($arrMatch[0], 4, -3);
				$strSize = $arrMatch[1];
				$chrsize = $size == 'medium' ? '240' : '75';
				$strImage = str_replace($strSize, '/s' . $chrsize . '/', $strImage) . 'jpg';
				$arrSize = mynetxtools_getjpegsize($strImage);
				$attr['width'] = $arrSize[0];
				$attr['height'] = $arrSize[1];
				add_post_meta($post_id, 'stickyimage-'.$size,
					$strImage.','.$arrSize[0].','.$arrSize[1], true);
			}
		}
	}
	if(!$strImage) {
		return $html;
	}
	$html = '<img src="'.cdnizer($strImage).'" ';
	if($attr)
		foreach($attr as $key => $val)
			$html .= $key . '="' . $val . '" ';
	$html .= '/>';
	return $html;
}

add_action('the_content', 'mynetxtools_adcontent');
add_filter('plugins_url', 'mynetxtools_plugins_url', 1);
add_filter('post_thumbnail_html', 'mynetxtools_post_thumbnail_html', 1, 5);

function mynetxtools_editpost() {
	global $wpdb;
	if (function_exists('icl_register_string')) {
		icl_register_string('mynetx Tools', 'Please enter the tags.', 'Please enter the tags.');
		icl_register_string('mynetx Tools', 'Please enter the post excerpt.', 'Please enter the post excerpt.');
	}
	?>
	<script type="text/javascript">
	(function($) {
		$("#post").submit(function() {
			/*if(!$("#adv-tags-input").attr("value")) {
				alert("<?php echo icl_t('mynetx Tools', 'Please enter the tags.', 'Please enter the tags.'); ?>");
				$("#adv-tagsdiv").removeClass("closed");
				$("#adv-tags-input").focus();
				return false;
			}
			*/
			if(!$("#excerpt").attr("value")) {
				alert("<?php echo icl_t('mynetx Tools', 'Please enter the post excerpt.', 'Please enter the post excerpt.'); ?>");
				$("#postexcerpt").removeClass("closed");
				$("#excerpt").focus();
				return false;
			}
			return true;
		});
		//$("#tagsdiv-post_tag").hide();
		<?php
		if(basename($_SERVER['PHP_SELF']) == 'post-new.php' && isset($_GET['trid']) && isset($_GET['source_lang'])) {
			$intTrid = intval($_GET['trid']);
			$strLang = mysql_real_escape_string($_GET['lang']);
			$strSourceLang = mysql_real_escape_string($_GET['source_lang']);
			$intPost = $wpdb->get_var("
				SELECT element_id FROM {$wpdb->prefix}icl_translations
				WHERE element_type = 'post_post' AND trid = $intTrid AND language_code = '$strSourceLang'");
			$objPost = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID = $intPost");
			$arrTaxId = $wpdb->get_results("SELECT tt.term_taxonomy_id
				FROM {$wpdb->prefix}term_relationships tr, {$wpdb->prefix}term_taxonomy tt
				WHERE tr.object_id = {$objPost->ID}
				AND tr.term_taxonomy_id = tt.term_taxonomy_id
				AND tt.taxonomy = 'category'");
			$arrCategories = array();
			foreach($arrTaxId as $objTaxId) {
				$intCatTrid = $wpdb->get_var("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = {$objTaxId->term_taxonomy_id} AND element_type = 'tax_category'");
				$arrCategories[] = $wpdb->get_var("SELECT term_id FROM {$wpdb->prefix}term_taxonomy, {$wpdb->prefix}icl_translations WHERE term_taxonomy_id = element_id AND taxonomy = 'category' AND trid = $intCatTrid AND language_code = '$strLang'");
			}
			?>
			setTimeout(function() {
				if(typeof send_to_editor != "function") {
					setTimeout(this, 2000);
					return;
				}
				send_to_editor('<?php echo str_replace(array("\r", "\n"), array('', "<br />\\\n"), addslashes($objPost->post_content)); ?>');
				$("input[name=post_title]").val('<?php echo str_replace(array("\r", "\n"), array('', "\\\n"), addslashes($objPost->post_title)); ?>').focus();
				$("textarea[name=excerpt]").val('<?php echo str_replace(array("\r", "\n"), array('', "\\\n"), addslashes($objPost->post_excerpt)); ?>');
				<?php
				foreach($arrCategories as $intCategory) {
				?>
				$("#in-category-<?php echo $intCategory; ?>").attr("checked", true);
				<?php
				}
				?>

			}, 2000);
			<?php
		}
		?>
	})(jQuery);
	</script>
	<?php
}
add_action('edit_form_advanced', 'mynetxtools_editpost');

function displayAd($intSize = 468, $intIndex = 0, $boolReturnAd = false) {
	$strSlot = '';

	switch($intSize.':'.$intIndex) {
		case '336:0':
			$strSlot = '2759913054';
			break;
		case '468:1':
			$strSlot = '6761713612';
			break;
		case '468:2':
			$strSlot = '0234164254';
			break;
		default:
			return;
	}
	$intWidth = 0;
	$intHeight = 0;
	switch($intSize) {
		case 336:
			$intWidth = 336;
			$intHeight = 280;
			break;
		case 468:
			$intWidth = 468;
			$intHeight = 60;
			break;
	}
	ob_start();
	?>
	<script type="text/javascript"> google_ad_client = "ca-pub-9881047971881878"; google_ad_slot = "<?php echo $strSlot; ?>"; google_ad_width = <?php echo $intWidth; ?>; google_ad_height = <?php echo $intHeight; ?>; </script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	<?php
	$strAd = ob_get_contents();
	ob_end_clean();
	if($boolReturnAd) {
		return $strAd;
	}
	echo $strAd;
}

// [player]
function mynetxtools_player_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'type' => 'flash',
		'url' => '',
		'width' => '640',
		'height' => '480'
		), $atts ) );
	if(!$url)
		return '';
	switch($type) {
		case 'silverlight':
		return '
		<p><object width="'.$width.'" height="'.$height.'" type="application/x-silverlight-2" data="data:application/x-silverlight-2,">
			<param value="/WEPlayer.xap" name="source" />
			<param value="black" name="background" />
			<param value="3.0.40624.0" name="minRuntimeVersion" />
			<param value="true" name="autoUpgrade" />
			<param value="videoUrl='.$url.'" name="initParams" />
		</object></p>
			';

		case 'flash':
			if(!isset($GLOBALS['player']))
				$GLOBALS['player'] = array();
			$GLOBALS['player'][] = md5($url);
			return '
		<p><a href="'.$url.'" style="display:block;width:'.$width.'px;height:'.$height.'px" id="player-'.md5($url).'"></a></p>';
	}
}
add_shortcode('player', 'mynetxtools_player_handler');


// wle.mx

function wlemx_shortlink_wp_head() {
	global $wp_query;

	$shortlink = wlemx_get_shortlink(0, 'query');
	echo '<link rel="shortlink" href="' . $shortlink . '" />';
}

function wlemx_shortlink_header() {
	global $wp_query;

	if ( headers_sent() )
		return;

	$shortlink = wlemx_get_shortlink(0, 'query');

	header('Link: <' . $shortlink . '>; rel=shortlink');
}

function wlemx_get_shortlink_html($html, $post_id) {
	$url = wlemx_get_shortlink($post_id);
	$html .= '<input id="wlemxshortlink" type="hidden" value="' . $url . '" /><a href="#" class="button" onclick="prompt(&#39;URL:&#39;, jQuery(\'#wlemxshortlink\').val()); return false;">' . __('Get Shortlink') . '</a>';
	return $html;
}

function wlemx_get_shortlink( $id = 0, $context = 'post', $allow_slugs = true ) {
	global $wp_query;

	if ( 'query' == $context ) {
		if ( is_singular() ) {
			$id = $wp_query->get_queried_object_id();
			$context = 'post';
		} elseif ( is_front_page() ) {
			$context = 'blog';
		} else {
			return '';
		}
	}

	if ( 'blog' == $context ) {
		return 'http://wle.mx/';
	}

	$post = get_post($id);

	if ( empty($post) )
			return '';

	$post_id = $post->ID;
	$type = false;

		if ( 'page' == $post->post_type )
			$type = 'P';
		elseif ( 'post' == $post->post_type )
			$type = '';
		elseif ( 'attachment' == $post->post_type )
			$type = 'a';

	if ( $type === false )
		return '';

	return 'http://wle.mx/' . $type . $id;
}

function wlemx_get_shortlink_handler($shortlink, $id, $context, $allow_slugs) {
	return wlemx_get_shortlink($id, $context, $allow_slugs);
}

if ( ! function_exists('wp_get_shortlink') ) {
	// Register these only for WP < 3.0.
	add_action('wp_head', 'wlemx_shortlink_wp_head');
	add_action('wp', 'wlemx_shortlink_header');
	add_filter( 'get_sample_permalink_html', 'wlemx_get_shortlink_html', 10, 2 );
} else {
	// Register a shortlink handler for WP >= 3.0.
	add_filter('get_shortlink', 'wlemx_get_shortlink_handler', 10, 4);
}

