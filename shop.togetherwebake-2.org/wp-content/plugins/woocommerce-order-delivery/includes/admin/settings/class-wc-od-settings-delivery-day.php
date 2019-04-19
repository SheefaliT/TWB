<?php
/**
 * Settings: Delivery Day.
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

if ( ! class_exists( 'WC_OD_Settings_Delivery_Day', false ) ) {
	/**
	 * Class WC_OD_Settings_Delivery_Day
	 */
	class WC_OD_Settings_Delivery_Day extends WC_OD_Settings_API {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'delivery_days';

		/**
		 * The day ID.
		 *
		 * @var int
		 */
		public $day_id;

		/**
		 * Constructor.
		 *
		 * @param int $day_id The delivery day ID.
		 */
		public function __construct( $day_id ) {
			$this->day_id = $day_id;

			parent::__construct();
		}

		/**
		 * Initialise form fields.
		 *
		 * @since 1.5.0
		 */
		public function init_form_fields() {
			$delivery_days = WC_OD()->settings()->get_setting( 'delivery_days' );

			$this->form_fields = array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'woocommerce-order-delivery' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this day for delivery', 'woocommerce-order-delivery' ),
					'default' => $delivery_days[ $this->day_id ]['enabled'],
				),
			);

			if ( empty( $delivery_days[ $this->day_id ]['time_frames'] ) ) {
				$this->form_fields['shipping_methods'] = array(
					'title'             => __( 'Shipping methods', 'woocommerce-order-delivery' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select-nostd',
					'css'               => 'width: 400px;',
					'desc_tip'          => __( 'Choose the available shipping methods for this delivery day.', 'woocommerce-order-delivery' ),
					'description'       => __( 'Use the time frames for a more specific configuration.', 'woocommerce-order-delivery' ),
					'options'           => wc_od_get_shipping_methods_choices(),
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select shipping methods', 'woocommerce-order-delivery' ),
					),
				);
			}

			$this->form_fields['time_frames'] = array(
				'title'    => __( 'Time frames', 'woocommerce-order-delivery' ),
				'type'     => 'wc_od_table',
				'desc'     => __( 'Define the time frames for this delivery day.', 'woocommerce-order-delivery' ),
				'desc_tip' => true,
			);
		}

		/**
		 * Enqueue the settings scripts.
		 *
		 * @since 1.5.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wc-od-admin-settings-delivery-day', WC_OD_URL . 'assets/js/admin/settings-delivery-day.js', array( 'jquery' ), WC_OD_VERSION, true );
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			parent::init_settings();

			$this->settings = ( isset( $this->settings[ $this->day_id ] ) ? $this->settings[ $this->day_id ] : $this->get_form_fields_defaults() );

			// Backward compatibility.
			$this->settings['enabled'] = wc_od_bool_to_string( $this->settings['enabled'] );

			foreach ( array( 'time_frames', 'shipping_methods' ) as $setting_id ) {
				if ( ! isset( $this->settings[ $setting_id ] ) || ! is_array( $this->settings[ $setting_id ] ) ) {
					$this->settings[ $setting_id ] = array();
				}
			}
		}

		/**
		 * Output the settings screen.
		 *
		 * @since 1.5.0
		 */
		public function admin_options() {
			$weekdays = wc_od_get_week_days();

			$this->enqueue_scripts();

			echo '<h2>';
			/* translators: %s: week day name */
			printf( esc_html_x( 'Delivery days > %s', 'delivery day settings page title', 'woocommerce-order-delivery' ), $weekdays[ $this->day_id ] ); // WPCS: XSS ok.
			wc_back_link( __( 'Return to shipping options', 'woocommerce-order-delivery' ), wc_od_get_settings_url() );
			echo '</h2>';

			echo wpautop( wp_kses_post( __( 'Edit the delivery day settings.', 'woocommerce-order-delivery' ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			parent::admin_options();
		}

		/**
		 * Merge the day settings with the rest of days before save the option.
		 *
		 * @param array $settings The sanitized settings.
		 *
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			// Sanitize settings with an array as the default value.
			foreach ( array( 'time_frames', 'shipping_methods' ) as $setting_id ) {
				if ( ! isset( $settings[ $setting_id ] ) || ! is_array( $settings[ $setting_id ] ) ) {
					$settings[ $setting_id ] = array();
				}
			}

			$delivery_days = WC_OD()->settings()->get_setting( 'delivery_days' );

			$delivery_days[ $this->day_id ] = $settings;

			return $delivery_days;
		}
	}
}
