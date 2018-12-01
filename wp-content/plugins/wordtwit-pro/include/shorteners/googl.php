<?php

include_once( 'base_shortener.php' );

class WordTwitGooglShortener extends WordTwitBaseShortener {
	function WordTwitGooglShortener() {
		parent::WordTwitBaseShortener( 'googl' );
	}
	
	function shorten( $url ) {
		$request_uri = 'https://www.googleapis.com/urlshortener/v1/url';
		
		$body = json_encode( array( 'longUrl' => $url ) );
				
		$request = new WP_Http;
		$result = $request->request( $request_uri, array( 'method' => 'POST', 'body' => $body, 'sslverify' => false ) );
		
		if ( $result ) {
			print_r( $result );
			if ( isset( $result['response'] ) && isset( $result['response']['code'] ) && $result['response']['code'] == 200 ) {
				$url = $result['body'];
			}
		}
		
		return $url;	
	}
}	
