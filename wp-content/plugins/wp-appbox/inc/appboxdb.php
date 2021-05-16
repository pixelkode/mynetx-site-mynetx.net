<?php 

include_once( 'getappinfo.class.php' );

/**
* Prüft ob Apps im Cache vorhanden sind
*
* @since   3.2.3
*/

function wpAppbox_cacheHasApps() {
	global $wpdb;
	$wpdb->get_results( "SELECT * FROM " . wpAppbox_databaseName() );
	if ( '0' != $wpdb->num_rows ) {
		return( true );
	}
}


/**
* Anzahl der Apps im Cache zurückgeben
*
* @since   3.2.3
*
* @param   boolean  $onlyDeprecated   Nur veraltete Apps zählen? (optional)
* @return  integer  $countApps        Anzahl der Apps
*/

function wpAppbox_countCachedApps( $onlyDeprecated = false ) {
	global $wpdb;
	if ( $onlyDeprecated ) {
		$countApps = $wpdb->get_var( "SELECT COUNT(*) FROM " . wpAppbox_databaseName() . " WHERE deprecated = 1" );
	} else {
		$countApps = $wpdb->get_var( "SELECT COUNT(*) FROM " . wpAppbox_databaseName() );
	}
	return( $countApps );
}


/**
* Name der Datenbank herausfinden
*
* @since   3.2.2
*
* @return  string  $dbName  Name der verwendeten Datenbank
*/

function wpAppbox_databaseName() {
	global $wpdb;
	$dbName = $wpdb->prefix . 'appbox';
	return( $dbName );
}


/**
* Prüfen ob Tabelle "wp_appbox" existiert
*
* @since   3.2.0
*
* @return  boolean  true/false  TRUE when exists
*/

function wpAppbox_tableExists() {
	global $wpdb;
	if( $wpdb->get_var( "SHOW TABLES LIKE '" . wpAppbox_databaseName() . "'" ) != wpAppbox_databaseName() ) {
		return( false );
	} else {
		return( true );
	}
}


/**
* Erstelle Tabelle in der Datenbank
*
* @since   3.2.0
* @change  4.0.0
*/

function wpAppbox_createTable() {
	global $wpdb;
	if ( get_option('wpAppbox_dbVersion') != WPAPPBOX_DB_VERSION || !wpAppbox_tableExists() ) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE " . wpAppbox_databaseName() . " (
				id VARCHAR(32) PRIMARY KEY NOT NULL,
				app_id VARCHAR(100) NOT NULL,
				app_url VARCHAR(255) NOT NULL,
				app_icon VARCHAR(350) NOT NULL,
				app_title VARCHAR(255) NOT NULL,
				app_author VARCHAR(100) NOT NULL,
				app_author_url VARCHAR(255) NULL,
				app_price VARCHAR(12) NOT NULL,
				app_has_iap INT(1) DEFAULT '0',
				app_rating NUMERIC(2,1) DEFAULT '-1',
				app_screenshots TEXT NOT NULL,
				app_extend VARCHAR(255),
				store_name VARCHAR(30) NOT NULL,
				store_name_css VARCHAR(20) NOT NULL,
				appbox_version VARCHAR(8) NOT NULL,
				created BIGINT(20) NOT NULL,
				fallback INT(1) DEFAULT '0',
				deprecated INT(1) DEFAULT '0',
				UNIQUE KEY id (id)
				) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); //See: https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
		dbDelta( $sql );
		if ( $wpdb->last_error ) {
			wpAppbox_errorOutput( "function: wpAppbox_createTable() ---> $wpdb->last_error" );
		} else {
			update_option( 'wpAppbox_dbVersion', WPAPPBOX_DB_VERSION );
		}
	}
}


