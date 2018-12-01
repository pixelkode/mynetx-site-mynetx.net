<?php wordtwit_setup_bnc_api(); ?>
<div id="wordtwit-admin-profile">
	<h5><?php _e( "Active Site Licenses", "wordtwit-pro" ); ?></h5>
	
	<?php if ( wordtwit_has_site_licenses() ) { ?>
		<p><?php _e( "You have activated these sites for automatic upgrades & support:", "wordtwit-pro" ); ?></p>
		<ol class="round-6">
			<?php while ( wordtwit_has_site_licenses() ) { ?>
				<?php wordtwit_the_site_license(); ?>
				<li <?php if ( wordtwit_can_delete_site_license() ) { echo 'class="green-text"'; } ?>>
					<?php wordtwit_the_site_license_name(); ?> <?php if ( wordtwit_can_delete_site_license() ) { ?><a class="wordtwit-remove-license" href="#" rel="<?php wordtwit_the_site_license_name(); ?>" title="<?php _e( "Remove license?", "wordtwit-pro" ); ?>">(x)</a><?php } ?></li>
			<?php } ?>
		</ol>
	<?php } ?>
	<!-- end site licenses -->
		
		<p><?php echo sprintf( __( "%s%d%s license(s) remaining.", "wordtwit-pro" ), '<strong>', wordtwit_get_site_licenses_remaining(), '</strong>' ); ?></p>
		
		<?php if ( !wordtwit_get_site_licenses_remaining() ) { ?>
		 	<p class="inline-button">
		 	<a href="http://www.bravenewcode.com/store/plugins/wordtwit-pro/?utm_source=wordtwit-pro&utm_medium=web&utm_campaign=admin-upgrades" id="upgrade-license" class="button round-24" target="_blank"><?php _e( "Purchase More Licenses", "wordtwit-pro" ); ?></a>
		 	</p>
		<?php } ?>

	<?php if ( wordtwit_get_site_licenses_remaining() ) { ?>
		<?php if ( !wordtwit_is_licensed_site() ) { ?>
			<?php _e( "You have not activated a license for this WordPress installation.", "wordtwit-pro" ); ?><br />
			<p class="inline-button">
				<a class="wordtwit-add-license round-24 button" id="partial-activation" href="#"><?php _e( "Activate This WordPress installation &raquo;", "wordtwit-pro" ); ?></a>
			</p>
		<?php } ?>
	<?php } ?>

	<?php if ( wordtwit_get_site_licenses_in_use() ) { ?>
		<?php if ( wordtwit_can_do_license_reset() && false ) { ?>
			<p class="inline-button">
				<a href="#" id="reset-licenses" class="button"><?php _e( "Reset Licenses Now", "wordtwit-pro" ); ?></a>
			</p>
			<?php if ( false ) { ?>
			<br /><br />
			<small>
				<?php echo sprintf( __( "* You can reset all support and auto-upgrade licenses once every %d days.", "wordtwit-pro" ), wordtwit_get_license_reset_days() ); ?>
			</small>
			<?php } ?>
		<?php } else { ?>
			<?php if ( false ) { ?>
			<br /><br />
			<small>
				<?php echo sprintf( __( "You will be able to reset licenses again in %d day(s).", "wordtwit-pro" ), wordtwit_get_license_reset_days_until() ); ?>
			</small>
			<?php } ?>
		<?php } ?>	
	<?php } ?>

	<br class="clearer" />
</div>
