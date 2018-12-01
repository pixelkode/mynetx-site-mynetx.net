<div id="wordtwit-accounts" class="wrap">
	<h2><?php _e( 'Twitter Accounts', 'wordtwit-pro' ); ?><?php if ( wordtwit_user_can_add_account() ) { ?> <a href="<?php wordtwit_the_twitter_authorize_url(); ?>" class="button add-new-h2"><?php _e( 'Add Account', 'wordtwit-pro' ); ?> &raquo;</a><?php } ?></h2>

	<?php if ( !wordtwit_has_accounts() ) { ?>
		<?php _e( 'You have not added any Twitter accounts yet', 'wordtwit-pro' ); ?>.<br /><br />
		<?php _e( "Click the 'Add Account' button above to add your first Twitter account", 'wordtwit-pro' ); ?>.<br /><br />
	<?php } else { ?>	

		<table class="wp-list-table widefat fixed posts" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col" class="manage-column desc"><?php _e( 'Screen Name', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-type"><?php _e( 'Type', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-followers"><?php _e( 'Followers', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-updates"><?php _e( 'Updates', 'wordtwit-pro' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col" class="manage-column desc"><?php _e( 'Screen Name', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-type"><?php _e( 'Type', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-followers"><?php _e( 'Followers', 'wordtwit-pro' ); ?></th>
					<th scope="col" class="manage-column desc col-updates"><?php _e( 'Updates', 'wordtwit-pro' ); ?></th>
				</tr>
			</tfoot>		
			<tbody>
			<?php $account_count = 0; ?>
			<?php while ( wordtwit_has_accounts() ) { ?>
				<?php wordtwit_the_account(); ?>			
				<tr class="<?php if ( $account_count % 2 == 1 ) echo 'alternate '; ?><?php if ( wordtwit_is_account_global() ) echo 'shared '; else echo 'private'; ?>" valign="top">
					<th scope="row" class="check-column"><input type="checkbox" /></th>
					<td class="avatar">
						<img src="<?php wordtwit_the_account_avatar(); ?>" />
						<a href="http://twitter.com/<?php wordtwit_the_account_screen_name(); ?>" target="_blank"><?php wordtwit_the_account_screen_name(); ?></a><br />
						<small><?php wordtwit_the_account_location(); ?></small>
						<div class="row-actions">
							<?php /* TODO: clean this logic up */ ?>
							<?php /* Global account and administrator, can do everything */ ?>
							<?php if ( wordtwit_current_user_can_modify_account() || wordtwit_current_user_can_delete_account() ) { ?>
							<a href="<?php wordtwit_the_account_refresh_url(); ?>"><?php _e( 'Refresh', 'wordtwit-pro' ); ?></a> | 
							<?php } ?>
							<?php if ( wordtwit_current_user_can_delete_account() ) { ?>
							<a href="<?php wordtwit_the_account_delete_url(); ?>"><?php _e( 'Remove', 'wordtwit-pro' ); ?></a>
							<?php } ?>
							<?php if ( wordtwit_current_user_can_modify_account() ) { ?>
								<?php if ( wordtwit_is_account_global() ) { ?>
								| <a href="<?php wordtwit_the_account_type_change_url( 'local' ); ?>"><?php _e( 'Make Private', 'wordtwit-pro' ); ?></a>
								<?php } else { ?>
								
								| <a href="<?php wordtwit_the_account_type_change_url( 'global' ); ?>"><?php _e( 'Make Shared', 'wordtwit-pro' ); ?></a>
								<?php } ?>
							<?php } ?>
							
						</div>
					</td>
					<td class="col-type">
					<?php if ( wordtwit_is_account_global() ) { ?>
						<?php _e( 'Shared', 'wordtwit-pro' ); ?><br />
						<small><?php _e( 'Others can publish to this account', 'wordtwit-pro' ); ?></small>
					<?php } else { ?>
						<?php _e( 'Private', 'wordtwit-pro' ); ?><br />
						<small><?php echo sprintf( __( 'Owned by %s', 'wordtwit-pro' ), wordtwit_get_account_owner() ); ?></small>
					<?php } ?>
					</td>
					<td class="col-followers"><?php echo number_format( wordtwit_get_account_followers() ); ?></td>
					<td class="col-updates"><?php echo number_format( wordtwit_get_account_status_updates() ) ; ?></td>
				</tr>
				<?php $account_count++; ?>
			<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>