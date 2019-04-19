<?php
/**
 * Settings: Delivery Day Time Frame.
 *
 * @author     WooThemes
 * @package    WC_OD/Admin/Settings
 * @since      1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	include_once 'class-wc-od-settings-time-frame.php';
}

if ( ! class_exists( 'WC_OD_Settings_Delivery_Day_Time_Frame', false ) ) {
	/**
	 * Class WC_OD_Settings_Delivery_Day_Time_Frame
	 */
	class WC_OD_Settings_Delivery_Day_Time_Frame extends WC_OD_Settings_Time_Frame {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'delivery_days';

		/**
		 * The delivery day ID.
		 *
		 * @var mixed A weekday index (0-6). False otherwise.
		 */
		protected $day_id;

		/**
		 * Constructor.
		 *
		 * @param mixed  $day_id   The delivery day ID.
		 * @param string $frame_id The time frame ID.
		 */
		public function __construct( $day_id, $frame_id ) {
			$this->day_id = $day_id;

			parent::__construct( $frame_id );
		}

		/**
		 * Initialise form fields.
		 *
		 * @since 1.5.0
		 */
		public function init_form_fields() {
			parent::init_form_fields();

			if ( $this->is_new() ) {
				$this->form_fields['delivery_days'] = array(
					'title'             => __( 'Delivery days', 'woocommerce-order-delivery' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'width: 400px;',
					'description'       => __( 'Choose the delivery days in which this time frame is available.', 'woocommerce-order-delivery' ),
					'desc_tip'          => true,
					'select_buttons'    => true,
					'options'           => wc_od_get_week_days(),
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select delivery days', 'woocommerce-order-delivery' ),
					),
				);
			}
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			parent::init_settings();

			$delivery_day = ( isset( $this->settings[ $this->day_id ] ) ? $this->settings[ $this->day_id ] : array() );
			$time_frames  = ( isset( $delivery_day['time_frames'] ) ? $delivery_day['time_frames'] : array() );

			$this->settings = ( isset( $time_frames[ $this->frame_id ] ) ? $time_frames[ $this->frame_id ] : $this->get_form_fields_defaults() );

			if ( $this->is_new() ) {
				$this->settings['shipping_methods'] = ( isset( $delivery_day['shipping_methods'] ) ? $delivery_day['shipping_methods'] : array() );
				$this->settings['delivery_days']    = array( (string) $this->day_id );
			}
		}

		/**
		 * Output the settings screen.
		 *
		 * @since 1.5.0
		 */
		public function admin_options() {
			$this->enqueue_scripts();

			$weekdays = wc_od_get_week_days();
			$label    = ( $this->is_new() ? __( 'Add Time Frame', 'woocommerce-order-delivery' ) : $this->settings['title'] );

			echo '<h2>';

			echo str_replace(
				array(
					'[delivery_day]',
					'[time_frame]',
				),
				array(
					$weekdays[ $this->day_id ],
					$label,
				),
				esc_html_x( 'Delivery days > [delivery_day] > [time_frame]', 'time frame settings page title', 'woocommerce-order-delivery' )
			); // WPCS: XSS ok.

			wc_back_link(
				__( 'Return to the delivery settings', 'woocommerce-order-delivery' ),
				wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) )
			);

			echo '</h2>';

			parent::admin_options();
		}

		/**
		 * Processes and saves options.
		 *
		 * @since 1.5.0
		 *
		 * @return bool was anything saved?
		 */
		public function process_admin_options() {
			$saved = parent::process_admin_options();

			$this->maybe_redirect();

			return $saved;
		}

		/**
		 * Redirect to the current delivery day screen after save a new time frame.
		 *
		 * @since 1.5.0
		 */
		public function maybe_redirect() {
			if ( $this->is_new() ) {
				wp_safe_redirect( wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) ) );
				exit;
			}
		}

		/**
		 * Sanitize the settings before save the option.
		 *
		 * @since 1.5.0
		 *
		 * @param array $settings The settings to sanitize.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			// Sanitize settings with an array as the default value.
			foreach ( array( 'shipping_methods', 'delivery_days' ) as $setting_id ) {
				if ( ! isset( $settings[ $setting_id ] ) || ! is_array( $settings[ $setting_id ] ) ) {
					$settings[ $setting_id ] = array();
				}
			}

			// Insert the time frame settings into the 'delivery_days' setting.
			$delivery_days = WC_OD()->settings()->get_setting( 'delivery_days' );

			if ( $this->is_new() ) {
				$chosen_delivery_days = array_map( 'intval', $settings['delivery_days'] );

				unset( $settings['delivery_days'] );

				foreach ( $chosen_delivery_days as $index ) {
					$time_frames   = ( isset( $delivery_days[ $index ]['time_frames'] ) ? $delivery_days[ $index ]['time_frames'] : array() );
					$time_frames[] = $settings;

					$delivery_days[ $index ]['time_frames'] = $time_frames;
				}
			} else {
				$delivery_days[ $this->day_id ]['time_frames'][ $this->frame_id ] = $settings;
			}

			return $delivery_days;
		}
	}
}
