<?php
/**
 * Notice - Updating
 *
 * @since 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><strong><?php _e( 'WooCommerce Order Delivery', 'woocommerce-order-delivery' ); ?></strong> &#8211; <?php _e( 'Your database is being updated in the background.', 'woocommerce-order-delivery' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_wc_od', 'true', wc_od_get_settings_url() ) ); ?>"><?php _e( 'Taking a while? Click here to run it now.', 'woocommerce-order-delivery' ); ?></a></p>
</div>
