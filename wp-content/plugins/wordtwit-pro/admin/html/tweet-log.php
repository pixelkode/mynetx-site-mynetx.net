<?php require_once( WORDTWIT_DIR . '/admin/template-tags/tweet-log.php' ); ?>
<div id="wordtwit-tweet-log" class="wrap">
	<h2><?php _e( 'Tweet Log', 'wordtwit-pro' ); ?> </h2>

	<ul class="subsubsub">
		<li class="all"><a href="<?php echo add_query_arg( 'tpage', null, add_query_arg( 'filter', 'all' ) ); ?>"<?php if ( wordtwit_get_tweet_log_filter() == 'all' ) echo ' class="current"'; ?>><?php _e( 'All', 'wordtwit-pro' ); ?></a> <span class="count">(<?php echo wordtwit_get_tweet_log_total_entries( 'all' ); ?>)</span> | </li>
		<li class="publish"><a href="<?php echo add_query_arg( 'tpage', null, add_query_arg( 'filter', 'publish' ) ); ?>"<?php if ( wordtwit_get_tweet_log_filter() == 'publish' ) echo ' class="current"'; ?>><?php _e( 'Published', 'wordtwit-pro' ); ?></a> <span class="count">(<?php echo wordtwit_get_tweet_log_total_entries( 'publish' ); ?>)</span> | </li>
		<li class="scheduled"><a href="<?php echo add_query_arg( 'filter', 'future' ); ?>"<?php if ( wordtwit_get_tweet_log_filter() == 'future' ) echo ' class="current"'; ?>><?php _e( 'Scheduled', 'wordtwit-pro' ); ?> <span class="count">(<?php echo wordtwit_get_tweet_log_total_entries( 'future' ); ?>)</span></a> | </li>
		<li class="errors"><a href="<?php echo add_query_arg( 'filter', 'errors' ); ?>"<?php if ( wordtwit_get_tweet_log_filter() == 'errors' ) echo ' class="current"'; ?>><?php _e( 'Failed', 'wordtwit-pro' ); ?></a> <span class="count">(<?php echo wordtwit_get_tweet_log_total_entries( 'errors' ); ?>)</span></li>
	</ul>
	
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-cb check-column"><!-- <input type="checkbox" /> -->&nbsp;</th>
				<th scope="col" class="manage-column desc"><?php _e( 'Tweet', 'wordtwit-pro' ); ?></th>
				<th scope="col" id="wordtwit-status-col" class="manage-column desc"><?php _e( 'Publish Status', 'wordtwit-pro' ); ?></th>
				<th scope="col" id="wordtwit-accounts-col" class="manage-column desc"><?php _e( 'Twitter Account', 'wordtwit-pro' ); ?></th>
				<th scope="col" id="wordtwit-date-col" class="manage-column desc"><?php _e( 'Date/Time', 'wordtwit-pro' ); ?></th>		
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column"><!--<input type="checkbox" /> -->&nbsp;</th>
				<th scope="col" class="manage-column desc"><?php _e( 'Tweet', 'wordtwit-pro' ); ?></th>
				<th scope="col" class="manage-column desc"><?php _e( 'Publish Status', 'wordtwit-pro' ); ?></th>				
				<th scope="col" class="manage-column desc"><?php _e( 'Twitter Account', 'wordtwit-pro' ); ?></th>
				<th scope="col" class="manage-column desc"><?php _e( 'Date/Time', 'wordtwit-pro' ); ?></th>				
			</tr>
		</tfoot>		
		<tbody>	
			<?php global $post; ?>
			<?php if ( !wordtwit_have_tweet_log() ) { ?>
				<tr>
					<th scope="row" class="check-column">&nbsp;</th>
					<td><?php _e( 'No tweets found', 'wordtwit-pro' ); ?>.<br /><?php _e( "When you publish tweets they'll appear here.", 'wordtwit-pro' ); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?php } else { ?>
				<?php while ( wordtwit_have_tweet_log() ) { ?>
					<?php wordtwit_the_tweet_log(); ?>		
					<tr>
						<th scope="row" class="check-column"><input type="checkbox" /></th>						
						<td>
							<?php wordtwit_the_tweet_log_title(); ?>
							<div class="row-actions">
								<?php if ( wordtwit_get_tweet_log_status() == 'future' ) { ?>
								<a href="<?php echo wordtwit_get_tweet_log_tweet_now_link(); ?>"><?php _e( 'Tweet Now', 'wordtwit-pro' ); ?></a> |
								<?php } ?>
								<?php if ( wordtwit_get_tweet_log_status() == 'publish' && wordtwit_tweet_log_can_retweet() ) { ?>
								<a href="<?php echo wordtwit_get_tweet_log_retweet_link(); ?>"><?php _e( 'Retweet', 'wordtwit-pro' ); ?></a> | 
								<?php } ?>
								<a href="<?php echo wordtwit_get_tweet_log_delete_link(); ?>"><?php _e( 'Delete log item', 'wordtwit-pro' ); ?></a>
							</div>							
						</td>
						<td>
							<?php if ( wordtwit_get_tweet_log_status() == 'future' ) { ?>
								<?php _e( 'Pending', 'wordtwit-pro' ); ?>
							<?php } else if ( wordtwit_get_tweet_log_status() == 'error' ) { ?>
								<span class="fail"><?php _e( 'Failed', 'wordtwit-pro' ); ?></span>
								<?php if ( wordtwit_get_tweet_log_status() == 'error' ) { ?>
									<div class="fail"><?php wordtwit_the_tweet_log_error(); ?></div>
								<?php } ?>							
							<?php } else { ?>
								<?php _e( 'Published', 'wordtwit-pro' ); ?>
							<?php } ?>
						</td>						
						<td><?php wordtwit_the_accounts_for_tweet(); ?></td>
						<td>
							<?php echo date( 'F jS, Y', wordtwit_get_tweet_log_date() ); ?><br />
							<?php echo date( 'g:i a', wordtwit_get_tweet_log_date() ); ?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">
			<?php echo sprintf( __( '%s items', 'wordtwit-pro' ), number_format ( wordtwit_get_tweet_log_total_entries() ) ); ?></span>
			<?php wordtwit_get_tweet_log_paginated_links(); ?>
		</div>
	</div>
</div>