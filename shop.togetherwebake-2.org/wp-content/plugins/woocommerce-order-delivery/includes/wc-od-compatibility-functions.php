<?php
/**
 * Backward compatibility functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets a property from the order.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $prop      Name of prop to get.
 * @return mixed|null The prop value. Null on failure.
 */
function wc_od_get_order_prop( $the_order, $prop ) {
	$order = ( $the_order instanceof WC_Order ? $the_order : wc_get_order( $the_order ) );

	if ( ! $order ) {
		return null;
	}

	$callable = array( $order, "get_{$prop}" );

	// Property renamed in WC 3.0+
	if ( 'data_created' === $prop ) {
		$prop = 'order_date';
	}

	return ( is_callable( $callable ) ? call_user_func( $callable ) : $order->$prop );
}

/**
 * Gets an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       Optional. The meta key to retrieve.
 * @param bool   $single    Optional. Whether to return a single value. Default true.
 * @return mixed The meta data value.
 */
function wc_od_get_order_meta( $the_order, $key = '', $single = true ) {
	$meta = '';

	if ( $the_order instanceof WC_Order ) {
		$order_id = ( version_compare( WC()->version, '3.0', '<' ) ? $the_order->id : $the_order->get_id() );
	} else {
		$order_id = intval( $the_order );
	}

	if ( $order_id ) {
		$meta = get_post_meta( $order_id, $key, $single );
	}

	return $meta;
}

/**
 * Updates an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       The meta key to update.
 * @param mixed  $value     The meta value.
 * @param bool   $save      Optional. True to save the meta immediately. Default false.
 * @return bool True on successful update, false on failure.
 */
function wc_od_update_order_meta( $the_order, $key, $value, $save = false ) {
	$updated = false;

	if ( version_compare( WC()->version, '3.0', '<' ) ) {
		$order_id = ( $the_order instanceof WC_Order ? $the_order->id : intval( $the_order ) );
		$updated = (bool) update_post_meta( $order_id, $key, $value );
	} else {
		$order = ( $the_order instanceof WC_Order ? $the_order : wc_get_order( $the_order ) );

		if ( $order ) {
			$old_value = $order->get_meta( $key );

			if ( $old_value !== $value ) {
				$order->update_meta_data( $key, $value );
				$updated = true;

				// Save the meta immediately.
				if ( $save ) {
					$order->save_meta_data();
				}
			}
		}
	}

	return $updated;
}

/**
 * Deletes an order meta data by key.
 *
 * @since 1.1.0
 *
 * @param mixed  $the_order Post object or post ID of the order.
 * @param string $key       The meta key to delete.
 * @param bool   $save      Optional. True to delete the meta immediately. Default false.
 * @return bool True on successful delete, false on failure.
 */
function wc_od_delete_order_meta( $the_order, $key, $save = false ) {
	$deleted = false;

	if ( version_compare( WC()->version, '3.0', '<' ) ) {
		$order_id = ( $the_order instanceof WC_Order ? $the_order->id : intval( $the_order ) );
		$deleted = delete_post_meta( $order_id, $key );
	} else {
		$order = ( $the_order instanceof WC_Order ? $the_order : wc_get_order( $the_order ) );

		if ( $order ) {
			$order->delete_meta_data( $key );
			$deleted = true;

			if ( $save ) {
				$order->save_meta_data();
			}
		}
	}

	return $deleted;
}

/**
 * Gets the logger instance.
 *
 * @since 1.4.0
 *
 * @return WC_Logger
 */
function wc_od_get_logger() {
	return ( function_exists( 'wc_get_logger' ) ? wc_get_logger() : new WC_Logger() );
}

/**
 * Logs a message.
 *
 * @since 1.4.0
 *
 * @param string         $message The message to log.
 * @param string         $level   The level
 * @param string         $handle  Optional. The log handlers.
 * @param WC_Logger|null $logger  Optional. The logger instance.
 */
function wc_od_log( $message, $level = 'notice', $handle = 'wc_od', $logger = null ) {
	if ( ! $logger ) {
		$logger = wc_od_get_logger();
	}

	if ( method_exists( $logger, $level ) ) {
		call_user_func( array( $logger, $level ), $message, array( 'source' => $handle ) );
	} else {
		$logger->add( $handle, $message );
	}
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.5.0
 *
 * @param string $string String to convert.
 * @return bool
 */
function wc_od_string_to_bool( $string ) {
	// TODO: Use 'wc_string_to_bool' function when the minimum requirements are WC 3.0+
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.5.0
 *
 * @param bool $bool String to convert.
 * @return string
 */
function wc_od_bool_to_string( $bool ) {
	// TODO: Use 'wc_bool_to_string' function when the minimum requirements are WC 3.0+
	if ( ! is_bool( $bool ) ) {
		$bool = wc_od_string_to_bool( $bool );
	}

	return ( true === $bool ? 'yes' : 'no' );
}

/**
 * Get an array of checkout fields.
 *
 * @since 1.5.0
 *
 * @param string $fieldset Optional. The fieldset to get.
 * @return array
 */
function wc_od_get_checkout_fields( $fieldset = '' ) {
	$checkout = WC()->checkout();

	// Added in WC 3.0.
	if ( method_exists( $checkout, 'get_checkout_fields' ) ) {
		$fields = $checkout->get_checkout_fields( $fieldset );
	} else {
		$fields = ( $fieldset ? $checkout->checkout_fields[ $fieldset ] : $checkout->checkout_fields );
	}

	return $fields;
}