/**
* Löscht die Tabelle "wp_appbox" bei Deinstallation
*
* @since   3.2.0
*
* @return  boolean   true/false     TRUE when deleted
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_deleteTable() {
	global $wpdb;
	if ( wpAppbox_tableExists() ) {
		$sql = "DROP TABLE " . wpAppbox_databaseName();
		$wpdb->query( $sql );
		if( $wpdb->last_error ) {
			wpAppbox_errorOutput( "function: wpAppbox_deleteTable() ---> $wpdb->last_error" );
		} else { 
			return( true );
		}
	}
}


/**
* Räumt den App-Cache auf
*
* @since   3.2.0
* @change  4.0.0
*
* @return  boolean  true/false  TRUE when cleaned
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_cleanCache() {
	global $wpdb;
	if ( !get_option('wpAppbox_disableAutoCache') ) {
		$timeNow = time(); 
		$timeExpires = WPAPPBOX_CACHINGTIME * 60;
		$sql = "DELETE FROM " . wpAppbox_databaseName() . " WHERE (created < (UNIX_TIMESTAMP() + $timeExpires) AND (deprecated = 0))";
		$wpdb->query( $sql );
		if ( $wpdb->last_error ) {
			wpAppbox_errorOutput( "function: wpAppbox_cleanCache() ---> $wpdb->last_error" );
		} else { 
			return( true );
		}
	}
}


/**
* Löscht eine bestimmte App aus dem App-Cache
*
* @since   3.2.0
* @change  3.4.0
*
* @param   string    $appID      Cache-ID der App
* @return  boolean   true/false  TRUE when deleted
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_clearAppCache( $cacheID ) {
	global $wpdb;
	$results = $wpdb->delete( wpAppbox_databaseName(), array( 'id' => $cacheID ) );
	if( $wpdb->last_error ) {
		wpAppbox_errorOutput( "function: wpAppbox_clearAppCache() ---> $wpdb->last_error" );
	} else {
		return( true );
	}	
}


/**
* Löscht den kompletten App-Cache
*
* @since   3.2.0
* @change  4.0.30
*
* @return  boolean  true/false  TRUE when cleared
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_clearCache() {
	global $wpdb;
	$sql = "DELETE FROM " . wpAppbox_databaseName() . " WHERE deprecated = 0";
	$wpdb->query( $sql );
	if ( $wpdb->last_error ) {
		wpAppbox_errorOutput( "function: wpAppbox_clearCache() ---> $wpdb->last_error" );
	} else {
		$sql = "DELETE FROM wp_options WHERE option_name LIKE ('%wpAppbox_blockQuery_%')";
		$wpdb->query( $sql );
		if ( $wpdb->last_error )
			wpAppbox_errorOutput( "function: wpAppbox_clearCache() ---> $wpdb->last_error" );
		else
			return( true );
	}
}


/**
* Setzt als nicht mehr verfügbar markierte Apps zurück
*
* @since   4.0.31
*
* @return  boolean  true/false  TRUE when cleared
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_resetDeprecatedApps() {
	global $wpdb;
	$sql = "UPDATE " . wpAppbox_databaseName() . " SET deprecated = 0 WHERE deprecated = 1";
	$wpdb->query( $sql );
	if ( $wpdb->last_error )
		wpAppbox_errorOutput( "function: wpAppbox_resetDeprecatedApps() ---> $wpdb->last_error" );
	else
		return( true );
}


/**
* Gesperrte Abfragen zurücksetzen
*
* @since   4.0.31
*
* @return  boolean  true/false  TRUE when cleared
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_resetBlockedQueries() {
	global $wpdb;
	$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE ('%wpAppbox_blockQuery_%')";
	$wpdb->query( $sql );
	if ( $wpdb->last_error )
		wpAppbox_errorOutput( "function: wpAppbox_resetBlockedQueries() ---> $wpdb->last_error" );
	else
		return( true );
}


/**
* Die n abgelaufenen App-Daten zurückgeben
*
* @since   4.0.0
* @change  4.1.26
*
* @output  function  errorOutput()  Fehlermeldung
*/

function wpAppbox_cacheCron() {
	if ( !defined( 'DOING_CRON' ) ) return;
	global $wpdb, $wpAppbox_optionsDefault;
	$currentStore = '';
	$cronCount = get_option( 'wpAppbox_cronCount' );
	if ( '0' == $cronCount ) return;
	if ( !is_int( intval( $cronCount ) ) ) {
		$cronCount = $wpAppbox_optionsDefault['cronCount'];
	}
	$timeNow = time();
	$timeExpires = WPAPPBOX_CACHINGTIME * 60;
	$appList = $wpdb->get_results( "SELECT id, app_id, store_name_css FROM " . wpAppbox_databaseName() . " WHERE (created + $timeExpires) < $timeNow ORDER BY created ASC LIMIT $cronCount");
	if ( $wpdb->last_error ) {
		wpAppbox_errorOutput( "function: wpAppbox_cacheCron() ---> $wpdb->last_error" );
		return;
	}
	$appArray = array();
	foreach ( $appList as $appData ):
		$dataArray = array(
			'id' => $appData->id,
			'app_id' => $appData->app_id,
			'store_name_css' => $appData->store_name_css
		);
		array_push( $appArray, $dataArray );
	endforeach;
	$currentStore = '';
	foreach ( $appArray as $appData ):
		if ( $currentStore == $storeNameCSS ) sleep( 5 );
		$appID = $appData['app_id'];
		$storeNameCSS = ('macappstore' == $appData['store_name_css']) ? 'appstore' : $appData['store_name_css'];
		$appCache = new wpAppbox_GetAppInfoAPI;
		$appCache = $appCache->getTheAppData( $storeNameCSS, $appID, true );
		$currentStore = $storeNameCSS;
	endforeach;
	wpAppbox_errorOutput( "function: wpAppbox_cacheCron() ---> Cron was fired" );
}


