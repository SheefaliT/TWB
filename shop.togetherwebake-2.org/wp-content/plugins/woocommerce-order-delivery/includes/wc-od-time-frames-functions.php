<?php
/**
 * Time frames functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the string representation of the time frame.
 *
 * @since 1.5.0
 *
 * @param array  $time_frame The time frame data.
 * @param string $context    Optional. The context.
 * @return string
 */
function wc_od_time_frame_to_string( $time_frame, $context = '' ) {
	$string = str_replace(
		array(
			'[title]',
			'[time_from]',
			'[time_to]',
		),
		array(
			( empty( $time_frame['title'] ) ? '' : $time_frame['title'] ),
			( empty( $time_frame['time_from'] ) ? '' : wc_od_localize_time( $time_frame['time_from'] ) ),
			( empty( $time_frame['time_to'] ) ? '' : wc_od_localize_time( $time_frame['time_to'] ) ),
		),
		_x( '[time_from] &ndash; [time_to]', 'Time Frame. Allowed tags: [time_from], [time_to], [title]', 'woocommerce-order-delivery' )
	);

	/**
	 * Filters the string representation of the time frame.
	 *
	 * @since 1.5.0
	 *
	 * @param string $string     The time frame string.
	 * @param array  $time_frame The time frame data.
	 * @param string $context    The context.
	 */
	return apply_filters( 'wc_od_time_frame_to_string', $string, $time_frame, $context );
}

/**
 * Gets the time frame value to store with the order metadata.
 *
 * @since 1.5.0
 *
 * @param array $time_frame The time frame data.
 * @return array An array with the time frame data.
 */
function wc_od_time_frame_to_order( $time_frame ) {
	$order_time_frame = array_intersect_key(
		$time_frame,
		array_flip(
			array(
				'time_from',
				'time_to',
			)
		)
	);

	/**
	 * Filters the time frame value to store with the order metadata.
	 *
	 * @since 1.5.0
	 *
	 * @param array $order_time_frame The order time frame data.
	 * @param array $time_frame       The time frame data.
	 */
	return apply_filters( 'wc_od_time_frame_to_order', $order_time_frame, $time_frame );
}

/**
 * Parse the time frame ID.
 *
 * @since 1.5.0
 *
 * @param mixed $time_frame The time frame data or just its ID.
 * @return int|false The time frame ID. False otherwise.
 */
function wc_od_parse_time_frame_id( $time_frame ) {
	$id = ( is_array( $time_frame ) && isset( $time_frame['id'] ) ? $time_frame['id'] : $time_frame );

	// Remove the prefix if it exists.
	$id = str_replace( 'time_frame:', '', $id );

	if ( ! is_numeric( $id ) ) {
		return false;
	}

	return intval( $id );
}

/**
 * Gets a time frame data by ID for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date The date or timestamp.
 * @param mixed      $id   The time frame Id.
 * @return mixed An array with the time frame data. False if not found.
 */
function wc_od_get_time_frame( $date, $id ) {
	$id          = wc_od_parse_time_frame_id( $id );
	$time_frames = wc_od_get_time_frames_for_date( $date );

	return ( isset( $time_frames[ $id ] ) ? $time_frames[ $id ] : false );
}

/**
 * Gets the key of the first time frame that matches with the specified parameters.
 *
 * @since 1.5.0
 *
 * @param array $time_frames The time frames.
 * @param array $params      The parameters to look for.
 * @return mixed The time frame key. False otherwise.
 */
function wc_od_search_time_frame( $time_frames, $params = array() ) {
	$found     = false;
	$args_size = count( $params );

	foreach ( $time_frames as $key => $time_frame ) {
		$intersect = array_intersect_assoc( $time_frame, $params );

		if ( count( $intersect ) === $args_size ) {
			$found = $key;
			break;
		}
	}

	return $found;
}

/**
 * Gets the time frames for the specified delivery day.
 *
 * @since 1.5.0
 *
 * @param array|int $delivery_day An array with the delivery day data or the weekday index.
 * @param array     $args         Optional. The additional arguments.
 * @param string    $context      Optional. The context.
 * @return array An array with the time frames.
 */
function wc_od_get_time_frames_for_delivery_day( $delivery_day, $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_method' => false,
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used to calculate the time frames of the delivery day.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $args         The arguments.
	 * @param array  $delivery_day The delivery day data.
	 * @param string $context      The context.
	 */
	$args = apply_filters( 'wc_od_get_time_frames_for_delivery_day_args', wp_parse_args( $args, $defaults ), $delivery_day, $context );

	$delivery_day = wc_od_get_delivery_day( $delivery_day );
	$time_frames  = ( isset( $delivery_day['time_frames'] ) ? $delivery_day['time_frames'] : array() );

	// Filter by shipping method.
	if ( $args['shipping_method'] ) {
		$filtered_time_frames = array();

		foreach ( $time_frames as $index => $time_frame ) {
			$shipping_methods = wc_od_expand_shipping_methods( $time_frame['shipping_methods'] );

			if ( empty( $shipping_methods ) || in_array( $args['shipping_method'], $shipping_methods, true ) ) {
				$filtered_time_frames[ $index ] = $time_frame;
			}
		}

		$time_frames = $filtered_time_frames;
	}

	/**
	 * Filters the time frames for the specified delivery day.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $time_frames  The time frames.
	 * @param array  $delivery_day The delivery day data.
	 * @param array  $args         The additional arguments.
	 * @param string $context      The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_for_delivery_day', $time_frames, $delivery_day, $args, $context );
}

/**
 * Gets the time frames for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return array An array with the time frames.
 */
function wc_od_get_time_frames_for_date( $date, $args = array(), $context = '' ) {
	$time_frames = array();
	$timestamp   = wc_od_get_timestamp( $date );

	if ( $timestamp ) {
		$time_frames = wc_od_get_time_frames_for_delivery_day( date( 'w', $timestamp ), $args, $context );
	}

	/**
	 * Filters the time frames for the specified date.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $time_frames The time frames.
	 * @param int    $timestamp   The timestamp representing the date.
	 * @param array  $args        The additional arguments.
	 * @param string $context     The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_for_date', $time_frames, $timestamp, $args, $context );
}

/**
 * Gets the time frames choices to use in a select field.
 *
 * @since 1.5.0
 *
 * @param array  $time_frames The time frames.
 * @param string $context     Optional. The context.
 * @return array An array with the choices.
 */
function wc_od_get_time_frames_choices( $time_frames, $context = '' ) {
	$choices = array();

	foreach ( $time_frames as $key => $time_frame ) {
		$choices[ 'time_frame:' . $key ] = esc_html( wc_od_time_frame_to_string( $time_frame, $context ) );
	}

	if ( 1 < count( $choices ) ) {
		// Don't use array_merge to avoid reindexing.
		$choices = array(
			'' => __( 'Choose a time frame', 'woocommerce-order-delivery' ),
		) + $choices;
	}

	/**
	 * Filters the time frames choices to use in a select field.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $choices     The choices.
	 * @param array  $time_frames The time frames.
	 * @param string $context     The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_choices', $choices, $time_frames, $context );
}

/**
 * Gets the time frames choices to use in a select field for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return array An array with the choices.
 */
function wc_od_get_time_frames_choices_for_date( $date, $args = array(), $context = '' ) {
	$time_frames = wc_od_get_time_frames_for_date( $date, $args, $context );

	return wc_od_get_time_frames_choices( $time_frames, $context );
}
