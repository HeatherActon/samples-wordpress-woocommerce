<?php

namespace Sitename\RestrictedProducts;

class Scripts {

	public function __construct() {

	}

	public function build() {

		wp_enqueue_script( 'remove-restricted-products', SITEPREFIX_RESTRICTED_PRODUCTS_URL . 'assets/js/remove-restricted-products.js', array( 'jquery' ), filemtime( SITEPREFIX_RESTRICTED_PRODUCTS_PATH . 'assets/js/remove-restricted-products.js' ), true );

		wp_localize_script( 'remove-restricted-products', 'removerestrictedproducts', array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'customer_id' => absint( wp_get_current_user()->ID ),
			'security'    => wp_create_nonce( 'remove-restricted-product-' . absint( wp_get_current_user()->ID ) ),
		) );

	}
}