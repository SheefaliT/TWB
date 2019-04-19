<?php
/**
 * Abstract Settings API Class
 *
 * @author     WooThemes
 * @package    WC_OD/Admin/Settings
 * @since      1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	/**
	 * Class WC_OD_Settings_API
	 */
	abstract class WC_OD_Settings_API extends WC_Settings_API {

		/**
		 * The plugin ID. Used for option names.
		 *
		 * @var string
		 */
		public $plugin_id = 'wc_od_';

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->init_form_fields();
			$this->init_settings();

			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitized_fields' ) );
		}

		/**
		 * Return the name of the option in the WP DB.
		 *
		 * @since 1.5.0
		 *
		 * @return string
		 */
		public function get_option_key() {
			return $this->plugin_id . $this->id;
		}

		/**
		 * Prefix key for settings.
		 *
		 * @since 1.5.0
		 *
		 * @param  string $key Field key.
		 * @return string
		 */
		public function get_field_key( $key ) {
			return $key;
		}

		/**
		 * Gets the default values for the form fields.
		 *
		 * @since 1.5.0
		 *
		 * @return array
		 */
		public function get_form_fields_defaults() {
			$form_fields = $this->get_form_fields();

			return array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			$this->settings = WC_OD()->settings()->get_setting( $this->get_option_key() );
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
			return $settings;
		}

		/**
		 * Generate the HTML for a table field.
		 *
		 * @since 1.5.0
		 *
		 * @param string $field_id Field ID.
		 * @param array  $field    Field data.
		 * @return string
		 */
		public function generate_wc_od_table_html( $field_id, $field ) {
			$field['id']    = $field_id;
			$field['value'] = $this->get_option( $field_id );

			ob_start();
			wc_od_field_wrapper( $field );
			return ob_get_clean();
		}

		/**
		 * Validates a table field.
		 *
		 * @since 1.5.0
		 *
		 * @param string $field_id Field ID.
		 * @param mixed  $value    Field value.
		 * @return mixed
		 */
		public function validate_wc_od_table_field( $field_id, $value ) {
			$field          = $this->form_fields[ $field_id ];
			$field['id']    = $field_id;
			$field['value'] = $this->get_option( $field_id );

			$instance = wc_od_get_table_field( $field );

			if ( $instance ) {
				$value = $instance->sanitize_field( $value );
			}

			return $value;
		}
	}
}
