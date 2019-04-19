<?php
/**
 * Field: Delivery Days.
 *
 * @author     WooThemes
 * @package    WC_OD/Admin/Fields
 * @since      1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WC_OD_Admin_Field_Delivery_days', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-field-table.php';
}

/**
 * WC_OD_Admin_Field_Delivery_days Class.
 */
class WC_OD_Admin_Field_Delivery_Days extends WC_OD_Admin_Field_Table {

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$columns = array(
			'name'        => array(
				'label' => __( 'Delivery day', 'woocommerce-order-delivery' ),
			),
			'status'      => array(
				'label' => __( 'Enabled', 'woocommerce-order-delivery' ),
				'width' => '1%',
			),
			'description' => array(
				'label' => __( 'Description', 'woocommerce-order-delivery' ),
			),
			'action'      => array(
				'label' => '',
				'width' => '1%',
			),
		);

		parent::__construct(
			$field,
			$columns,
			WC_OD()->settings()->get_setting( $field['id'] )
		);
	}

	/**
	 * Gets the row URL.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row The row index.
	 * @return string
	 */
	public function get_row_url( $row ) {
		return wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $row ) );
	}

	/**
	 * Outputs the column 'name'.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row The row index.
	 */
	public function output_column_name( $row ) {
		$week_days = wc_od_get_week_days();

		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( $week_days[ $row ] )
		);
	}

	/**
	 * Outputs the column 'status'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_status( $row, $data ) {
		$enabled = wc_od_string_to_bool( $data['enabled'] );

		echo '<label class="wc-od-input-toggle">';

		printf(
			'<input type="checkbox" name="%1$s" %2$s />',
			esc_attr( $this->id . "[{$row}][enabled]" ),
			checked( $enabled, true, false )
		);

		$class  = ( version_compare( WC()->version, '3.0', '<' ) ? 'status-' : 'woocommerce-input-toggle woocommerce-input-toggle--' );
		$class .= ( $enabled ? 'enabled' : 'disabled' );

		printf(
			'<span class="%1$s">%2$s</span>',
			esc_attr( $class ),
			( $enabled ? esc_html__( 'Yes', 'woocommerce' ) : esc_html__( 'No', 'woocommerce' ) )
		);

		echo '</label>';
	}

	/**
	 * Outputs the column 'description'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_description( $row, $data ) {
		if ( ! empty( $data['time_frames'] ) ) {
			$time_frames = array();

			foreach ( $data['time_frames'] as $key => $time_frame ) {
				$params = array(
					'day_id'   => $row,
					'frame_id' => $key,
				);

				$time_frames[] = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( wc_od_get_settings_url( 'time_frame', $params ) ),
					esc_html( $time_frame['title'] )
				);
			}

			printf(
				'<p><strong>%1$s</strong> %2$s</p>',
				esc_html__( 'Time frames:', 'woocommerce-order-delivery' ),
				wp_kses_post( join( ' | ', $time_frames ) )
			);
		} elseif ( ! empty( $data['shipping_methods'] ) ) {
			printf(
				'<p><strong>%1$s</strong> %2$s</p>',
				esc_html__( 'Shipping methods:', 'woocommerce-order-delivery' ),
				esc_html( join( ' | ', array_map( 'wc_od_shipping_method_choice_label', $data['shipping_methods'] ) ) )
			);
		} else {
			echo '-';
		}
	}

	/**
	 * Outputs the column 'action'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_action( $row, $data ) {
		printf(
			'<a class="button alignright" href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( wc_od_string_to_bool( $data['enabled'] ) ? __( 'Manage', 'woocommerce' ) : __( 'Set Up', 'woocommerce' ) )
		); // WPCS: XSS ok.
	}
}
