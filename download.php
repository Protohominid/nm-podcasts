<?php
/**
 * Downloadalytics
 *
 * Copyright (c) 2014 Van Patten Media Inc.
 *
 * Released under the terms of the MIT License.
 * Find the license here: http://opensource.org/licenses/MIT
 * or consult the included README file.
 */

/**
 * Step 1:
 * Downloadalytics requires the Server Side Google Analytics script,
 * available here: https://github.com/dancameron/server-side-google-analytics
 *
 * Make sure the path below is correct.
 */
require_once( dirname(__FILE__).'/ss-ga.class.php' );

// added by me:
$file_hosted_at = 'archive.org';
$site_domain = $_SERVER['SERVER_NAME'];
global $options;
$podcasts_options = get_option( 'nm_podcasts_options' );

/**
 * Step 2:
 * Supply your Google Analytics property ID and domain:
 */
$ssga = new ssga( $podcasts_options['google_analytics_tracking'], $site_domain );

if( isset( $_GET['url'] ) ) {
	$domain = $_GET['url'];
	$domain = urldecode( $domain );
	$domain = filter_var( $domain, FILTER_SANITIZE_URL );

	/**
	 * Step 3:
	 * Make sure to replace the domain names below with your own domain name,
	 * and the file type ('mp3') with the type you want to track.
	 */
	if( strstr( $domain, $file_hosted_at ) && strstr( $domain, 'mp3' ) ) {
		$domain   = preg_replace( '/https?:\/\/(www\.)?'.$site_domain.'/', '', $domain );
		$filename = basename( $domain );
		
		/**
		 * Step 4:
		 * Replace the below line with your Event parameters.
		 */
		$ssga->set_event( 'Downloads', 'Download Type', $filename );

		$ssga->send();
		$ssga->reset();

		header( 'Location: ' . $domain );
	}
	else {
		trigger_error( 'Sorry, this file is invalid.' );
	}
}
else {
	trigger_error( 'Sorry, you didn\'t specify a URL to download.' );
}
