<?php

include_once( 'base_shortener.php' );

class WordTwitTinyUrlShortener extends WordTwitBaseShortener {
	function WordTwitTinyUrlShortener() {
		parent::WordTwitBaseShortener( 'tinyurl' );
	}
	
	function shorten( $url ) {
		$request_uri = 'http://tinyurl.com/api-create.php?url=' . urlencode( $url );
				
		$request = new WP_Http;
		$result = $request->request( $request_uri );
		
		if ( $result ) {
			if ( isset( $result['response'] ) && isset( $result['response']['code'] ) && $result['response']['code'] == 200 ) {
				$url = $result['body'];
			}
		}
		
		return $url;	
	}
}	
