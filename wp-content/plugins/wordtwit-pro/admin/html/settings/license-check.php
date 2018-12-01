<?php
	global $wordtwit_pro;
	$wordtwit_pro->bnc_api->verify_site_license( 'wordtwit-pro' );
?>

<?php if ( wordtwit_has_proper_auth() ) { ?>
	<?php if ( wordtwit_has_license() ) { ?>
	<p class="license-valid round-6"><span><?php _e( 'License accepted, thank you for supporting WordTwit Pro!', 'wordtwit-pro' ); ?></span></p>	
	<?php } else { ?>
	<p class="license-partial round-6"><span><?php echo sprintf( __( 'Your Account E-Mail and License Key have been accepted. <br />Next, %sconnect a site license%s to this domain to enable support and automatic upgrades.', 'wordtwit-pro' ), '<a href="#pane-5" class="configure-licenses wordtwit-admin-switch" rel="licenses">', '</a>' ); ?></span></p>
	<?php } ?>
<?php } else { ?>
	<?php if ( wordtwit_credentials_invalid() ) { ?>
	<p class="license-invalid bncid-failed round-6"><span><?php echo __( 'This Account E-Mail/License Key combination you have entered was rejected by the BraveNewCode server. Please try again.' ); ?></span></p>	
	<?php } else { ?>
	<p class="license-invalid round-6"><span><?php echo sprintf( __( 'Please enter your Account E-Mail and License Key to begin the license activation process, or %spurchase a license &raquo;%s', 'wordtwit-pro' ), '<a href="http://www.bravenewcode.com/store/plugins/wordtwit-pro/?utm_source=wordtwit_pro&utm_medium=web&utm_campaign=admin-purchase">', '</a>' ); ?></span></p>
	<?php } ?>
<?php } ?>