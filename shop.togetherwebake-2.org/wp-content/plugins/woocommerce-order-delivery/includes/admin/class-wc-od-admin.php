<?php
/**
 * WC_OD Admin
 *
 * @author      WooThemes
 * @package     WC_OD/Admin
 * @since       1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_OD_Admin', false ) ) {
	/**
	 * Class WC_OD_Admin
	 */
	class WC_OD_Admin {

		/**
		 * Constructor.
		 *
		 * @since 1.5.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'includes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		}

		/**
		 * Include any classes we need within admin.
		 *
		 * @since 1.5.0
		 */
		public function includes() {
			include_once 'wc-od-admin-init.php';
		}

		/**
		 * Enqueue scripts
		 *
		 * @since 1.5.0
		 */
		public function enqueue_scripts() {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';

			if ( 'shop_order' === $screen_id ) {
				wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.11.14' );
				wp_enqueue_style( 'wc-od-admin', WC_OD_URL . 'assets/css/wc-od-admin.css', array( 'woocommerce_admin_styles' ), WC_OD_VERSION );

				wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.11.14', true );
				wp_enqueue_script( 'wc-od-admin-meta-boxes-order', WC_OD_URL . 'assets/js/admin/meta-boxes-order.js', array( 'jquery', 'jquery-timepicker' ), WC_OD_VERSION, true );
			}
		}

		/**
		 * Adds custom meta boxes.
		 *
		 * @since 1.5.0
		 */
		public function add_meta_boxes() {
			add_meta_box( 'woocommerce-order-delivery', _x( 'Delivery', 'meta box title', 'woocommerce-order-delivery' ), 'WC_OD_Meta_Box_Order_Delivery::output', 'shop_order', 'side', 'default' );
		}
	}
}

return new WC_OD_Admin();
