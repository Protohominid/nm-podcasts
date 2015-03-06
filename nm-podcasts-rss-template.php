<?php 
/** 
Template Name: NM Podcasts RSS
**/

global $options;
$podcasts_options = get_option( 'nm_podcasts_options' );

// Query the Podcast Custom Post Type and fetch the latest 100 posts
$args = array( 'post_type' => 'nm_podcasts', 'posts_per_page' => 100 );
$loop = new WP_Query( $args );

// Output the XML header
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<?php // Start the iTunes RSS Feed: https://www.apple.com/itunes/podcasts/specs.html ?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">

  <channel>
	
    <title><?php echo !empty( $podcasts_options['podcast_title'] ) ? esc_attr( $podcasts_options['podcast_title'] ) : get_bloginfo('name') . ' Podcast'; ?></title>
    <link><?php echo get_bloginfo('url'); ?></link>
    <language><?php echo get_bloginfo ( 'language' ); ?></language>
    <copyright><?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?></copyright>
    
    <itunes:author><?php echo esc_attr( get_bloginfo('name') ); ?></itunes:author>
    <itunes:summary><?php echo $podcasts_options['podcast_summary']; ?></itunes:summary>
    <description><?php echo get_bloginfo('url'); ?></description>
    
    <itunes:owner>
      <itunes:name><?php echo esc_attr( get_bloginfo('name') ); ?></itunes:name>
      <itunes:email><?php echo get_bloginfo('admin_email'); ?></itunes:email>
    </itunes:owner>
    
    <itunes:image href="<?php echo $podcasts_options['cover_art_url']; ?>" />
    
    <itunes:category text="<?php echo esc_attr( $podcasts_options['podcast_category'] ); ?>">
      <itunes:category text="<?php echo esc_attr( $podcasts_options['podcast_subcategory'] ); ?>"/>
    </itunes:category>
    <itunes:explicit>no</itunes:explicit>
    <?php // Start the loop for Podcast posts
    while ( $loop->have_posts() ) : $loop->the_post(); $pid = get_the_id(); ?>
    <item>
      <title><?php echo html_entity_decode( get_the_title() ); ?></title>
      <itunes:author><?php echo get_bloginfo('name'); ?></itunes:author>
      <itunes:summary><?php echo get_the_excerpt(); ?></itunes:summary>
      
      <?php $file_url = get_post_meta( $pid, 'nm_podcast_file_url', true ); ?>
      <enclosure url="<?php echo $file_url; ?>" length="<?php echo get_post_meta( $pid, 'nm_podcast_file_size', true ); ?>" type="audio/mpeg" />
      <guid><?php echo $file_url; ?></guid>
      <pubDate><?php the_time( 'D, d M Y H:i:s T') ?></pubDate>
      <itunes:duration><?php echo get_post_meta( $pid, 'nm_podcast_file_length', true ); ?></itunes:duration>
    </item>
    <?php endwhile; ?>
  
  </channel>

</rss>