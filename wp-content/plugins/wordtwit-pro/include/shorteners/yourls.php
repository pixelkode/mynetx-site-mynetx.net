<?php

include_once( 'base_shortener.php' );

class WordTwitYourlsShortener extends WordTwitBaseShortener {
	var $path;
	var $signature;
	
	function WordTwitYourlsShortener( $path, $signature ) {
		parent::WordTwitBaseShortener( 'yourls' );
		
		$this->path = $path;
		$this->signature = $signature;
	}
	
	function shorten( $url ) {
		$request_uri = $this->path . '?signature=' . $this->signature . '&action=shorturl&format=simple&url=' . urlencode( $url );			
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
