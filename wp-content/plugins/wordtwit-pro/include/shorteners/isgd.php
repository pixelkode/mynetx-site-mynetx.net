<?php

include_once( 'base_shortener.php' );

class WordTwitIsgdShortener extends WordTwitBaseShortener {
	function WordTwitIsgdShortener() {
		parent::WordTwitBaseShortener( 'isgd' );
	}
	
	function shorten( $url ) {
		$request_uri = 'http://is.gd/create.php?format=simple&url=' . urlencode( $url );		
				
		$request = new WP_Http;
		$result = $request->request( $request_uri );
		
		if ( $result ) {
			$decoded_result = $result['body'];
			if ( $result['response']['code'] == 200 && $decoded_result ) {
				return $decoded_result;	
			}
		}
		
		return $url;	
	}
}	
