<?php
/**
Plugin Name: NM Podcasts
Description: A simple plugin to add a custom post type and RSS feed for podcasts
Author: Shawn Beelman
Version: 0.7.0
Author URI: http://www.sbgraphicdesign.com
Plugin URI: https://github.com/Protohominid/nm-podcasts
GitHub Plugin URI: https://github.com/Protohominid/nm-podcasts
based on http://css-tricks.com/roll-simple-wordpress-podcast-plugin/
**/

require_once( 'nm-podcasts-settings-page.php' );
require_once( 'nm-podcasts-select-metabox.php' );
include_once( 'updater.php' );

if ( is_admin() ) {
	$config = array(
		'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' => 'nm-podcasts', // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/Protohominid/nm-podcasts', // the GitHub API url of your GitHub repo
		'raw_url' => 'https://raw.github.com/Protohominid/nm-podcasts/master', // the GitHub raw url of your GitHub repo
		'github_url' => 'https://github.com/Protohominid/nm-podcasts', // the GitHub url of your GitHub repo
		'zip_url' => 'https://github.com/Protohominid/nm-podcasts/zipball/master', // the zip url of the GitHub repo
		'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '3.0', // which version of WordPress does your plugin require?
		'tested' => '3.3', // which version of WordPress is your plugin tested up to?
		'readme' => 'README.md', // which file to use as the readme for the version number
		'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
	);
	new WP_GitHub_Updater( $config );
}

// Add the custom post type
//--------------------------------------------------------------------------------------------
add_action( 'init', 'nm_podcasts_cpt' );
function nm_podcasts_cpt() {

	$labels = array(
		'name'					=> __( 'NM Podcasts', 'nmpodcasts' ),
		'singular_name'			=> __( 'NM Podcast', 'nmpodcasts' ),
		'menu_name'				=> __( 'NM Podcasts', 'nmpodcasts' ),
		'parent_item_colon'		=> __( 'Parent Podcast:', 'nmpodcasts' ),
		'all_items'				=> __( 'All NM Podcasts', 'nmpodcasts' ),
		'view_item'				=> __( 'View NM Podcast', 'nmpodcasts' ),
		'add_new_item'			=> __( 'Add New NM Podcast', 'nmpodcasts' ),
		'add_new'				=> __( 'Add New', 'nmpodcasts' ),
		'edit_item'				=> __( 'Edit NM Podcast', 'nmpodcasts' ),
		'update_item'			=> __( 'Update NM Podcast', 'nmpodcasts' ),
		'search_items'			=> __( 'Search NM Podcasts', 'nmpodcasts' ),
		'not_found'				=> __( 'Not found', 'nmpodcasts' ),
		'not_found_in_trash'	=> __( 'Not found in Trash', 'nmpodcasts' ),
	);
	$args = array(
		'label'					=> __( 'nm_podcasts', 'nmpodcasts' ),
		'description'			=> __( 'NM Podcast Description', 'nmpodcasts' ),
		'labels'				=> $labels,
		'supports'				=> array( 'title', 'excerpt' ),
		#'taxonomies'			=> array( 'category' ),
		'hierarchical'			=> false,
		'public'				=> true,
		'show_ui'				=> true,
		#'show_in_menu'			=> true,
		#'show_in_nav_menus'	=> true,
		'show_in_admin_bar'		=> true,
		#'menu_position'		=> 5,
		'menu_icon'				=> 'dashicons-format-audio',
		'can_export'			=> true,
		'has_archive'			=> true,
		#'exclude_from_search'	=> false,
		#'publicly_queryable'	=> true,
		#'capability_type'		=> 'page'
	);
	register_post_type( 'nm_podcasts', $args );
}




// Add Podcast Categories
//--------------------------------------------------------------------------------------------
/*
add_action( 'init', 'nm_podcast_taxonomies', 0 );
function nm_podcast_taxonomies(){
	global 'nmpodcasts';
	$labels = array(
		'name'				=> _x( 'NM Podcast Categories', 'taxonomy general name', 'nmpodcasts' ),
		'singular_name'		=> _x( 'NM Podcast Category', 'taxonomy singular name', 'nmpodcasts' ),
		'search_items'		=> __( 'Search Podcast Categories', 'nmpodcasts' ),
		'all_items'			=> __( 'All Podcast Categories', 'nmpodcasts' ),
		'parent_item'		=> __( 'Parent Podcast Category', 'nmpodcasts' ),
		'parent_item_colon'	=> __( 'Parent Podcast Category:', 'nmpodcasts' ),
		'edit_item'			=> __( 'Edit Podcast Category', 'nmpodcasts' ), 
		'update_item'		=> __( 'Update Podcast Category', 'nmpodcasts' ),
		'add_new_item'		=> __( 'Add New Podcast Category', 'nmpodcasts' ),
		'new_item_name'		=> __( 'New Podcast Category', 'nmpodcasts' ),
		'menu_name'			=> __( 'Podcast Categories', 'nmpodcasts' )
	);
	$args = array(
		'hierarchical' => true,
		'labels' => $labels
	);
	register_taxonomy( 'nm-podcasts', 'nm_podcasts', $args );
}
*/




