
function doWordTwitReady() {
	wtSetupTabSwitching();
	wtCookieSetup();	
	wtSetupGlobals();
	wtLoadNews();
	wtSetupLicenseArea();
	wtLicenseFeedback();
	wtSetupSelects();
	wtDoDashboardAjax();
	wtSavedOrReset();
}

function wtSetupTabSwitching() {
	var adminTabSwitchLinks = jQuery( 'a.wordtwit-admin-switch' );
	if ( adminTabSwitchLinks.length ) {
		adminTabSwitchLinks.live( 'click', function( e ) {
			var targetTabId = '';
			var targetTabSection = '';
			var targetArea = jQuery( this ).attr( 'rel' );

			 if ( targetArea == 'licenses' ) {
				targetTabId = 'pane-2';
				targetTabSection = 'tab-section-manage-licenses-section';	

			} else if ( targetArea == 'account' ) {
				targetTabId = 'pane-2';
				targetTabSection = 'tab-section-bncid';	
			}
			
			jQuery( 'a#' + targetTabId + ',' + 'a#' + targetTabSection ).click();				
			e.preventDefault();
		});
	}
}

function wtCookieSetup() {
	// Top menu tabs
	jQuery( '#wordtwit-top-menu li a' ).unbind( 'click' ).click( function() {
		var tabId = jQuery( this ).attr( 'id' );
		
		jQuery.cookie( 'wordtwit-tab', tabId );
		
		jQuery( '.pane-content' ).hide();
		jQuery( '#pane-content-' + tabId ).show();
		
		jQuery( '#pane-content-' + tabId + ' .left-area li a:first' ).click();
		
		jQuery( '#wordtwit-top-menu li a' ).removeClass( 'active' );
		jQuery( '#wordtwit-top-menu li a' ).removeClass( 'round-top-6' );
		
		jQuery( this ).addClass( 'active' );
		jQuery( this ).addClass( 'round-top-6' );

		return false;
	});

	// Left menu tabs
	jQuery( '#wordtwit-admin-form .left-area li a' ).unbind( 'click' ).click( function() {
		var relAttr = jQuery( this ).attr( 'rel' );
		
		jQuery.cookie( 'wordtwit-list', relAttr );
			
		jQuery( '.setting-right-section' ).hide();
		jQuery( '#setting-' + relAttr ).show();
		
		jQuery( '#wordtwit-admin-form .left-area li a' ).removeClass( 'active' );
		
		jQuery( this ).addClass( 'active' );
		
		return false;
	});
	
	// Cookie saving for tabs
	var tabCookie = jQuery.cookie( 'wordtwit-tab' );
	if ( tabCookie ) {
		var tabLink = jQuery( "#wordtwit-top-menu li a[id='" + tabCookie + "']" ); 
		jQuery( '.pane-content' ).hide();
		jQuery( '#pane-content-' + tabCookie ).show();	
		tabLink.addClass( 'active' );
		tabLink.addClass( 'round-top-6' );
		
		var listCookie = jQuery.cookie( 'wordtwit-list' );
		if ( listCookie ) {
			var menuLink = jQuery( "#wordtwit-admin-form .left-area li a[rel='" + listCookie + "']");
			jQuery( '.setting-right-section' ).hide();
			jQuery( '#setting-' + listCookie ).show();	
			jQuery( '#wordtwit-admin-form .left-area li a' ).removeClass( 'active' );	
			menuLink.click();			
		} else {
			jQuery( '#wordtwit-admin-form .left-area li a:first' ).click();
		}
	} else {
		jQuery( '#wordtwit-top-menu li a:first' ).click();
	}	
}

var wtNotifiedCustomKeyWarning = 0;

function wtSetupGlobals() {	
	jQuery.ajaxSetup ({
	    cache: false
	});		

	jQuery( '#twitboard .box-holder' ).equalHeights( 280, 450 );
	jQuery( '#unlicensed-board' ).shake( 4, 5, 1200 );
	jQuery( 'a.wordtwit-tooltip' ).tooltip( { effect: 'fade', position: 'top right', offset: [-12, -6], tip: '#wordtwit-tooltip' });
	
	jQuery( '#allow_users_to_add_accounts' ).live( 'change', function() {
		if ( jQuery( this ).attr( 'checked' ) ) {
			jQuery( '#setting_minimum_user_capability_for_account_add' ).slideDown();
		} else {
			jQuery( '#setting_minimum_user_capability_for_account_add' ).hide();
		}
	}).change();
	
	jQuery( '#url_shortener' ).live( 'change', function() {
		var currentValue = jQuery( this ).val();
		
		jQuery( '#setting_yourls_path, #setting_yourls_signature, #setting_bitly_api_key, #setting_bitly_username' ).hide();

		if ( currentValue == 'yourls' ) {
			jQuery( '#setting_yourls_path' ).slideDown();
			jQuery( '#setting_yourls_signature' ).slideDown();
		} else if ( currentValue == 'bitly' ) {
			jQuery( '#setting_bitly_api_key' ).slideDown();
			jQuery( '#setting_bitly_username' ).slideDown();	
		}
	}).change();
	
	jQuery( 'a#estimate-offset' ).live( 'click', function( e ) {
		wtAdminAjax( 'estimate-offset', {}, function( result ) {	
			jQuery( '#oauth_time_offset' ).val( result );
		});
		
		e.preventDefault();
	});	
	
	jQuery( '#setting_custom_consumer_key, #custom_consumer_key' ).live( 'change', function() {
		if ( !wtNotifiedCustomKeyWarning ) {
			alert( WordTwitProCustom.custom_key_warning );
			wtNotifiedCustomKeyWarning = 1;
		}
	});
}

