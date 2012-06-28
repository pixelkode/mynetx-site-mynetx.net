<?php get_sidebar();
$options = get_option('philna_options'); ?>
</div><!--container end-->
<div class="fixed"></div>
</div><!--page end-->
</div><!--wall end-->
</div>
<div id="footerbg">
    <div id="footer">
        <?php if ($wp_query->max_num_pages > 1): ?>

        <div id="pagenavi">
            <?php if (function_exists('wp_pagenavi')) : ?>
            <?php wp_pagenavi() ?>
            <?php else : ?>
            <span class="newer"><?php previous_posts_link(__('Newer Entries', 'philna')); ?></span>
            <span class="older"><?php next_posts_link(__('Older Entries', 'philna')); ?></span>
            <?php endif; ?>
            <div class="fixed"></div>
        </div>
        <?php elseif (is_single()): ?>
        <div id="postnavi">
            <span class="prev"><?php next_post_link('%link') ?></span>
            <span class="next"><?php previous_post_link('%link') ?></span>

            <div class="fixed"></div>
        </div>
        <?php endif; ?>
        <div id="foot-content">
            <ul>
                <li class="home <?php if (is_home())
                {
                    echo "current_page_item";
                }?>">
                    <a href="<?php echo get_option('home'); ?>"><?php _e('Home', 'philna')?></a>
                </li>
                <?php wp_list_pages('depth=1&title_li=0&sort_column=menu_order&link_before=-+'); ?>
            </ul>
            <div id="copyright">
                <?php printf(__('Copyright &copy; %s', 'philna'), '2008-' . date('Y')); ?>
                <?php bloginfo('name'); ?> <?php echo $options['footer_content']; ?>
            </div>
            <div id="themeinfo">
                <?php _e('Theme', 'philna'); ?> Work by <a
                    href="http://twitter.com/blessedguy">Mauro
                J&uacute;nior</a> &middot;
                <?php printf(__('Logo by %s', 'philna'), '<a href="http://twitter.com/blessedguy" style="color:#609">Mauro J&uacute;nior</a>'); ?> &middot;
                <?php printf(__('Multilingual by %s', 'philna'), '<a href="http://wpml.org/">WPML</a>'); ?> &middot;
                <?php printf(__('Hosted by %s', 'philna'), '<a href="http://www.jublo.net/">Jublo</span>WebSolutions</a>'); ?> &middot;
                <?php printf(__('Licensed under %s', 'philna'), '<a href="' . __('http://creativecommons.org/licenses/by-sa/3.0/us/') . '" title="' . __('Creative Commons Attribution-Share Alike 3.0 United States License') . '">Creative Commons</a>'); ?>
            </div>
            <?php global $user_level;if ($user_level > 8): ?>
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
    userlevel: <?php echo $user_level; ?>,
    WLE: '<?php _e('Windows Live, enhanced.'); ?>'
};

var re_name_tag = "<?php
global $current_user;
get_currentuserinfo();
echo $user_ID ? $current_user->display_name : ''; ?>";
                   <?php
                   if (isset($GLOBALS['player']))
{
    $strList = 'var arrPlayers = [';
    for ($i = 0; $i < count($GLOBALS['player']); $i++)
    {
        $strList .= '"' . $GLOBALS['player'][$i] . '", ';
    }
    $strList = substr($strList, 0, -2) . '];';
    echo $strList;
} ?>

/*]]>*/</script>
<?php foreach (array(
                   'http://code.jquery.com/jquery-latest.min.js',
                   '/wp-content/themes/mxnet6/js/reinvigorate.js',
                   '/wp-content/themes/mxnet6/js/main.js',
                   '/wp-content/themes/mxnet6/js/yinheli.js'
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

