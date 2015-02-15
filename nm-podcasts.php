<?php
/**
Plugin Name: NM Podcasts
Description: A simple plugin to add a custom post type and RSS feed for podcasts
Author: Shawn Beelman
Version: 0.5
Author URI: http://www.sbgraphicdesign.com
based on http://css-tricks.com/roll-simple-wordpress-podcast-plugin/
**/

$textdomain = 'nmpodcasts';

require_once( 'nm-podcasts-settings-page.php' );
require_once( 'nm-podcasts-select-metabox.php' );


// Add the custom post type
//--------------------------------------------------------------------------------------------
add_action( 'init', 'nm_podcasts_cpt', 0 );
function nm_podcasts_cpt() {

	global $textdomain;
  $labels = array(
    'name'                => _x( 'NM Podcasts', 'Podcast General Name', $textdomain ),
    'singular_name'       => _x( 'NM Podcast', 'Podcast Singular Name', $textdomain ),
    'menu_name'           => __( 'NM Podcasts', $textdomain ),
    'parent_item_colon'   => __( 'Parent Podcast:', $textdomain ),
    'all_items'           => __( 'All NM Podcasts', $textdomain ),
    'view_item'           => __( 'View NM Podcast', $textdomain ),
    'add_new_item'        => __( 'Add New NM Podcast', $textdomain ),
    'add_new'             => __( 'Add New', $textdomain ),
    'edit_item'           => __( 'Edit NM Podcast', $textdomain ),
    'update_item'         => __( 'Update NM Podcast', $textdomain ),
    'search_items'        => __( 'Search NM Podcasts', $textdomain ),
    'not_found'           => __( 'Not found', $textdomain ),
    'not_found_in_trash'  => __( 'Not found in Trash', $textdomain ),
  );
  $args = array(
    'label'               => __( 'nm_podcasts', $textdomain ),
    'description'         => __( 'NM Podcast Description', $textdomain ),
    'labels'              => $labels,
    'supports'            => array( 'title', 'excerpt' ),
    //'taxonomies'          => array( 'category' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'menu_icon'           => 'dashicons-format-audio',
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page'
  );
  register_post_type( 'nm_podcasts', $args );
}




// Add Podcast Categories
//--------------------------------------------------------------------------------------------
/*
add_action( 'init', 'nm_podcast_taxonomies', 0 );
function nm_podcast_taxonomies(){
	global $textdomain;
	$labels = array(
		'name'              => _x( 'NM Podcast Categories', 'taxonomy general name', $textdomain ),
		'singular_name'     => _x( 'NM Podcast Category', 'taxonomy singular name', $textdomain ),
		'search_items'      => __( 'Search Podcast Categories', $textdomain ),
		'all_items'         => __( 'All Podcast Categories', $textdomain ),
		'parent_item'       => __( 'Parent Podcast Category', $textdomain ),
		'parent_item_colon' => __( 'Parent Podcast Category:', $textdomain ),
		'edit_item'         => __( 'Edit Podcast Category', $textdomain ), 
		'update_item'       => __( 'Update Podcast Category', $textdomain ),
		'add_new_item'      => __( 'Add New Podcast Category', $textdomain ),
		'new_item_name'     => __( 'New Podcast Category', $textdomain ),
		'menu_name'         => __( 'Podcast Categories', $textdomain )
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
	global $textdomain;
	$prefix = 'nm_podcast_'; // Prefix for all fields
	$meta_boxes[] = array(
		'id' => 'nm_podcast_metabox',
		'title' => __( 'Podcast Details', $textdomain ),
		'pages' => array( 'nm_podcasts' ), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => __( 'File URL', $textdomain ),
				'desc' => __( 'Direct link to file. Should be mp3 format.', $textdomain ),
				'id' => $prefix . 'file_url',
				'type' => 'text_url'
			),
			array(
				'name' => __( 'Duration', $textdomain ),
				'desc' => __( 'HH:MM:SS', $textdomain ),
				'id' => $prefix . 'file_length',
				'type' => 'text'
			),
			array(
				'name' => __( 'File Size', $textdomain ),
				'desc' => __( 'In bytes', $textdomain ),
				'id' => $prefix . 'file_size',
				'type' => 'text'
			)
		)
	);
	return $meta_boxes;
}

add_action('add_meta_boxes', 'yoast_is_toast', 99);
function yoast_is_toast(){
	remove_meta_box('wpseo_meta', 'nm_podcasts', 'normal');
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




// Add the shortcode
//--------------------------------------------------------------------------------------------
function nm_podcast_module($atts, $content = null) {
	extract(shortcode_atts(array(
		"archive_id" => ''
		), $atts));
	$output = '<div class="podcast-wrap clearfix">
		<h3>' . __( 'Download the free podcast:' ) . '</h3>
		<ul class="podcast" style="text-align: left;">
			<li class="mp3"><a href="http://www.archive.org/download/'.$archive_id.'.mp3">mp3 Download</a></li>
			<li class="itunes"><a href="http://itunes.apple.com/us/podcast/namely-marly/id403607622">Subscribe via iTunes</a></li>
		</ul>
	</div>';
	return $output;
}
add_shortcode("podcast", "nm_podcast_module");




// Add the podcast feature box to post
//--------------------------------------------------------------------------------------------
function nm_podcasts_filter_content( $content ) {
	global $post;
	global $options;
	$podcasts_options = get_option( 'nm_podcasts_options' );
	$nm_podcast = get_post_meta( $post->ID, '_selected_nm_podcasts', true );

	if( is_single() && is_main_query() && !empty( $nm_podcast ) ) {
		$pod_url = get_post_meta( $nm_podcast, 'nm_podcast_file_url', true );
		$output = '<div class="podcast-wrap clearfix">
		<h3>' . __( 'Download the free podcast:' ) . '</h3>
		<ul class="podcast" style="text-align: left;">
			<li class="mp3"><a href="' . $pod_url . '">mp3 Download</a></li>
			<li class="itunes"><a href="' . $podcasts_options['itunes_subscribe_url'] . '">Subscribe via iTunes</a></li>
		</ul>
		</div>';

		$content = $output . $content;	
	}	
	return $content;
}
add_filter( 'the_content', 'nm_podcasts_filter_content' );




// Add 'settings' link to plugin page
//--------------------------------------------------------------------------------------------
add_filter( 'plugin_action_links', 'nm_podcasts_plugin_action_links', 10, 2 );
function nm_podcasts_plugin_action_links( $links, $file ) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=nm-podcasts-admin">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}