function wtAdminAjax( actionName, actionParams, callback ) {	
	var ajaxData = {
		action: "wordtwit_ajax",
		wordtwit_action: actionName,
		wordtwit_nonce: WordTwitProCustom.admin_nonce
	};
	
	for ( name in actionParams ) { ajaxData[name] = actionParams[name]; }

	jQuery.post( ajaxurl, ajaxData, function( result ) {
		callback( result );	
	});	
}

function wtSetupSelects() {
	jQuery( '#tweet_template' ).change( function(){
		var currentVal = jQuery( this ).val();
		if ( currentVal == 'custom' ) {
			jQuery( '#setting_custom_tweet_template' ).slideDown();
		} else {
			jQuery( '#setting_custom_tweet_template' ).hide();	
		}
	}).change();	
	
	jQuery( '#enable_utm' ).change( function(){
		if ( jQuery( this ).attr( 'checked' ) ) {
			jQuery( '#setting_utm_source, #setting_utm_medium, #setting_utm_campaign' ).slideDown();
		} else {
			jQuery( '#setting_utm_source, #setting_utm_medium, #setting_utm_campaign' ).hide();
		}
	}).change();
}

function wtLoadNews() {
	var twitBoardNews = jQuery( '#blog-news-box-ajax' );
	if ( twitBoardNews.length ) {
		wtAdminAjax( 'wordtwit-news', {}, function( response ) {
			twitBoardNews.html( response );
			jQuery( '#blog-news-box' ).removeClass( 'loading' );
			jQuery( '#twitboard .box-holder' ).equalHeights( 280, 450 );
		});
	}	
}

function wtSetupLicenseArea() {
	wtAdminAjax( 'manage-licenses', {}, function( result ) { 
		jQuery( '#setting_manage-license' ).html( result );
	});
	
	jQuery( 'a.wordtwit-add-license' ).live( 'click', function( e ) {
		wtAdminAjax( 'activate-site-license', {}, function( result ) { 
			window.location = location.href;
		});		
		
		e.preventDefault();
	});
	
	jQuery( 'a.wordtwit-remove-license' ).live( 'click', function( e ) {
		var ajax_params = {
			site: jQuery( this ).attr( 'rel' )
		};
		
		wtAdminAjax( 'deactivate-site-license', ajax_params, function( result ) { 
			window.location = location.href;
		});		
		
		e.preventDefault();		
	});	
}

function wtLicenseFeedback() {
	if ( jQuery( '#setting-bncid p.license-valid' ).length ) {
		jQuery( 'input#bncid.text, input#license_key.text' ).addClass( 'valid' );
	}
	
	if ( jQuery( '#setting-bncid p.license-partial' ).length ) {
		jQuery( 'input#bncid.text, input#license_key.text' ).addClass( 'partial' );
	}
	
	/* Failed credentials */
	if ( jQuery( 'p.bncid-failed' ).length ) {
		jQuery( 'p.bncid-failed' ).shake( 4, 8, 900 );
	}
}

function wtSavedOrReset() {
	if ( jQuery( '#bnc .saved' ).length ) {
		setTimeout( function() {
			jQuery( '#bnc .saved' ).fadeOut( 200 );
		}, 1500 );
	}

	if ( jQuery( '#bnc .reset' ).length ) {
		setTimeout( function() {
			jQuery( '#bnc .reset' ).fadeOut( 200 );
		}, 1500 );
	}

	/* Reset confirmation */
	jQuery( '#bnc-submit-reset input' ).click( function() {
		var answer = confirm( WordTwitProCustom.reset_admin_settings );
		if ( answer ) {
			jQuery.cookie( 'wordtwit-tab', '' );
			jQuery.cookie( 'wordtwit-list', '' );
		} else {
			return false;	
		}
	});
}

function wtDoDashboardAjax() {
	wtAdminAjax( 'dashboard-ajax', {}, function( response ) {
		jQuery( '#touchboard-ajax' ).html( response );
	});	
}

jQuery( document ).ready( function() { doWordTwitReady(); } );