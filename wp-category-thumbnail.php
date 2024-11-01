<?php
/**
 * Plugin Name: WP Category Thumbnail
 * Plugin URI:  https://www.nettantra.com/wordpress/
 * Description: This plugin provides thumbnails for categories fetched from the latest page/post of the corresponding category. The post thumbnail are generated from its featured image in the post.
 * Version:     1.0.7
 * Author:      NetTantra
 * Author URI:  https://nettantra.com
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp_category_thumbnail
 * Domain Path: /languages/
 *
 * @package WP_Category_Thumbnail
 */


if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly.
}


//define path

if( ! defined( 'WPCT_PLUGIN_DIR' ) )
  define( 'WPCT_PLUGIN_DIR', dirname( __FILE__ ) );

if ( ! defined( 'WPCT_PLUGIN_BASENAME' ) )
  define( 'WPCT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if( ! defined( 'WPCT_PLUGIN_URL' ) )
  define( 'WPCT_PLUGIN_URL', WP_PLUGIN_URL.'/'.str_replace( basename( __FILE__ ), '', WPCT_PLUGIN_BASENAME ) );


if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
  add_action( 'admin_notices', 'wpct_fail_php_version' );
} else {
  add_action( 'widgets_init', 'wpct' );
  function wpct() {
    register_widget( 'WP_Category_Thumbnail' );
  }
  // Include the WP_Category_Thumbnail class.
  require_once dirname( __FILE__ ) . '/inc/class-wp-category-thumbnail.php';
}


/**
 * Admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function wpct_fail_php_version() {

  if ( isset( $_GET['activate'] ) ) {
    unset( $_GET['activate'] );
  }

  /* translators: %s: PHP version */
  $message      = sprintf( esc_html__( 'WP Category Thumbnail requires PHP version %s+, plugin is currently NOT RUNNING.', 'wp-category-thumbnail' ), '5.6' );
  $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
  echo wp_kses_post( $html_message );
}