/**
* Ändern den Tag für einen bestehenden Store
*
* @since   4.2.0
*
* @return  boolean  true/false  TRUE when OK
*/

function wpAppbox_changeStoreName( $oldTag, $newTag, $newName ) {
	global $wpdb;
	if ( !isset( $oldTag ) || !isset( $newTag ) || !isset( $newName ) ) return( false );
	/* ================================================ */
	/* WP-Appbox Optionen umbenennen                    */
	/* ================================================ */
	if ( get_option( 'wpAppbox_buttonAppbox_' . $oldTag ) ) 
		update_option( 'wpAppbox_buttonAppbox_' . $newTag, get_option( 'wpAppbox_buttonAppbox_' . $oldTag ), 'no' );
	if ( get_option( 'wpAppbox_buttonWYSIWYG_' . $oldTag ) ) 
		update_option( 'wpAppbox_buttonWYSIWYG_' . $newTag, get_option( 'wpAppbox_buttonWYSIWYG_' . $oldTag ), 'no' );
	if ( get_option( 'wpAppbox_buttonHTML_' . $oldTag ) ) 
		update_option( 'wpAppbox_buttonHTML_' . $newTag, get_option( 'wpAppbox_buttonHTML_' . $oldTag ), 'no' );
	if ( get_option( 'wpAppbox_buttonHidden_' . $oldTag ) ) 
		update_option( 'wpAppbox_buttonHidden_e' . $newTag, get_option( 'wpAppbox_buttonHidden_' . $oldTag ), 'no' );
	if ( get_option( 'wpAppbox_storeURL_URL_' . $oldTag ) ) 
		update_option( 'wpAppbox_storeURL_URL_' . $newTag, get_option( 'wpAppbox_storeURL_URL_' . $oldTag ), 'no' );
	if ( get_option( 'wpAppbox_storeURL_' . $oldTag ) ) 
		update_option( 'wpAppbox_storeURL_' . $newTag, get_option( 'wpAppbox_storeURL_' . $oldTag ), 'no' );
	/* ================================================ */
	/* Alte Optionen aus Datenbank löschen              */
	/* ================================================ */
    delete_option( 'wpAppbox_buttonAppbox_' . $oldTag );
    delete_option( 'wpAppbox_buttonWYSIWYG_' . $oldTag );
    delete_option( 'wpAppbox_buttonHTML_' . $oldTag );
    delete_option( 'wpAppbox_buttonHidden_' . $oldTag );
    delete_option( 'wpAppbox_storeURL_URL_' . $oldTag );
    delete_option( 'wpAppbox_storeURL_' . $oldTag );
	/* ================================================ */
	/* Wenn Image-Cache aktiv: Bestehende IDs ermitteln */
	/* ================================================ */
	if ( wpAppbox_imageCache::quickcheckImageCache() ):
		$oldApps = $wpdb->get_results( "SELECT id, app_id FROM " . wpAppbox_databaseName() . " WHERE store_name_css = '$oldTag'" ); 
		foreach( $oldApps as $oldApp ):
			$oldCacheID = $oldApp->id;
			$newCacheID = md5( $oldApp->app_id . '-' . $newTag );
			$folderOld = WPAPPBOX_CACHE_PATH . $oldCacheID;
			$folderNew = WPAPPBOX_CACHE_PATH . $newCacheID;
			if ( file_exists( $folderNew ) )
				$result = wpAppbox_imageCache::deleteAppImages( $oldCacheID );
			if ( file_exists( $folderOld ) )
				$result = @rename( $folderOld, $folderNew );
			if ( file_exists( $folderOld . '-deprecated' ) )
				$result = @rename( $folderOld . '-deprecated', $folderNew . '-deprecated' );
		endforeach;
	endif;  
	/* ================================================ */
	/* Alle Einträge in der Datenbank umlisten          */
	/* ================================================ */
	$wpdb->query( "UPDATE " . wpAppbox_databaseName() . " SET store_name = '$newName', store_name_css = '$newTag', id = MD5( CONCAT( app_id, '-$newTag' ) ) WHERE store_name_css = '$oldTag'" );
	/* ================================================ */
	/* Nochmals alle Ordner bereinigen                  */
	/* ================================================ */
	if ( wpAppbox_imageCache::quickcheckImageCache() )
		wpAppbox_imageCache::cleanUpCacheFolder();
}

?>