<?php
/**
 * Shipping methods functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the shipping methods choices to use in a select field.
 *
 * @since 1.5.0
 *
 * @return array An array with the choices.
 */
function wc_od_get_shipping_methods_choices() {
	$choices = array();

	$zones        = WC_Shipping_Zones::get_zones();
	$default_zone = WC_Shipping_Zones::get_zone( 0 );

	// Add the default shipping zone.
	if ( $default_zone ) {
		$zones[0] = array(
			'zone_id'          => 0,
			'shipping_methods' => $default_zone->get_shipping_methods(),
		);
	}

	foreach ( $zones as $zone ) {
		// Skip empty zones.
		if ( empty( $zone['shipping_methods'] ) ) {
			continue;
		}

		$zone_id = "zone:{$zone['zone_id']}";

		// Add the shipping zone.
		$choices[ $zone_id ] = wc_od_shipping_method_choice_label( $zone_id );

		// Add the shipping methods of the current zone.
		foreach ( $zone['shipping_methods'] as $method_id => $method ) {
			if ( ! wc_od_string_to_bool( $method->enabled ) ) {
				continue;
			}

			$value = $method->id . ':' . $method->instance_id;
			$label = '&nbsp;&nbsp; ' . wc_od_shipping_method_choice_label( $value );

			$choices[ $value ] = $label;
		}
	}

	return $choices;
}

/**
 * Gets the label for shipping method choice.
 *
 * @since 1.5.0
 *
 * @param string $choice_id The choice ID.
 * @return string
 */
function wc_od_shipping_method_choice_label( $choice_id ) {
	$parts = preg_split( '/:/', $choice_id );

	if ( 2 !== count( $parts ) ) {
		return '';
	}

	$title = '';

	// Sanitize zone_id/instance_id.
	$parts[1] = (int) $parts[1];

	if ( 'zone' === $parts[0] ) {
		$zone  = WC_Shipping_Zones::get_zone( $parts[1] );
		$title = ': ' . __( 'All shipping methods', 'woocommerce-order-delivery' );
	} else {
		$zone   = WC_Shipping_Zones::get_zone_by( 'instance_id', $parts[1] );
		$method = WC_Shipping_Zones::get_shipping_method( $parts[1] );

		if ( $method ) {
			$title = ' — ' . ( $method->title ?: $method->method_title );
		}
	}

	$label = '';

	if ( $zone && $title ) {
		// Name for the default zone.
		if ( 0 === $zone->get_id() ) {
			$zone->set_zone_name( _x( 'Other locations', 'label for the default shipping zone', 'woocommerce-order-delivery' ) );
		}

		$label = $zone->get_zone_name() . $title;
	}

	/**
	 * Filters the label used to display the shipping method choice.
	 *
	 * @since 1.5.0
	 *
	 * @param string $label     The choice label.
	 * @param string $choice_id The choice ID.
	 */
	return apply_filters( 'wc_od_shipping_method_choice_label', $label, $choice_id );
}

/**
 * Gets all the shipping methods replacing the shipping zones.
 *
 * @since 1.5.0
 *
 * @param array $shipping_methods The shipping methods to process.
 * @return mixed
 */
function wc_od_expand_shipping_methods( $shipping_methods ) {
	$parsed_methods = array();

	foreach ( $shipping_methods as $index => $shipping_method ) {
		// Replace the zone by all its shipping methods.
		if ( 0 === strpos( $shipping_method, 'zone' ) ) {
			$zone_id = (int) str_replace( 'zone:', '', $shipping_method );
			$zone    = WC_Shipping_Zones::get_zone( $zone_id );

			if ( $zone ) {
				$zone_methods = $zone->get_shipping_methods( true );

				foreach ( $zone_methods as $method ) {
					$parsed_methods[] = $method->id . ':' . $method->instance_id;
				}
			}
		} else {
			$parsed_methods[] = $shipping_method;
		}
	}

	return $parsed_methods;
}