// Initialize the metabox classes
//--------------------------------------------------------------------------------------------
add_action( 'init', 'nm_podcast_init_cmb_meta_boxes', 9999 );
function nm_podcast_init_cmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( 'cmb/init.php' );
	}
}
// Meta Boxes
add_filter( 'cmb_meta_boxes', 'nm_podcast_metaboxes' );
function nm_podcast_metaboxes( $meta_boxes ) {
	$prefix = 'nm_podcast_'; // Prefix for all fields
	$meta_boxes[] = array(
		'id' => 'nm_podcast_metabox',
		'title' => __( 'Podcast Details', 'nmpodcasts' ),
		'pages' => array( 'nm_podcasts' ), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => __( 'File URL', 'nmpodcasts' ),
				'desc' => __( 'Direct link to file. Should be mp3 format and http protocol (not https).', 'nmpodcasts' ),
				'id' => $prefix . 'file_url',
				'type' => 'text_url'
			),
			array(
				'name' => __( 'Duration', 'nmpodcasts' ),
				'desc' => __( 'HH:MM:SS', 'nmpodcasts' ),
				'id' => $prefix . 'file_length',
				'type' => 'text'
			),
			array(
				'name' => __( 'File Size', 'nmpodcasts' ),
				'desc' => __( 'In bytes', 'nmpodcasts' ),
				'id' => $prefix . 'file_size',
				'type' => 'text'
			)
		)
	);
	return $meta_boxes;
}

add_action( 'add_meta_boxes', 'yoast_is_toast', 99 );
function yoast_is_toast(){
	remove_meta_box( 'wpseo_meta', 'nm_podcasts', 'normal' );
}




// Add the custom feed
//--------------------------------------------------------------------------------------------
add_action( 'init', 'nm_podcast_rss' );
function nm_podcast_rss(){
	add_feed( 'podcast', 'nm_podcast_rss_template' );
}

function nm_podcast_rss_template(){
	require_once( dirname( __FILE__ ) . '/nm-podcasts-rss-template.php' );
}




// Add the podcast feature box to post
//--------------------------------------------------------------------------------------------
add_filter( 'the_content', 'nm_podcasts_filter_content' );
function nm_podcasts_filter_content( $content ) {
	global $post;
	global $options;
	$podcasts_options = get_option( 'nm_podcasts_options' );
	$nm_podcast = get_post_meta( $post->ID, '_selected_nm_podcasts', true );

	if( is_single() && !empty( $nm_podcast ) ) {
		$pod_url = get_post_meta( $nm_podcast, 'nm_podcast_file_url', true );
		$output = '<div class="podcast-wrap">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="podcast-icon" x="0px" y="0px" width="48px" height="48px" viewBox="0 0 48 48" enable-background="new 0 0 16 16" xml:space="preserve" fill="#ffffff"> <path d="M 36 44.763L36.00 42 c0.00-1.431-0.294-2.781-0.75-4.053 C 39.3 34.6  42 29.7  42 24.00c0.00-9.939-8.061-18.00-18.00-18.00S 6 14.1  6 24.00c0.00 5.7  2.7 10.7  6.8 13.947C 12.3 39.2  12 40.6  12 42.00l0.00 2.8 C 4.8 40.6 0 32.9 0 24.00c0.00-13.254  10.746-24.00  24.00-24.00s 24 10.7  24 24.00C 48 32.9  43.2 40.6  36 44.763z M 24 9.00c 8.3 0  15 6.7  15 15 c0.00 4.482-2.004 8.457-5.118 11.205c-1.164-1.686-2.763-3.027-4.614-3.936C 31.5 29.6  33 27  33 24.00c0.00-4.971-4.029-9.00-9.00-9.00 S 15 19  15 24.00c0.00 3  1.5 5.6  3.7 7.269c-1.851 0.912-3.45 2.25-4.614 3.936C 11 32.5  9 28.5  9 24.00C 9 15.7  15.7 9  24 9.00z M 18 24.00c0.00-3.312  2.688-6.00  6.00-6.00s 6 2.7  6 6.00s-2.688 6.00-6.00 6.00S 18 27.3  18 24.00z M 24 33.00c 5 0  9 4  9 9.00l0.00 6 L15.00 48 l0.00 -6 C 15 37  19 33  24 33.00z"/></svg>
		<h3>' . __( 'Download the free podcast:' ) . '</h3>
		<ul class="podcast" style="text-align: left;">
			<li class="mp3"><a href="' . esc_url( $pod_url ) . '">mp3 Download</a></li>
			<li class="itunes"><a href="' . esc_url( $podcasts_options['itunes_subscribe_url'] ) . '">Subscribe via iTunes</a></li>';

		if( !empty( $podcasts_options['stitcher_subscribe_url'] ) ) {
			$output .= '<li class="stitcher"><a href="' . esc_url( $podcasts_options['stitcher_subscribe_url'] ) . '">Subscribe via Stitcher</a></li>';
		}
		
		$output .= '</ul>
		</div>';

		// Prepend markup to wp content
		$content = $output . $content;	
	}	
	return $content;
}



// Add 'settings' link to plugin page
//--------------------------------------------------------------------------------------------
add_filter( 'plugin_action_links', 'nm_podcasts_plugin_action_links', 10, 2 );
function nm_podcasts_plugin_action_links( $links, $file ) {
    static $this_plugin;

    if ( !$this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }

    if ( $file == $this_plugin ) {
        $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=nm-podcasts-admin">Settings</a>';
        array_unshift( $links, $settings_link );
    }

    return $links;
}
