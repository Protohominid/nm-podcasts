<?php
/**
Plugin Name: NM Podcasts
Description: A simple plugin to add a custom post type and RSS feed for podcasts
Author: Shawn Beelman
Version: 0.6.5
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
		<h3>' . __( 'Download the free podcast:' ) . '</h3>
		<ul class="podcast" style="text-align: left;">
			<li class="mp3"><a href="' . esc_url( $pod_url ) . '">mp3 Download</a></li>
			<li class="itunes"><a href="' . esc_url( $podcasts_options['itunes_subscribe_url'] ) . '">Subscribe via iTunes</a></li>
		</ul>
		</div>';

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
