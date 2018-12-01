<?php $settings = wordtwit_get_settings(); ?>
<?php wordtwit_setup_bnc_api(); ?>
<ul>
	<li>
		<?php _e( "Twitter.com Status", "wordtwit-pro" ); ?>: 
		<?php if ( wordtwit_is_twitter_api_up() ) { ?>
			<span class="green-text">
				<?php _e( "No issues", "wordtwit-pro" ); ?>
			</span>
		<?php } else { ?>
			<span class="red-text">
				<?php _e( "API connection failed", "wordtwit-pro" ); ?>
			</span>
		<?php } ?>
	</li>
	<li>
		<?php _e( "WordTwit Version", "wordtwit-pro" ); ?>: <span><?php echo WORDTWIT_VERSION; ?></span> 	
		<?php if ( wordtwit_is_upgrade_available() ) { ?>
		<a id="upgrade-link" href="plugins.php?plugin_status=upgrade"><?php _e( "Upgrade Available", "wordtwit-pro" ); ?> &raquo;</a></li>
		<?php } else { ?>
		<span class="current grey-999-text">(<?php _e( "Up to date", "wordtwit-pro" ); ?>)</span>
		<?php } ?>
	</li>
	
	<?php if ( wordtwit_has_proper_auth() ) { ?>
		<?php if ( wordtwit_has_license() ) { ?>
		<li>
			<?php _e( "Plugin Status", "wordtwit-pro" ); ?>: <span class="green-text"><?php _e( "LICENSED", "wordtwit-pro" ); ?></span> | <em><?php _e( "Thank you for supporting us!", "wordtwit-pro" ); ?></em>
		</li>
		<?php } else { ?>
		<li>
			<?php _e( "Plugin Status", "wordtwit-pro" ); ?>: <span class="status-unl"><?php _e( "ACTIVATION REQUIRED", "wordtwit-pro" ); ?></span> | <a href="#pane-2" class="wordtwit-admin-switch blue-text" rel="licenses"><?php _e( "Activate license", "wordtwit-pro" ); ?> &raquo;</a>
		</li>
		<?php } ?>	
	<?php } else { ?>	
		<li>
			<?php _e( "Plugin Status", "wordtwit-pro" ); ?>: <span class="status-unl"><?php _e( "UNLICENSED", "wordtwit-pro" ); ?></span> | 
			<a href="http://www.bravenewcode.com/store/plugins/wordtwit-pro/?utm_source=wordtwit_pro&utm_medium=web&utm_campaign=admin-purchase" target="_blank"><?php _e( "Purchase a license", "wordtwit-pro" ); ?>  &raquo;</a>
		</li>
	<?php } ?>	
</ul>		