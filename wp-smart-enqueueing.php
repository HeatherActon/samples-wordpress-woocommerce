<?php
/**
 * Enqueue scripts and styles.
 *
 * We split up scripts and styles and only load exactly what's needed where we need it. Deregister/dequeue stuff we don't use where we don't use it.
 */
function prefixredacted_styles_scripts() {
	wp_enqueue_style( 'default-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'default-style', 'rtl', 'replace' );

	if ( ! is_home() && ! is_category() && ! is_singular( 'post' ) && ! is_search() && ! is_cart() && ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) && ! is_page( 'dosage-dashboard' ) && ! is_account_page() ) {
		wp_enqueue_style( 'prefixredacted-style', get_template_directory_uri() . '/dist/css/bundle.css', array(), _S_VERSION );
	}

	wp_enqueue_script( 'prefixredacted-scripts', get_template_directory_uri() . '/dist/js/bundle.js', array( 'jquery' ), _S_VERSION, true );
	wp_enqueue_script( 'prefixredacted-front-scripts', get_template_directory_uri() . '/dist/js/front-end-only.js', array( 'jquery' ), _S_VERSION, true );

	/* Blog Styles & Scripts */
	if ( is_home() || is_category() || is_singular( 'post' ) || is_search() ) {
		wp_enqueue_style( 'prefixredacted-blog-style', get_template_directory_uri() . '/dist/css/blog.css', array(), _S_VERSION );
		wp_enqueue_script( 'prefixredacted-blog-script', get_template_directory_uri() . '/dist/js/custom/blog.js', array( 'jquery' ), _S_VERSION, true );
	}

	/* Checkout Styles & Scripts */
	if ( is_checkout() ) {
		wp_enqueue_style( 'prefixredacted-checkout-style', get_template_directory_uri() . '/dist/css/checkout.css', array(), _S_VERSION );
		wp_enqueue_script( 'prefixredacted-checkout', get_template_directory_uri() . '/dist/js/custom/checkout.js', array( 'jquery' ), _S_VERSION, true );
		wp_enqueue_script( 'prefixredacted-strip-emojis-from-gifting-field', get_template_directory_uri() . '/dist/js/custom/strip-emojis-from-gifting-field.js', array( 'jquery' ), _S_VERSION, true );
		wp_enqueue_script( 'eqguestcheckout' );
	}

	/* My Account Styles & Scripts */
	if ( is_account_page() ) {
		wp_enqueue_style( 'prefixredacted-my-account-style', get_template_directory_uri() . '/dist/css/my-account.css', array(), _S_VERSION );
		wp_enqueue_script( 'prefixredacted-my-account-scripts', get_template_directory_uri() . '/dist/js/custom/my-account.js', array( 'jquery' ), _S_VERSION, true );
	}

	/* Edit Sub Styles & Scripts */
	if ( is_wc_endpoint_url( 'view-subscription' ) ) {
		wp_enqueue_style( 'prefixredacted-edit-sub-style', get_template_directory_uri() . '/dist/css/edit-sub.css', array(), _S_VERSION );
		wp_register_script( 'prefixredacted-quantity-input', get_template_directory_uri() . '/dist/js/custom/quantity-buttons.js', array( 'jquery' ), _S_VERSION, true );
		wp_register_script( 'edit-sub-add-to-box', get_template_directory_uri() . '/dist/js/custom/edit-sub-add-to-box.js', array( 'jquery' ), _S_VERSION, true );
	}

	/* Dosage Dashboard Styles */
	if ( is_page( 'dosage-dashboard' ) ) {
		wp_enqueue_style( 'prefixredacted-dosage-dashboard-style', get_template_directory_uri() . '/dist/css/dosage-dashboard.css', array(), _S_VERSION );
	}

	wp_enqueue_script( 'prefixredacted-single-product-scripts', get_template_directory_uri() . '/dist/js/custom/single-product.js', array( 'jquery', 'prefixredacted-scripts', 'xoo-wsc-main-js' ), _S_VERSION, true );

	if ( is_page_template( 'page-order-confirmation.php' ) ) {
		wp_enqueue_script( 'prefixredacted-first-purchase-modal', get_template_directory_uri() . '/dist/js/custom/first-purchase-modal.js', array( 'jquery' ), _S_VERSION, true );
	}

	if ( is_page_template( 'page-ambassador-dashboard.php' ) ) {
		wp_enqueue_script( 'Chartjs', get_template_directory_uri() . '/dist/js/custom/Chart.js', array( 'jquery' ), _S_VERSION, true );
		add_filter( 'js_do_concat', '__return_false' );
		add_filter( 'css_do_concat', '__return_false' );
	}

	if ( is_page( 'register-your-prefixredacted-products' ) ) {
		wp_enqueue_script( 'prefixredacted-paying-customer-modal', get_template_directory_uri() . '/dist/js/custom/paying-customer-modal.js', array( 'jquery' ), _S_VERSION, true );
	}

	if ( is_cart() ) {
		wp_enqueue_style( 'prefixredacted-cart-style', get_template_directory_uri() . '/dist/css/cart.css', array(), _S_VERSION );
		wp_enqueue_script( 'prefixredacted-cart-scripts', get_template_directory_uri() . '/dist/js/custom/cart.js', array( 'jquery' ), _S_VERSION, true );
	}

	// These are for Quick View Pro plugin quickviews.
	if ( ( ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) && ! is_cart() && ! is_account_page() ) {
		wp_enqueue_script( 'prefixredacted-quickview-open', get_template_directory_uri() . '/dist/js/custom/quickview-open.js', array( 'jquery' ), _S_VERSION, true );
		wp_enqueue_script( 'prefixredacted-quickview-cookie', get_template_directory_uri() . '/dist/js/custom/quickview-cookie.js', array( 'jquery', 'wc-quick-view-pro' ), _S_VERSION, true );
	}

	wp_enqueue_script( 'quantity-input', get_template_directory_uri() . '/dist/js/custom/quantity-buttons.js', array( 'jquery' ), _S_VERSION, true );

	wp_register_script( 'prefixredacted-block-track-products', get_template_directory_uri() . '/template-parts/blocks/tracks/products/block-track_products.js', array( 'jquery' ), _S_VERSION, true );

	wp_register_script( 'prefixredacted-block-icons', get_template_directory_uri() . '/template-parts/blocks/icons/icons.js', array( 'jquery', 'prefixredacted-scripts', 'prefixredacted-front-scripts' ), _S_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'prefixredacted_styles_scripts' );

function prefixredacted_admin_scripts() {
	wp_enqueue_script( 'prefixredacted-scripts', get_template_directory_uri() . '/dist/js/bundle.js', array( 'jquery' ), _S_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'prefixredacted_admin_scripts' );

function eq_dequeue_plugin_style() {

	// remove sitewide
	wp_dequeue_style( 'ywcmap-frontend' );
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wc-blocks-vendors-style' );
	wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_style( 'aftax-frontc' );
	wp_dequeue_style( 'affwp-forms' );
	wp_dequeue_style( 'wcsatt-css' );
	wp_dequeue_style( 'lightslider' );
	//wp_dequeue_style( 'wc-square-cart-checkout-block' );
	wp_dequeue_style( 'metorik-css' );

	// Woo subscriptions blocks integration
	wp_dequeue_style( 'wc-blocks-integration' );

	// Woo composite products stuff we do not need
	wp_dequeue_style( 'wc-cp-checkout-blocks' );

	// remove on /checkout/
	if ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
		wp_dequeue_style( 'automatewoo-referrals' );
		wp_dequeue_style( 'xoo-wsc-fonts' );
	}

	if ( ! is_checkout() ) {
		wp_dequeue_style( 'wc-square' );
	}
}
add_action( 'wp_enqueue_scripts', 'eq_dequeue_plugin_style', 9999 );

function eq_dequeue_plugin_scripts() {

	// remove sitewide
	wp_deregister_script( 'aftax-frontj' );
	wp_dequeue_script( 'aftax-frontj' );
	wp_dequeue_script( 'lightslider' );

	// remove everywhere but on gift card PDP
	if ( ! is_single( 'prefixredacted-gift-card' ) ) {
		wp_deregister_script( 'pw-gift-cards' );
		wp_dequeue_script( 'pw-gift-cards' );
		wp_deregister_script( 'pikaday' );
		wp_dequeue_script( 'pikaday' );
		wp_deregister_script( 'moment-with-locales' );
		wp_dequeue_script( 'moment-with-locales' );
	}

	// remove on /checkout/
	if ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
		wp_deregister_script( 'prefixredacted-ajax-add-to-cart' );
		wp_dequeue_script( 'prefixredacted-ajax-add-to-cart' );
		wp_deregister_script( 'xoo-wsc-main-js' );
		wp_dequeue_script( 'xoo-wsc-main-js' );
	}

	// remove everywhere but checkout
	if ( ! is_checkout() && ! is_account_page() ) {
		wp_deregister_script( 'wc-square' );
		wp_dequeue_script( 'wc-square' );
		wp_deregister_script( 'wc-square-payment-form' );
		wp_dequeue_script( 'wc-square-payment-form' );
	}
	// remove on /my-account/*
	if ( is_account_page() ) {
		wp_deregister_script( 'prefixredacted-ajax-add-to-cart' );
		wp_dequeue_script( 'prefixredacted-ajax-add-to-cart' );
	}

}
add_action( 'wp_print_scripts', 'eq_dequeue_plugin_scripts', 999 );

// Opt in to loading styles only when related block is on a page https://make.wordpress.org/core/2021/07/01/block-styles-loading-enhancements-in-wordpress-5-8/
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
