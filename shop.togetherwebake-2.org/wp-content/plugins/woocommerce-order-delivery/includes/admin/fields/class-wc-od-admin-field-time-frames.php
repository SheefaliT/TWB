<?php
/**
 * Field: Time Frames.
 *
 * @author     WooThemes
 * @package    WC_OD/Admin/Fields
 * @since      1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WC_OD_Admin_Field_Time_Frames', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-field-table.php';
}

/**
 * WC_OD_Admin_Field_Time_Frames Class.
 */
class WC_OD_Admin_Field_Time_Frames extends WC_OD_Admin_Field_Table {

	/**
	 * Define if the table is sortable or not.
	 *
	 * @var bool
	 */
	protected $sortable = true;

	/**
	 * The day ID.
	 *
	 * @var mixed A weekday index (0-6). False otherwise.
	 */
	protected $day_id = false;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$this->init_day_id();

		$columns = array(
			'title'       => array(
				'label' => __( 'Title', 'woocommerce-order-delivery' ),
			),
			'description' => array(
				'label' => __( 'Description', 'woocommerce-order-delivery' ),
			),
		);

		parent::__construct(
			$field,
			$columns,
			( isset( $field['value'] ) ? $field['value'] : array() )
		);
	}

	/**
	 * Initialize the day ID.
	 *
	 * @since 1.5.0
	 */
	public function init_day_id() {
		if ( isset( $_GET['day_id'] ) ) {
			$day_id = (int) wc_clean( wp_unslash( $_GET['day_id'] ) ); // WPCS: CSRF ok, sanitization ok.

			$this->day_id = ( $day_id >= 0 && $day_id <= 6 ? $day_id : false );
		}
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
		$params = array(
			'frame_id' => $row,
		);

		if ( false !== $this->day_id ) {
			$params['day_id'] = $this->day_id;
		}

		return wc_od_get_settings_url( 'time_frame', $params );
	}

	/**
	 * Outputs the 'add time frame' button.
	 *
	 * @since 1.5.0
	 */
	public function add_button() {
		printf(
			'<a class="button" href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( 'new' ) ),
			esc_html__( 'Add time frame', 'woocommerce-order-delivery' )
		);
	}

	/**
	 * Outputs the action links for a row.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row The row index.
	 */
	public function row_actions( $row ) {
		$actions = array(
			'settings' => array(
				'label' => _x( 'Edit', 'time frame row action', 'woocommerce-order-delivery' ),
				'url'   => $this->get_row_url( $row ),
			),
			'delete'   => array(
				'label' => _x( 'Delete', 'time frame row action', 'woocommerce-order-delivery' ),
				'url'   => '#',
			),
		);

		$action_strings = array();

		foreach ( $actions as $key => $action ) {
			$action_strings[] = sprintf(
				'<a class="wc-od-time-frame-%1$s" href="%2$s">%3$s</a>',
				esc_attr( $key ),
				esc_url( $action['url'] ),
				esc_html( $action['label'] )
			);
		}

		echo '<div class="row-actions">';
		echo join( ' | ', $action_strings ); // WPCS: XSS ok.
		echo '</div>';
	}

	/**
	 * Outputs the column 'title'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_title( $row, $data ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( $data['title'] )
		);

		$this->row_actions( $row );
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
		echo wp_kses_post( '<strong>' . wc_od_time_frame_to_string( $data ) . '</strong>' );

		if ( ! empty( $data['shipping_methods'] ) ) {
			printf(
				'<p><strong>%1$s</strong> %2$s</p>',
				esc_html__( 'Shipping methods:', 'woocommerce-order-delivery' ),
				esc_html( join( ' | ', array_map( 'wc_od_shipping_method_choice_label', $data['shipping_methods'] ) ) )
			);
		}
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 1.5.0
	 */
	public function output_footer() {
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
					<?php $this->add_button(); ?>
				</td>
			</tr>
		</tfoot>
		<?php
	}
}
