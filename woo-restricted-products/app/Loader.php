<?php
namespace Sitename\RestrictedProducts;

class Loader {

	public function __construct() {

		// Load this first to localize our AJAX script.
		add_action( 'wp_enqueue_scripts', array( new Scripts(), 'build' ) );

		// Adds checkbox to product edit screen and saves the data.
		$admin = new Admin();
		add_filter( 'woocommerce_product_data_tabs', array( $admin, 'add_product_data_tabs' ), 10, 1 );
		add_action( 'admin_head', array( $admin, 'change_product_tab_icons' ), 999 );
		add_action( 'woocommerce_product_data_panels', array( $admin, 'edit_product' ), 10 );
		add_action( 'woocommerce_process_product_meta', array( $admin, 'save_product' ), 10, 1 );
		add_action( 'woocommerce_process_product_meta', array( $admin, 'save_option' ), 10, 1 );

		//$front = new Front();

		$restrict_cbn = new Restrictions\CBN();
		add_action( 'woocommerce_after_checkout_validation', array( $restrict_cbn, 'maybe_block_purchase' ), 10, 2 );
		add_action( 'wp_footer', array( $restrict_cbn, 'purchase_blocked_modal' ), 99 );
		add_action( 'wp_ajax_remove_restricted_products_from_cart', array( $restrict_cbn, 'remove_restricted_products_from_cart' ) );

		$restrict_repeat_customer = new Restrictions\RepeatCustomer();
		add_action( 'wp', array( $restrict_repeat_customer, 'hide_add_to_cart_button_for_repeat_customers_product' ) );
		add_action( 'wc_quick_view_pro_quick_view_product_details', array( $restrict_repeat_customer, 'hide_add_to_cart_button_for_repeat_customers_quickview' ) );
		add_filter( 'equilibria_add_to_cart_validation', array( $restrict_repeat_customer, 'disallow_repeat_customer_siteprefix_add_to_cart' ), 99, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $restrict_repeat_customer, 'disallow_repeat_customer_woo_add_to_cart' ), 10, 2 );
		add_action( 'wp_login', array( $restrict_repeat_customer, 'remove_restricted_product_repeat_customer_login' ), 999, 2 );
		// The below may be overkill, so let's keep it inactive unless we find we need it.
		// add_filter( 'woocommerce_update_cart_action_cart_updated', array( $restrict_repeat_customer, 'remove_restricted_product_repeat_customer_cart_refresh' ), 99 );

	}
}