/**
 * Gets the available shipping methods for the specified delivery day.
 *
 * An empty array means that all the shipping methods are available.
 *
 * @since 1.5.0
 *
 * @param array|int $delivery_day An array with the delivery day data or the weekday index.
 * @param array     $args         Optional. The additional arguments.
 * @param string    $context      Optional. The context.
 * @return array
 */
function wc_od_get_shipping_methods_for_delivery_day( $delivery_day, $args = array(), $context = '' ) {
	$defaults = array(
		'time_frame' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$shipping_methods = array();
	$delivery_day     = wc_od_get_delivery_day( $delivery_day );

	// Use the shipping methods of the time frames if exists.
	if ( isset( $delivery_day['time_frames'] ) && ! empty( $delivery_day['time_frames'] ) ) {
		$time_frame_id = wc_od_parse_time_frame_id( $args['time_frame'] );

		if ( false !== $time_frame_id ) {
			$shipping_methods = ( isset( $delivery_day['time_frames'][ $time_frame_id ] ) ? $delivery_day['time_frames'][ $time_frame_id ]['shipping_methods'] : array() );
		} else {
			// Merge the shipping methods of all the time frames.
			foreach ( $delivery_day['time_frames'] as $time_frame ) {
				// All shipping methods are available.
				if ( empty( $time_frame['shipping_methods'] ) ) {
					$shipping_methods = array();
					break;
				} else {
					$shipping_methods = array_merge( $shipping_methods, $time_frame['shipping_methods'] );
				}
			}

			// Remove duplicated.
			$shipping_methods = array_unique( $shipping_methods );
		}
	} elseif ( isset( $delivery_day['shipping_methods'] ) ) {
		$shipping_methods = $delivery_day['shipping_methods'];
	}

	/**
	 * Filters the shipping methods for the specified delivery day.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $time_frames  The time frames.
	 * @param array  $delivery_day The delivery day data.
	 * @param array  $args         The additional arguments.
	 * @param string $context      The context.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_for_delivery_day', wc_od_expand_shipping_methods( $shipping_methods ), $delivery_day, $args, $context );
}

/**
 * Gets the shipping methods for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return array
 */
function wc_od_get_shipping_methods_for_date( $date, $args = array(), $context = '' ) {
	$shipping_methods = array();
	$timestamp        = wc_od_get_timestamp( $date );

	if ( $timestamp ) {
		$shipping_methods = wc_od_get_shipping_methods_for_delivery_day( date( 'w', $timestamp ), $args, $context );
	}

	/**
	 * Filters the shipping methods for the specified date.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $shipping_methods The shipping methods.
	 * @param int    $timestamp        The timestamp representing the date.
	 * @param array  $args             The additional arguments.
	 * @param string $context          The context.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_for_date', $shipping_methods, $timestamp, $args, $context );
}

/**
 * Gets the first shipping method of the order.
 *
 * Returns the string 'method_id:instance_id'.
 *
 * @since 1.5.0
 *
 * @param mixed $the_order Post object or post ID of the order.
 * @return string|false The shipping method. False on failure.
 */
function wc_od_get_order_shipping_method( $the_order ) {
	$order = ( $the_order instanceof WC_Order ? $the_order : wc_get_order( $the_order ) );

	if ( ! $order ) {
		return false;
	}

	$item_shippings = $order->get_shipping_methods();
	$item_shipping  = reset( $item_shippings );

	if ( ! $item_shipping || '' === $item_shipping['method_id'] ) {
		return false;
	}

	$shipping_method = $item_shipping['method_id'];
	$instance_id     = ( isset( $item_shipping['instance_id'] ) ? $item_shipping['instance_id'] : null );

	if ( $instance_id || 0 === $instance_id ) {
		$shipping_method .= ":{$instance_id}";
	} else {
		// Subscriptions contain the 'instance_id' into the 'method_id' parameter.
		$parts = preg_split( '/:/', $item_shipping['method_id'] );

		if ( empty( $parts ) || 2 !== count( $parts ) ) {
			return false;
		}
	}

	return $shipping_method;
}
