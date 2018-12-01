<?php global $wordtwit_pro; $current_scheme = get_user_option('admin_color'); ?>

<form method="post" action="" id="bnc-form" class="<?php if ( $wordtwit_pro->locale ) echo 'locale-' . strtolower( $wordtwit_pro->locale ); ?>">
	<div id="bnc" class="<?php echo $current_scheme; ?> <?php if ( WORDTWIT_PRO_BETA ) { echo 'beta'; } else { echo 'normal'; } ?> wrap">
		<div id="wordtwit-admin-top">
			<h2><?php echo WORDTWIT_PRODUCT_NAME . ' <span class="version">' . WORDTWIT_VERSION; ?></span></h2>
			<div id="wordtwit-api-server-check"></div>
			<?php wordtwit_save_reset_notice(); ?>
		</div>		
			
		<div id="wordtwit-admin-form">		
			<ul id="wordtwit-top-menu">
			
				<?php do_action( 'wordtwit_pre_menu' ); ?>
				
				<?php $pane = 1; ?>
				<?php foreach( $wordtwit_pro->tabs as $name => $value ) { ?>
					<li><a id="pane-<?php echo $pane; ?>" class="pane-<?php echo wordtwit_string_to_class( $name ); ?>" href="#"><?php echo $name; ?></a></li>
					<?php $pane++; ?>
				<?php } ?>
		
				<?php do_action( 'wordtwit_post_menu' ); ?>
				
				<li>
					<div class="wordtwit-ajax-results blue-text" id="ajax-loading" style="display:none"><?php _e( "Loading...", "wordtwit-pro" ); ?></div>
					<div class="wordtwit-ajax-results blue-text" id="ajax-saving" style="display:none"><?php _e( "Saving...", "wordtwit-pro" ); ?></div>
					<div class="wordtwit-ajax-results green-text" id="ajax-saved" style="display:none"><?php _e( "Done", "wordtwit-pro" ); ?></div>
					<div class="wordtwit-ajax-results red-text" id="ajax-fail" style="display:none"><?php _e( "Oops! Try saving again.", "wordtwit-pro" ); ?></div>
					<br class="clearer" />
				</li>
			</ul>
					
			<div id="wordtwit-tabbed-area"  class="round-6 box-shadow">
				<?php wordtwit_show_tab_settings(); ?>
			</div>
			
			<br class="clearer" />
			
			<input type="hidden" name="wordtwit-admin-tab" id="wordtwit-admin-tab" value="" />
			<input type="hidden" name="wordtwit-admin-menu" id="wordtwit-admin-menu" value="" />
		</div>
		<input type="hidden" name="wordtwit-admin-nonce" value="<?php echo wp_create_nonce( 'wordtwit-post-nonce' ); ?>" />

		<p class="submit" id="bnc-submit">
			<input class="button-primary" type="submit" name="wordtwit-submit" tabindex="1" value="<?php _e( "Save Changes", "wordtwit-pro" ); ?>" />
		</p>
		
		<p class="submit" id="bnc-submit-reset">
			<input class="button" type="submit" name="wordtwit-submit-reset" tabindex="2" value="<?php _e( "Reset Settings", "wordtwit-pro" ); ?>" />
			<span id="saving-ajax">
				<?php _e( "Saving", "wordtwit-pro" ); ?>&hellip; <img src="<?php echo WORDTWIT_URL . '/admin/images/ajax-loader.gif'; ?>" alt="ajax image" />
			</span>
		</p>
	
		<br class="clearer" />
		
		<ul id="link-menu">
			<li><?php echo sprintf( __( "WordTwit Pro is a trademark of %sBraveNewCode Inc.%s", "wordtwit-pro" ), '<a href="http://www.bravenewcode.com">', '</a>' ); ?> |</li>
			<li><a href="http://www.bravenewcode.com/support/profile/"><?php _e( "My Account", "wordtwit-pro" ); ?></a> |</li>
			<li><a href="http://www.bravenewcode.com/docs/"><?php _e( "Documentation", "wordtwit-pro" ); ?></a> |</li>
			<li><a href="http://www.bravenewcode.com/support/"><?php _e( "Support Forums", "wordtwit-pro" ); ?></a> |</li>
			<li><a href="http://twitter.com/bravenewcode"><?php _e( "Twitter &rsaquo; BNC", "wordtwit-pro" ); ?></a> |</li>
			<li><a href="http://www.bravenewcode.com/general/terms/"><?php _e( "Terms &amp; Legal", "wordtwit-pro" ); ?></a></li>
		</ul>
		<div class="poof">&nbsp;</div>
		<div id="wordtwit-tooltip" class="round-12">&nbsp;</div>	
		<!--  <div id="wordtwit-tooltip-left" class="round-12">&nbsp;</div>	-->
		<!-- <div id="wordtwit-tooltip-center" class="round-12">&nbsp;</div>	 -->
	</div> <!-- wordtwit-admin-area -->
</form>