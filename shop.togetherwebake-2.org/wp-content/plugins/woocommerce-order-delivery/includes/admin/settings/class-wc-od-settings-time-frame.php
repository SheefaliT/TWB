<?php
/**
 * Settings: Delivery Time Frame.
 *
 * @author     WooThemes
 * @package    WC_OD/Admin/Settings
 * @since      1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	include_once 'abstract-class-wc-od-settings-api.php';
}

if ( ! class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	/**
	 * Class WC_OD_Settings_Time_Frame
	 */
	class WC_OD_Settings_Time_Frame extends WC_OD_Settings_API {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'time_frames';

		/**
		 * The time frame ID.
		 *
		 * @var string
		 */
		public $frame_id;

		/**
		 * Constructor.
		 *
		 * @param string $frame_id The time frame ID.
		 */
		public function __construct( $frame_id ) {
			$this->frame_id = $frame_id;

			parent::__construct();
		}

		/**
		 * Gets if it's a new time frame or not.
		 *
		 * @since 1.5.0
		 *
		 * @return bool
		 */
		public function is_new() {
			return ( 'new' === $this->frame_id );
		}

		/**
		 * Initialise form fields.
		 *
		 * @since 1.5.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'title'            => array(
					'title'             => __( 'Title', 'woocommerce-order-delivery' ),
					'type'              => 'text',
					'description'       => __( 'This controls the title which the user sees during checkout.', 'woocommerce-order-delivery' ),
					'desc_tip'          => true,
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				'time_from'        => array(
					'title'             => __( 'Time from', 'woocommerce-order-delivery' ),
					'type'              => 'text',
					'description'       => __( 'The starting time of this time frame.', 'woocommerce-order-delivery' ),
					'desc_tip'          => true,
					'class'             => 'timepicker',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				'time_to'          => array(
					'title'             => __( 'Time to', 'woocommerce-order-delivery' ),
					'type'              => 'text',
					'description'       => __( 'The ending time of this time frame.', 'woocommerce-order-delivery' ),
					'desc_tip'          => true,
					'class'             => 'timepicker',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				'shipping_methods' => array(
					'title'             => __( 'Shipping methods', 'woocommerce-order-delivery' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select-nostd',
					'css'               => 'width: 400px;',
					'description'       => __( 'Choose the available shipping methods for this time frame.', 'woocommerce-order-delivery' ),
					'desc_tip'          => true,
					'options'           => wc_od_get_shipping_methods_choices(),
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select shipping methods', 'woocommerce-order-delivery' ),
					),
				),
			);
		}

		/**
		 * Enqueue the settings scripts.
		 *
		 * @since 1.5.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.11.14' );
			wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.11.14', true );
			wp_enqueue_script( 'wc-od-admin-settings-time-frame', WC_OD_URL . 'assets/js/admin/settings-time-frame.js', array( 'jquery', 'jquery-timepicker' ), WC_OD_VERSION, true );
		}
	}
}
