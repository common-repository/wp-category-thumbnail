<?php 
/**
 * Class WP_Category_Thumbnail
 * 
 * @package WP_Category_Thumbnail
 * @version 1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly.
}

class WP_Category_Thumbnail extends WP_Widget {

  /**
   * Constructor.
   *
   * @access public
   * @since 1.0.6
   */
  public function __construct() {
    parent::__construct( false, $name = 'WP Category Thumbnail' );
    add_action( 'wp_print_scripts', array( $this, 'load_script' ) );
  }

  //script load
  public function load_script() {
    echo '<link rel="stylesheet" type="text/css" href="' .WPCT_PLUGIN_URL.'css/wpct.css" />'."\n";
    wp_enqueue_script( 'jquery-thumb', WPCT_PLUGIN_URL. 'js/wpct.js', '1.0' );
  }

  //widget function
  public function widget( $args, $instance ) {
    extract( $args );
    echo $before_widget;

      //WP post Query
      $latestpost = new WP_Query();
      $latestpost->query( array(
         "showposts"   => $instance['num_post'],
         "cat"         => $instance['category'],
         "post_type"   => $instance['post_type'],
         "orderby"     => "date",
         "order"       => "DESC",
       ) );

      //add new_excerpt_more
      if( isset( $instance['more_link'] ) ) {
        if( ! function_exists( 'new_excerpt_more' ) ) {
          function new_excerpt_more() {
            global $post;
            return '<br><a class="wpct-read-more" href="'. get_permalink( $post->ID ) .'"> '. __( 'Read more', 'wp-category-thumbnail' ) .' &raquo;</a>';
          }
        }
       add_filter( 'excerpt_more', 'new_excerpt_more' );
      }

      //define length for excerpt
      $excerpt_length = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '15';
      $new_length = create_function( '$length', "return " .$instance['excerpt_length']. ";" );
      if( $excerpt_length > 0 )
      add_filter( 'excerpt_length', $new_length );

      // display widget content
      if( ! empty( $instance['title'] ) )
        echo $before_title . $instance['title'] . $after_title;
         ?>
          <ul class="wpct custom-wpct">
            <?php
              while( $latestpost->have_posts() ):
               $latestpost->the_post();
                global $post, $posts;
                $postContent          = $post->post_content;
                $input_width          = isset( $instance['thumb_width'] ) ? $instance['thumb_width'] : '';
                $input_height         = isset( $instance['thumb_width'] ) ? $instance['thumb_width'] : '';
                $input_content_color  = isset( $instance['content_color'] ) ? $instance['content_color'] : '';
                $input_overlay_color  = isset( $instance['overlay_color'] ) ? $instance['overlay_color'] : '';
              ?>
              <style type="text/css">
              <!--
              <?php if( $input_content_color ): ?>
                .wpct-box-content .thumb-title a, .wpct-box-content a, .wpct-read-more {
                  color: <?php echo $input_content_color; ?>
                }
                .wpct-box-content .thumb-title a:hover, .wpct-box-content a:hover, .wpct-read-more:hover {
                  opacity: 0.7;
                }
              <?php endif; ?>
              //-->
              </style>
              <li>
                <div class="wpct-wrap custom-wpct-wrap">
                <?php if ( has_post_thumbnail( $post->ID ) ): ?>
                  <?php
                    add_image_size('wp-cat-thumb', $input_width, $input_height, true);
                  ?>
                  <a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php the_title(); ?>"><?php
                  the_post_thumbnail( 'wp-cat-thumb', array( 'class' => 'wpct-img custom-wpct-box' ) ); ?></a>
                <?php endif; ?>

                  <div class="wpct-box custom-wpct-box" style="color: <?php echo $input_content_color; ?>;">

                    <div class="wpct-box-content" style="background: <?php echo $input_overlay_color; ?>;">
                    <?php if( isset( $instance['show_title'] ) ):?>
                      <h3 class="thumb-title"><a href="<?php echo get_permalink( $post->ID ); ?>"><?php the_title(); ?></a></h3>
                     <?php endif; //show_title ?>
                      <?php if( isset( $instance['post_date'] ) ): ?>
                      <span class="thumb-date"><?php the_time( 'jS F, Y' ); ?></span>
                      <?php endif; ?>
                      <?php if( isset( $instance['comment_num'] ) ): ?>
                      <span class="comment-num"><?php comments_number(); ?></span>
                      <?php endif; ?>
                      <div class="excerpt-section">
                        <?php the_excerpt(); ?>
                      </div>
                    </div><!--.wpct-box-content-->
                 </div>
                </div><!--.wpct_box-wrap-->
              </li>

          <?php endwhile; ?>
        </ul>
         <?php
    echo $after_widget;
  }//widget function

  //widget update option
  public function update_i18n( $new_instance, $old_instance ) { }

  //widget form
  public function form( $instance ) {
   ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php esc_html_e( 'Title', 'wp-category-thumbnail' ); ?>
        <?php $instance_title = isset( $instance['title'] ) ? $instance['title'] : ''; ?>
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr_e( $instance_title ); ?>" class="widefat" />
      </label>
    </p>

      <p>
        <label>
          <?php esc_html_e( 'Post Type', 'wp-category-thumbnail' ); ?>
          <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
            <?php
            $post_types = array( 'post' => 'Posts', 'page' => 'Pages' );
            $instance_post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : '';
            foreach( $post_types as $id => $post_type ): ?>
              <option value="<?php echo $id; ?>" <?php echo selected( $instance_post_type, $id )?>><?php echo $post_type; ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </p>

    <p>
      <label>
        <?php esc_html_e( 'Category', 'wp-category-thumbnail' ); ?>
        <?php $instance_category = isset( $instance['category'] ) ? $instance['category'] : ''; ?>
        <?php wp_dropdown_categories( array(
            'show_option_all'  => 'all categories',
            'selected'         => $instance_category,
            'name'             => $this->get_field_name( 'category' ),
          ) );
       ?>
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'num_post' ); ?>">
        <?php esc_html_e( 'Number of posts to show', 'wp-category-thumbnail' ); ?>
        <?php $instance_num_post = isset( $instance['num_post'] ) ? $instance['num_post'] : ''; ?>
        <input id="<?php echo $this->get_field_id( 'num_post' ); ?>" name="<?php echo $this->get_field_name( 'num_post' ); ?>" type="text" value="<?php echo (int) $instance_num_post; ?>" size="3" />
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>">
        <?php esc_html_e( 'Excerpt length (in words)', 'wp-category-thumbnail' ); ?>
        <?php $instance_excerpt_length = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : ''; ?>
        <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="text" value="<?php echo $instance_excerpt_length; ?>" size="3" />
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'show_title' ); ?>">
        <?php $instance_show_title = isset( $instance['show_title'] ) ? $instance['show_title'] : ''; ?>
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>"  <?php checked( ( bool )$instance_show_title, true ); ?> />
       <?php esc_html_e( 'Show Post title', 'wp-category-thumbnail' ); ?>
     </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'post_date' ); ?>">
        <?php $instance_post_date = isset( $instance['post_date'] ) ? $instance['post_date'] : ''; ?>
        <input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'post_date' ); ?>" id="<?php echo $this->get_field_id( 'post_date' ); ?>" <?php checked( ( bool ) $instance_post_date, true ); ?> />
        <?php esc_html_e( 'Show Post date', 'wp-category-thumbnail' ); ?>
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'comment_num' ); ?>">
        <?php $instance_comment_num = isset( $instance['comment_num'] ) ? $instance['comment_num'] : ''; ?>
        <input type="checkbox" name="<?php echo $this->get_field_name( 'comment_num' ); ?>" id="<?php echo $this->get_field_id( 'comment_num' ); ?>" class="checkbox" <?php echo checked( ( bool )$instance_comment_num, true ); ?> />
        <?php esc_html_e( 'Show number of Comment', 'wp-category-thumbnail' ); ?>
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'more_link' ); ?>">
        <?php $instance_more_link = isset( $instance['more_link'] ) ? $instance['more_link'] : ''; ?>
        <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name( 'more_link' ); ?>" id="<?php echo $this->get_field_id( 'more_link' ); ?>"  <?php checked( ( bool ) $instance_more_link, true ); ?> />
      <?php esc_html_e( 'Show more link', 'wp-category-thumbnail' ); ?>
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'overlay_color' ); ?>">
        <?php $instance_overlay_color = isset( $instance['overlay_color'] ) ? $instance['overlay_color'] : ''; ?>
        <?php esc_html_e( 'Background color', 'wp-category-thumbnail' ); ?>
        <input id="<?php echo $this->get_field_id( 'overlay_color' ); ?>" name="<?php echo $this->get_field_name( 'overlay_color' ); ?>" type="text" value="<?php echo $instance_overlay_color; ?>" size="8" />
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id( 'content_color' ); ?>">
        <?php esc_html_e( 'Content color', 'wp-category-thumbnail' ); ?>
        <?php $instance_content_color = isset( $instance['content_color'] ) ? $instance['content_color'] : ''; ?>
        <input id="<?php echo $this->get_field_id( 'content_color' ); ?>" name="<?php echo $this->get_field_name( 'content_color' ); ?>" type="text" value="<?php echo $instance_content_color; ?>" size="8" />
      </label>
    </p>

    <p>
      <label><?php esc_html_e( 'Thumbnail dimensions(in px)', 'wp-category-thumbnail' ); ?></label>
        <div>
        <label>
          <?php $instance_thumb_width = isset( $instance['thumb_width'] ) ? $instance['thumb_width'] : ''; ?>
          <?php $instance_thumb_height = isset( $instance['thumb_height'] ) ? $instance['thumb_height'] : ''; ?>
          <label for="<?php echo $this->get_field_id( 'thumb_width' ); ?>">
            <?php esc_html_e( 'W', 'wp-category-thumbnail' ); ?><input type="text" name="<?php echo $this->get_field_name( 'thumb_width' ); ?>" id="<?php echo $this->get_field_id( 'thumb_width' ); ?>" value="<?php echo $instance_thumb_width; ?>" size="6"/>
          </label>
          <label for="<?php echo $this->get_field_id( 'thumb_height' ); ?>">
            <?php esc_html_e( 'H', 'wp-category-thumbnail' ); ?><input type="text" name="<?php echo $this->get_field_name( 'thumb_height' ); ?>" id="<?php echo $this->get_field_id( 'thumb_height' ); ?>" value="<?php echo $instance_thumb_height; ?>" size="6"/>
          </label>
        </div>
    </p>

    <?php
  }//form end

}//WP_Category_Thumbnail class
