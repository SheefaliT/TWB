<?php
/**
 * Class to add compatibility with the WooCommerce Subscriptions extension.
 *
 * @author     WooThemes
 * @package    WC_OD
 * @since      1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( version_compare( get_option( 'woocommerce_subscriptions_active_version' ), '2.2', '<' ) ) {
	add_action( 'admin_notices', 'wc_od_subscriptions_requirements_notice' );
	return;
}

/**
 * Displays an admin notice when the minimum requirements are not satisfied for the Subscriptions extension.
 *
 * @since 1.4.1
 */
function wc_od_subscriptions_requirements_notice() {
	if ( current_user_can( 'activate_plugins' ) ) :
		/* translators: %s: woocommerce subscription version */
		$message = sprintf( __( '<strong>WooCommerce Order Delivery</strong> requires WooCommerce Subscriptions %s or higher.', 'woocommerce-order-delivery' ), '2.2' );

		printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
	endif;
}

if ( ! class_exists( 'WC_OD_Subscriptions' ) ) {
	/**
	 * Class WC_OD_Subscriptions
	 */
	class WC_OD_Subscriptions {

		/**
		 * Constructor.
		 *
		 * @since 1.3.0
		 */
		public function __construct() {
			$this->includes();

			add_filter( 'wc_od_max_delivery_days', array( $this, 'checkout_max_delivery_days' ) );

			add_action( 'woocommerce_checkout_subscription_created', array( $this, 'setup_delivery_preferences' ) );
			add_action( 'woocommerce_checkout_subscription_created', array( $this, 'process_subscription' ), 20 );
			add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'process_subscription' ) );
			add_action( 'wc_od_subscription_delivery_date_not_found', array( $this, 'delivery_date_not_found' ) );

			add_filter( 'wcs_renewal_order_meta', array( $this, 'copy_order_meta' ), 10, 3 );
			add_filter( 'wcs_resubscribe_order_meta', array( $this, 'copy_order_meta' ), 10, 3 );

			add_filter( 'wcs_new_order_created', array( $this, 'order_created' ), 10, 2 );

			// Priority 5. Before send the emails.
			add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'process_order' ), 5 );
			add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'process_order' ), 5 );
		}

		/**
		 * Includes the necessary files.
		 *
		 * @since 1.3.0
		 */
		public function includes() {
			include_once 'wc-od-subscriptions-functions.php';
			include_once 'class-wc-od-subscriptions-emails.php';
			include_once 'class-wc-od-subscriptions-settings.php';
			include_once 'class-wc-od-subscription-delivery.php';

			if ( is_admin() ) {
				include_once 'class-wc-od-subscription-admin.php';
			}
		}

		/**
		 * Restricts the maximum delivery days value to the minimum subscription period.
		 *
		 * @since 1.3.0
		 *
		 * @param int $max_delivery_days The max delivery days value.
		 * @return int The maximum delivery days.
		 */
		public function checkout_max_delivery_days( $max_delivery_days ) {
			if ( ! wc_od_string_to_bool( WC_OD()->settings()->get_setting( 'subscriptions_limit_to_billing_interval' ) ) ) {
				return $max_delivery_days;
			}

			$period = wc_od_get_min_subscription_period_in_cart();

			if ( $period ) {
				$seconds_in_a_day = 86400;
				$time             = time();
				$diff             = ( strtotime( "+ {$period['interval']} {$period['period']}", $time ) - $time );
				$days             = abs( ( $diff / $seconds_in_a_day ) );

				if ( $days < $max_delivery_days ) {
					$max_delivery_days = $days;
				}
			}

			return $max_delivery_days;
		}

		/**
		 * Setups the subscription's delivery preferences.
		 *
		 * @since 1.5.0
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		public function setup_delivery_preferences( $subscription ) {
			$time_frame = wc_od_get_order_meta( $subscription, '_delivery_time_frame', true );

			if ( $time_frame ) {
				$search_params  = array_intersect_key( $time_frame, array_flip( array( 'time_from', 'time_to' ) ) );
				$delivery_days  = wc_od_get_subscription_delivery_days( $subscription );
				$preferred_days = array();

				foreach ( $delivery_days as $index => $delivery_day ) {
					$time_frame_id = wc_od_search_time_frame( $delivery_day['time_frames'], $search_params );

					$preferred_days[ $index ] = array(
						'enabled'    => $delivery_day['enabled'],
						'time_frame' => ( false === $time_frame_id ? '' : 'time_frame:' . $time_frame_id ),
					);
				}

				// Setup the 'delivery_days' based on the order time frame.
				wc_od_update_order_meta( $subscription, '_delivery_days', $preferred_days, true );
				wc_od_delete_order_meta( $subscription, '_delivery_time_frame', true );
			}
		}

		/**
		 * Processes new subscriptions and their renewals.
		 *
		 * @since 1.5.0
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		public function process_subscription( $subscription ) {
			wc_od_update_subscription_delivery_date( $subscription );
			wc_od_update_subscription_delivery_time_frame( $subscription );
		}

		/**
		 * Adds a note to the subscription when a delivery date for the next order is not found.
		 *
		 * @since 1.3.0
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		public function delivery_date_not_found( $subscription ) {
			wc_od_add_order_note( $subscription, __( 'Delivery date not found for the next order.', 'woocommerce-order-delivery' ) );
		}

		/**
		 * Filters the metadata that will be copied from a subscription to an order.
		 *
		 * @since 1.3.0
		 *
		 * @param array           $meta         The metadata to copy to the order.
		 * @param WC_Order        $order        The order instance.
		 * @param WC_Subscription $subscription The subscription instance.
		 * @return array An array with the order metadata.
		 */
		public function copy_order_meta( $meta, $order, $subscription ) {
			/**
			 * Filters the subscription delivery fields that will be copied as metadata to the order.
			 *
			 * @since 1.3.0
			 * @deprecated 1.5.0 Use the `wc_od_exclude_order_meta` filter instead.
			 *
			 * @param array           $fields       An array with the field keys.
			 * @param WC_Subscription $subscription The subscription instance.
			 */
			$copy_meta = apply_filters( 'wc_od_copy_order_meta', array( 'delivery_date', 'delivery_time_frame' ), $subscription );

			/**
			 * Filters the metadata that will be excluded from the copy of a subscription to an order.
			 *
			 * @since 1.5.0
			 *
			 * @param array           $meta         The meta keys to exclude.
			 * @param WC_Order        $order        The order instance.
			 * @param WC_Subscription $subscription The subscription instance.
			 */
			$exclude_metas = apply_filters( 'wc_od_exclude_order_meta', array( '_delivery_days' ), $order, $subscription );

			if ( empty( $exclude_metas ) ) {
				return $meta;
			}

			$meta_keys = wp_list_pluck( $meta, 'meta_key' );

			// Exclude the meta keys from the copy.
			foreach ( $exclude_metas as $exclude_meta ) {
				// Backward compatibility with the 'wc_od_copy_order_meta' hook.
				if ( ! in_array( ltrim( $exclude_meta, '_' ), $copy_meta, true ) ) {
					$index = array_search( $exclude_meta, $meta_keys, true );

					if ( false !== $index ) {
						unset( $meta[ $index ] );
					}
				}
			}

			return $meta;
		}

		/**
		 * Processes the order created from a subscription.
		 *
		 * @since 1.5.0
		 *
		 * @param WC_Order        $order        The order instance.
		 * @param WC_Subscription $subscription The subscription instance.
		 * @return WC_Order
		 */
		public function order_created( $order, $subscription ) {
			$this->fix_order_shipping_method( $order, $subscription );
			$this->update_order_time_frame( $order );

			return $order;
		}

		/**
		 * Fix for WooCommerce Subscriptions: Set the missing order-item meta 'instance_id'.
		 *
		 * @since 1.5.0
		 *
		 * @param WC_Order        $order        The order instance.
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		public function fix_order_shipping_method( $order, $subscription ) {
			$order_shippings = $order->get_shipping_methods();
			$order_shipping  = reset( $order_shippings );

			if ( $order_shipping && ! $order_shipping['instance_id'] ) {
				$subscription_shippings = $subscription->get_shipping_methods();
				$subscription_shipping  = reset( $subscription_shippings );

				if ( $subscription_shipping && '' !== $subscription_shipping['instance_id'] ) {
					wc_update_order_item_meta( $order_shipping->get_id(), 'instance_id', $subscription_shipping['instance_id'] );
				}
			}
		}

		/**
		 * Replaces the time frame ID by its data.
		 *
		 * @since 1.5.0
		 *
		 * @param WC_Order $order The order instance.
		 */
		public function update_order_time_frame( $order ) {
			$delivery_date = wc_od_get_order_meta( $order, '_delivery_date' );

			if ( ! $delivery_date ) {
				return;
			}

			$time_frame_id = wc_od_get_order_meta( $order, '_delivery_time_frame' );

			if ( $time_frame_id ) {
				$time_frame = wc_od_get_time_frame( $delivery_date, $time_frame_id );

				if ( $time_frame ) {
					wc_od_update_order_meta( $order, '_delivery_time_frame', wc_od_time_frame_to_order( $time_frame ) );
				}
			}
		}

		/**
		 * Processes the order created from a subscription renewal after the payment success.
		 *
		 * @since 1.5.0
		 *
		 * @param int $order_id The order Id.
		 */
		public function process_order( $order_id ) {
			// No renewals in the order.
			if ( ! wcs_order_contains_renewal( $order_id ) ) {
				return;
			}

			$this->validate_order_delivery_date( $order_id );
			$this->validate_order_delivery_time_frame( $order_id );
			$this->add_order_shipping_date( $order_id );
		}

		/**
		 * Validates and updates if necessary the delivery date of the renewal order.
		 *
		 * @since 1.3.0
		 *
		 * @param int $order_id The order Id.
		 */
		public function validate_order_delivery_date( $order_id ) {
			$delivery_date = wc_od_get_order_meta( $order_id, '_delivery_date' );

			// Delivery date not found during the subscription renewal or removed manually by the merchant.
			if ( ! $delivery_date ) {
				return;
			}

			// The 'next_payment' date is not up to date at this point, so we cannot use the 'end_date' parameter here.
			$args = array(
				'order_id'           => $order_id,
				'shipping_method'    => wc_od_get_order_shipping_method( $order_id ),
				'disabled_days_args' => array(
					'type'     => 'delivery',
					'country'  => wc_od_get_order_prop( $order_id, 'shipping_country' ),
					'state'    => wc_od_get_order_prop( $order_id, 'shipping_state' ),
					'order_id' => $order_id,
				),
			);

			// Get the first delivery date since payment.
			$first_delivery_date = wc_od_get_first_delivery_date( $args, 'renewal-order' );

			// No delivery date available.
			if ( ! $first_delivery_date ) {
				wc_od_delete_order_meta( $order_id, '_delivery_date', true );
				return;
			}

			// The minimum date for delivery.
			$args['start_date'] = date( 'Y-m-d', $first_delivery_date );

			// If the current date is not valid, change it for the first delivery date.
			if ( ! wc_od_validate_delivery_date( $delivery_date, $args, 'renewal-order' ) ) {
				wc_od_update_order_meta( $order_id, '_delivery_date', $args['start_date'], true );
			}
		}

		/**
		 * Validates and updates if necessary the delivery time frame of the renewal order.
		 *
		 * @since 1.5.0
		 *
		 * @param int $order_id The order Id.
		 */
		public function validate_order_delivery_time_frame( $order_id ) {
			$delivery_date = wc_od_get_order_meta( $order_id, '_delivery_date' );

			if ( ! $delivery_date ) {
				wc_od_delete_order_meta( $order_id, '_delivery_time_frame', true );
				return;
			}

			$time_frames = wc_od_get_time_frames_for_date(
				$delivery_date,
				array(
					'shipping_method' => wc_od_get_order_shipping_method( $order_id ),
				),
				'renewal-order'
			);

			if ( empty( $time_frames ) ) {
				wc_od_delete_order_meta( $order_id, '_delivery_time_frame', true );
			} elseif ( 1 === count( $time_frames ) ) {
				$time_frame = reset( $time_frames );

				wc_od_update_order_meta( $order_id, '_delivery_time_frame', wc_od_time_frame_to_order( $time_frame ), true );
			} else {
				$time_frame = wc_od_get_order_meta( $order_id, '_delivery_time_frame' );

				// Time frame not available for the current delivery date.
				if ( $time_frame ) {
					$params = array_intersect_key( $time_frame, array_flip( array( 'time_from', 'time_to' ) ) );

					if ( false === wc_od_search_time_frame( $time_frames, $params ) ) {
						wc_od_delete_order_meta( $order_id, '_delivery_time_frame', true );
					}
				}
			}
		}

		/**
		 * Adds the shipping date to the renewal order after the delivery date validation.
		 *
		 * @since 1.4.1
		 *
		 * @param int $order_id The order Id.
		 */
		public function add_order_shipping_date( $order_id ) {
			$shipping_timestamp = wc_od_get_order_last_shipping_date( $order_id, 'renewal-order' );

			if ( $shipping_timestamp ) {
				// Stores the date in the ISO 8601 format.
				$shipping_date = wc_od_localize_date( $shipping_timestamp, 'Y-m-d' );
				wc_od_update_order_meta( $order_id, '_shipping_date', $shipping_date, true );
			}
		}

		/**
		 * Registers the emails of a subscription that will include the delivery information.
		 *
		 * @since 1.3.0
		 * @deprecated 1.4.1
		 *
		 * @param array $email_ids The email IDs.
		 * @return array An array with the email IDs.
		 */
		public function register_subscription_emails( $email_ids ) {
			_deprecated_function( __METHOD__, '1.4.1', 'Moved to WC_OD_Subscriptions_Emails->emails_with_delivery_details()' );

			return $email_ids;
		}

		/**
		 * Additional delivery information for the subscription emails.
		 *
		 * @since 1.3.0
		 * @deprecated 1.4.1
		 *
		 * @param array $args The arguments.
		 */
		public function email_after_delivery_details( $args ) {
			_deprecated_function( __METHOD__, '1.4.1', 'Moved to WC_OD_Subscriptions_Emails->delivery_details()' );
		}

	}
}

return new WC_OD_Subscriptions();
