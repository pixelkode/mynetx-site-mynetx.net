<?php

include_once( 'base_shortener.php' );

class WordTwitSchiebdeShortener extends WordTwitBaseShortener {
	function WordTwitSchiebdeShortener() {
		parent::WordTwitBaseShortener( 'schiebde' );
	}
	
	function shorten( $url ) {
		$request_uri = 'http://www.schieb.de/twitter/shortener-dlvr.php?&url=' . urlencode( $url );
				
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
