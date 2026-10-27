<?php
/**
 * Plugin Name:       Before After Slider for Elementor
 * Description:       A lightweight, accessible before/after image comparison slider. Elementor widget plus a shortcode that works anywhere.
 * Version:           1.3.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Noushad Nipun
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       before-after-slider-for-elementor
 *
 * @package BASFE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BASFE_VERSION', '1.3.0' );
define( 'BASFE_FILE', __FILE__ );
define( 'BASFE_PATH', plugin_dir_path( __FILE__ ) );
define( 'BASFE_URL', plugin_dir_url( __FILE__ ) );

require_once BASFE_PATH . 'includes/functions.php';

if ( is_admin() ) {
	require_once BASFE_PATH . 'includes/settings.php';
}

add_action( 'init', 'basfe_register_assets' );
add_action( 'init', 'basfe_register_block', 20 );
add_shortcode( 'before_after_slider', 'basfe_shortcode' );

// Fires only when Elementor is active, regardless of plugin load order.
add_action( 'elementor/widgets/register', 'basfe_register_widget' );

/**
 * Register (not enqueue) assets; they load only when the slider is used.
 */
function basfe_register_assets() {
	$deps = defined( 'ELEMENTOR_VERSION' ) ? array( 'elementor-frontend' ) : array();
	wp_register_style( 'basfe-style', BASFE_URL . 'assets/css/style.css', array(), BASFE_VERSION );
	wp_add_inline_style( 'basfe-style', basfe_global_css() );
	// Used by the Elementor widget.
	wp_register_script( 'basfe-script', BASFE_URL . 'assets/js/script.js', $deps, BASFE_VERSION, true );
	// Used by the shortcode and the block (no Elementor dependency).
	wp_register_script( 'basfe-view', BASFE_URL . 'assets/js/script.js', array(), BASFE_VERSION, true );
}

/**
 * Register the Elementor widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
 */
function basfe_register_widget( $widgets_manager ) {
	require_once BASFE_PATH . 'includes/class-basfe-widget.php';
	$widgets_manager->register( new Basfe_Widget() );
}
