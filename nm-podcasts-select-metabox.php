<?php
/**
 * Description: Adds a metabox to select a podcast.
 * taken from http://wordpress.stackexchange.com/questions/85107/populating-meta-box-with-select-list-of-existing-posts-and-assigning-it-to-cust
 */

class Select_NMPodcast_Metabox {
  var $FOR_POST_TYPE = 'post';
  var $SELECT_POST_TYPE = 'nm_podcasts';
  var $SELECT_POST_LABEL = 'NM Podcasts';
  var $box_id;
  var $box_label;
  var $field_id;
  var $field_label;
  var $field_name;
  var $meta_key;
  function __construct() {
    add_action( 'admin_init', array( $this, 'admin_init' ) );
  }
  function admin_init() {
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
    $this->meta_key     = "_selected_{$this->SELECT_POST_TYPE}";
    $this->box_id       = "select-{$this->SELECT_POST_TYPE}-metabox";
    $this->field_id     = "selected-{$this->SELECT_POST_TYPE}";
    $this->field_name   = "selected_{$this->SELECT_POST_TYPE}";
    $this->box_label    = __( "Select {$this->SELECT_POST_LABEL}", 'select-specials-promo-metabox' );
    $this->field_label  = __( "Choose {$this->SELECT_POST_LABEL}", 'select-specials-promo-metabox' );
  }
  function add_meta_boxes() {
    add_meta_box(
  	  //$id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null
      $this->box_id,
      $this->box_label,
      array( $this, 'select_box' ),
      $this->FOR_POST_TYPE,
      'advanced',
      'high',
      null
    );
  }
  function select_box( $post ) {
    $selected_post_id = get_post_meta( $post->ID, $this->meta_key, true );
    global $wp_post_types;
    $save_hierarchical = $wp_post_types[$this->SELECT_POST_TYPE]->hierarchical;
    $wp_post_types[$this->SELECT_POST_TYPE]->hierarchical = true;
    wp_dropdown_pages( array(
      'id' => $this->field_id,
      'name' => $this->field_name,
      'selected' => empty( $selected_post_id ) ? 0 : $selected_post_id,
      'post_type' => $this->SELECT_POST_TYPE,
      'show_option_none' => $this->field_label,
    ));
    $wp_post_types[$this->SELECT_POST_TYPE]->hierarchical = $save_hierarchical;
  }
  function save_post( $post_id, $post ) {
    if ( $post->post_type == $this->FOR_POST_TYPE && isset( $_POST[$this->field_name] ) ) {
      update_post_meta( $post_id, $this->meta_key, $_POST[$this->field_name] );
    }
  }
}
new Select_NMPodcast_Metabox();