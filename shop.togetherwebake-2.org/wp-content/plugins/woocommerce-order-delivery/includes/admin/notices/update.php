<?php
/**
 * Notice - Update
 *
 * @since 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><strong><?php _e( 'WooCommerce Order Delivery', 'woocommerce-order-delivery' ); ?></strong> &#8211; <?php _e( 'We need to update your store database to the latest version.', 'woocommerce-order-delivery' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_wc_od', 'true', wc_od_get_settings_url() ) ); ?>" class="wc-update-now button-primary"><?php _e( 'Run the updater', 'woocommerce-order-delivery' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.wc-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'woocommerce-order-delivery' ) ); ?>' ); // jshint ignore:line
	});
</script>
