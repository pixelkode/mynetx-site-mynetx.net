
<div class='wordtwit-setting' id='twitboard'>
	<div class="box-holder round-6" id="right-now-box">

		<h3><?php _e( "Right Now", "wordtwit-pro" ); ?></h3>

		<p class="sub"><?php _e( "At a Glance", "wordtwit-pro" ); ?></p>

		<table class="fonty">
			<tbody>
				<tr>
					<td class="box-table-number"><?php wordtwit_the_bloginfo( 'published_tweets' ); ?></td>
					<td class="box-table-text"><a href="admin.php?page=tweet_queue"><?php echo _n( "Published Tweet", "Published Tweets", wordtwit_get_bloginfo( 'published_tweets' ), "wordtwit-pro" ); ?></a></td>
				</tr>	
				<tr>
					<td class="box-table-number"><?php wordtwit_the_bloginfo( 'scheduled_tweets' ); ?></td>
					<td class="box-table-text"><a href="admin.php?page=tweet_queue"><?php echo _n( "Scheduled Tweet", "Scheduled Tweets", wordtwit_get_bloginfo( 'scheduled_tweets' ), "wordtwit-pro" ); ?></a></td>
				</tr>
				<tr>
					<td class="box-table-number"><?php wordtwit_the_bloginfo( 'total_accounts' ); ?></td>
					<td class="box-table-text"><a href="admin.php?page=wordtwit_account_configuration"><?php echo _n( "Twitter Account", "Twitter Accounts", wordtwit_get_bloginfo( 'total_accounts' ), "wordtwit-pro" ); ?></a></td>
				</tr>
				<tr>
					<td class="box-table-number"><?php wordtwit_the_bloginfo( 'licenses_remaining' ); ?></td>
					<td class="box-table-text"><a href="#" rel="licenses" class="wordtwit-admin-switch"><?php echo _n( "License Remaining", "Licenses Remaining", wordtwit_get_bloginfo( 'licenses_remaining' ), "wordtwit-pro" ); ?></a></td>
				</tr>
			</tbody>
		</table>

		<div id="touchboard-ajax">&nbsp;</div>
		
	</div><!-- box-holder -->

	<div class="box-holder loading round-6" id="blog-news-box">
		<h3><?php _e( "WordTwit News", "wordtwit-pro" ); ?></h3>

		<p class="sub"><?php _e( "From the BraveNewCode Blog", "wordtwit-pro" ); ?></p>

		<div id="blog-news-box-ajax"></div>

	</div><!-- box-holder -->

			<br class="clearer" />

		<?php if ( wordtwit_has_proper_auth() && !wordtwit_has_license() ) { ?>
			<div id="unlicensed-board" class="partial round-6">
				<strong><?php _e( "This copy of WordTwit Pro is partially activated.", "wordtwit-pro" ); ?></strong>
				<a href="#pane-5" id="target-pane-2" class="partial wordtwit-admin-switch" rel="licenses"><?php _e( "Add a site license &raquo;", "wordtwit-pro" ); ?></a>
			</div>		
		<?php } ?>

		<?php if ( !wordtwit_has_proper_auth() ) { ?>
			<div id="unlicensed-board" class="round-6">
				<strong><?php echo sprintf( __( "This copy of WordTwit Pro %s is unlicensed.", "wordtwit-pro" ), wordtwit_the_bloginfo( 'version' ) ); ?></strong>
				<a href="#pane-2" class="wordtwit-admin-switch" rel="account"><?php _e( "Get started with Activation &raquo;", "wordtwit-pro" ); ?></a>
			</div>
		<?php } ?>

</div><!-- wordtwit-setting -->
