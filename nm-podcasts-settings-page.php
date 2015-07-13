<?php
class NMPodcastsSettingsPage
{
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// submenu under custom post type menu
		add_submenu_page(
			'edit.php?post_type=nm_podcasts',
			'Podcasts Settings Admin', 
			'Settings', 
			'manage_options', 
			'nm-podcasts-admin', 
			array( $this, 'create_admin_page' )
		);
		// This page will be under "Settings"
		/*
		add_options_page(
			'Podcasts Settings Admin', 
			'NM Podcasts', 
			'manage_options', 
			'nm-podcasts-admin', 
			array( $this, 'create_admin_page' )
		);
		*/
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'nm_podcasts_options' );

		// Add admin messages
		if( isset( $_GET['settings-updated'] ) ) {
			echo '<div id="message" class="';
			echo $_GET['settings-updated'] == 'false' ? 'error"><p><strong>There was an error. The settings were not saved.' : 'updated"><p><strong>Settings saved.';
			echo '</strong></p></div>';
		}
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Podcasts Settings</h2>		   
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'nm_podcasts_option_group' );	
				do_settings_sections( 'nm-podcasts-admin' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{		
		
		register_setting(
			'nm_podcasts_option_group', // Option group
			'nm_podcasts_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'section_one', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'nm-podcasts-admin' // Page
		);	

		// START - Fields
		
		add_settings_field(
			'podcast_title', // ID
			'Podcast Title', // Title 
			array( $this, 'podcast_title_callback' ), // Callback
			'nm-podcasts-admin', // Page
			'section_one' // Section			  
		);		

		add_settings_field(
			'itunes_subscribe_url', // ID
			'iTunes Subscribe URL', // Title 
			array( $this, 'itunes_subscribe_url_callback' ), // Callback
			'nm-podcasts-admin', // Page
			'section_one' // Section			  
		);		

		add_settings_field(
			'stitcher_subscribe_url', // ID
			'Stitcher Subscribe URL', // Title 
			array( $this, 'stitcher_subscribe_url_callback' ), // Callback
			'nm-podcasts-admin', // Page
			'section_one' // Section			  
		);		

		add_settings_field(
			'cover_art_url', // ID
			'Cover Art URL', // Title 
			array( $this, 'cover_art_url_callback' ), // Callback
			'nm-podcasts-admin', // Page
			'section_one' // Section			  
		);		

		add_settings_field(
			'podcast_summary', 
			'Podcast Summary', 
			array( $this, 'podcast_summary_callback' ), 
			'nm-podcasts-admin', 
			'section_one'
		);

		add_settings_field(
			'podcast_category', 
			'Podcast Category', 
			array( $this, 'podcast_category_callback' ), 
			'nm-podcasts-admin', 
			'section_one'
		);

		add_settings_field(
			'podcast_subcategory', 
			'Podcast Subcategory', 
			array( $this, 'podcast_subcategory_callback' ), 
			'nm-podcasts-admin', 
			'section_one'
		);
		
		// END Fields	
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$new_input = array();
		if( isset( $input['podcast_title'] ) )
			$new_input['podcast_title'] = sanitize_text_field( $input['podcast_title'] );

		if( isset( $input['itunes_subscribe_url'] ) )
			$new_input['itunes_subscribe_url'] = sanitize_text_field( $input['itunes_subscribe_url'] );

		if( isset( $input['stitcher_subscribe_url'] ) )
			$new_input['stitcher_subscribe_url'] = sanitize_text_field( $input['stitcher_subscribe_url'] );

		if( isset( $input['cover_art_url'] ) )
			$new_input['cover_art_url'] = sanitize_text_field( $input['cover_art_url'] );

		if( isset( $input['podcast_summary'] ) )
			$new_input['podcast_summary'] = sanitize_text_field( $input['podcast_summary'] );

		if( isset( $input['podcast_category'] ) )
			$new_input['podcast_category'] = sanitize_text_field( $input['podcast_category'] );

		if( isset( $input['podcast_subcategory'] ) )
			$new_input['podcast_subcategory'] = sanitize_text_field( $input['podcast_subcategory'] );

		return $new_input;
	}

	/** 
	 * Print the Section text
	 */
	public function print_section_info()
	{
		print '<p>These settings are used for the podcast RSS feed.<br>Category options are listed <a href="http://www.apple.com/itunes/podcasts/specs.html#categories" target="_blank">here.</a></p>';
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public function podcast_title_callback()
	{
		printf(
			'<input type="text" id="podcast_title" name="nm_podcasts_options[podcast_title]" value="%s" />',
			isset( $this->options['podcast_title'] ) ? esc_attr( $this->options['podcast_title']) : ''
		);
	}
	public function itunes_subscribe_url_callback()
	{
		printf(
			'<input type="text" id="itunes_subscribe_url" name="nm_podcasts_options[itunes_subscribe_url]" value="%s" style="width: 100%%;" />',
			isset( $this->options['itunes_subscribe_url'] ) ? esc_attr( $this->options['itunes_subscribe_url']) : ''
		);
	}
	public function stitcher_subscribe_url_callback()
	{
		printf(
			'<input type="text" id="stitcher_subscribe_url" name="nm_podcasts_options[stitcher_subscribe_url]" value="%s" style="width: 100%%;" />',
			isset( $this->options['stitcher_subscribe_url'] ) ? esc_attr( $this->options['stitcher_subscribe_url']) : ''
		);
	}
	public function cover_art_url_callback()
	{
		printf(
			'<input type="text" id="cover_art_url" name="nm_podcasts_options[cover_art_url]" value="%s" style="width: 100%%;" /><br><em>Direct link to podcast cover art. Should be 1400x1400.</em>',
			isset( $this->options['cover_art_url'] ) ? esc_attr( $this->options['cover_art_url']) : ''
		);
	}
	public function podcast_summary_callback()
	{
		printf(
			'<textarea type="textarea" id="podcast_summary" name="nm_podcasts_options[podcast_summary]" style="width: 100%%; height: 5em;">%s</textarea><br><em>Short description of the podcast.</em>',
			isset( $this->options['podcast_summary'] ) ? esc_attr( $this->options['podcast_summary']) : ''
		);
	}
	public function podcast_category_callback()
	{
		printf(
			'<input type="text" id="podcast_category" name="nm_podcasts_options[podcast_category]" value="%s" /><br><em>Top-level category. See list at link above.</em>',
			isset( $this->options['podcast_category'] ) ? esc_attr( $this->options['podcast_category']) : ''
		);
	}
	public function podcast_subcategory_callback()
	{
		printf(
			'<input type="text" id="podcast_subcategory" name="nm_podcasts_options[podcast_subcategory]" value="%s" /><br><em>Secondary category. See list at link above.</em>',
			isset( $this->options['podcast_subcategory'] ) ? esc_attr( $this->options['podcast_subcategory']) : ''
		);
	}
}

if( is_admin() )
	$my_settings_page = new NMPodcastsSettingsPage();
