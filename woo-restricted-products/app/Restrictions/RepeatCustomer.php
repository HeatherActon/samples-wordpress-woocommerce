<?php
namespace Sitename\RestrictedProducts\Restrictions;

class RepeatCustomer {

	public function __construct() {

		$this->restriction   = \Sitename\RestrictedProducts\Helpers::get_restriction( 'repeat_customer' );
		$this->blocked_ids   = \Sitename\RestrictedProducts\Helpers::get_blocked_ids( 'repeat_customer' );
		$this->error_message = 'We\'re sorry. This product is only available for purchase by first time customers.';
	}

	public function hide_add_to_cart_button_for_repeat_customers_product() {

		// If the user isn't logged in, don't try to block anything.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// If the user is not a repeat customer, don't try to block anything.
		$repeat_customer = get_user_meta( get_current_user_id(), 'paid_order_count', true ) > 0 ? true : false;
		if ( ! $repeat_customer ) {
			return;
		}

		// Get all products that the restriction applies to.
		$restricted_product_ids = $this->blocked_ids;

		if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

			if ( in_array( get_the_id(), $restricted_product_ids ) ) {

				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_single_product_summary', function () {
					echo '<p>' . esc_html( $this->error_message ) . '</p>';
				}, 30 );
			}
		}
	}

	public function hide_add_to_cart_button_for_repeat_customers_quickview( $product ) {

		// If the user isn't logged in, don't try to block anything.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// If the user is not a repeat customer, don't try to block anything.
		$repeat_customer = get_user_meta( get_current_user_id(), 'paid_order_count', true ) > 0 ? true : false;
		if ( ! $repeat_customer ) {
			return;
		}

		// Get all products that the restriction applies to.
		$restricted_product_ids = $this->blocked_ids;

		if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

			if ( in_array( $product->get_id(), $restricted_product_ids ) ) {

				remove_action( 'woocommerce_variation_add_to_cart', array( 'Quick_View_Content', 'add_variation_to_cart_template' ) );
				remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_variation_add_to_cart', function () {
					echo '<p>' . esc_html( $this->error_message ) . '</p>';
				} );
				add_action( 'wc_quick_view_pro_quick_view_product_details', function () {
					echo '<p>' . esc_html( $this->error_message ) . '</p>';
				}, 30 );
			}
		}
	}

	// Block adding to cart when using ?eq-add-to-cart={product or variation ID}.
	public function disallow_repeat_customer_siteprefix_add_to_cart( $allowed, $product_id ) {

		// If the user isn't logged in, don't try to block anything.
		if ( ! is_user_logged_in() ) {
			return $allowed;
		}

		// If the user is a first time customer, don't try to block anything.
		$repeat_customer = get_user_meta( get_current_user_id(), 'paid_order_count', true ) > 0 ? true : false;
		if ( ! $repeat_customer ) {
			return $allowed;
		}

		// We store the product ID, not variation ID, so let's get the parent product ID if this happens to be a variation.
		if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

			$product_id = wp_get_post_parent_id( $product_id );
		}

		// Get all products that the restriction applies to.
		$restricted_product_ids = $this->blocked_ids;

		if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

			if ( in_array( $product_id, $restricted_product_ids, true ) ) {

				$allowed = false;
			}
		}

		return $allowed;
	}

	// Block adding to cart when using ?add-to-cart={product or variation ID}.
	public function disallow_repeat_customer_woo_add_to_cart( $valid, $added_product_id ) {

		$is_repeat_customer = (bool) false;
		if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'paid_order_count', true ) > 0 ) {
			$is_repeat_customer = true;
		}

		if ( wp_get_post_parent_id( $added_product_id ) !== 0 ) {

			$added_product_id = wp_get_post_parent_id( $added_product_id );
		}

		if ( $is_repeat_customer ) {

			// Get all products that the restriction applies to.
			$restricted_product_ids = $this->blocked_ids;

			if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

				if ( in_array( $added_product_id, $restricted_product_ids, true ) ) {

					$valid              = false;
					$added_product      = wc_get_product( $added_product_id );
					$added_product_name = $added_product->get_name();

					wc_add_notice( sprintf( __( 'You cannot add %s to the cart because it is only available for first time customers.', 'woocommerce' ), $added_product_name ), 'error' );
				}
			}
		}

		return $valid;
	}

	// Remove restricted products from cart on repeat customer login.
	public function remove_restricted_product_repeat_customer_login( $user_login, $user ) {

		// If the cart is empty don't check the cart.
		if ( WC()->cart->is_empty() ) {
			return;
		}

		// If the user is not a repeat customer, don't check the cart.
		$is_repeat_customer = get_user_meta( $user->ID, 'paid_order_count', true ) > 0 ? true : false;
		if ( ! $is_repeat_customer ) {
			return;
		}

		// This is a repeat customer, so let's remove the products they can't buy.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$product    = $cart_item['data'];
			$product_id = $cart_item['product_id'];

			if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

				$product_id = wp_get_post_parent_id( $product_id );
			}

			// Get all products that the restriction applies to.
			$restricted_product_ids = $this->blocked_ids;

			if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

				if ( in_array( $product_id, $restricted_product_ids, true ) ) {

					WC()->cart->remove_cart_item( $cart_item_key );
					$product_obj  = wc_get_product( $product_id );
					$product_name = $product_obj->get_name();
					wc_add_notice( sprintf( __( 'Removed %s from the cart because it is only available for first time customers.', 'woocommerce' ), $product_name ), 'error' );
				}
			}
		}
	}

	public function remove_restricted_product_repeat_customer_cart_refresh() {

		// If the user isn't logged in, don't check the cart.
		if ( ! is_user_logged_in() ) {
			return $cart_updated;
		}

		// If the user is a first time customer, don't check the cart.
		$repeat_customer = get_user_meta( get_current_user_id(), 'paid_order_count', true ) > 0 ? true : false;
		if ( ! $repeat_customer ) {
			return $cart_updated;
		}

		// This is a repeat customer, so let's remove the products they can't buy.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$product    = $cart_item['data'];
			$product_id = $cart_item['product_id'];

			if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

				$product_id = wp_get_post_parent_id( $product_id );
			}

			// Get all products that the restriction applies to.
			$restricted_product_ids = $this->blocked_ids;

			if ( ! empty( $restricted_product_ids ) && is_array( $restricted_product_ids ) ) {

				if ( in_array( $product_id, $restricted_product_ids, true ) ) {

					WC()->cart->remove_cart_item( $cart_item_key );
					$product_obj  = wc_get_product( $product_id );
					$product_name = $product_obj->get_name();
					wc_add_notice( sprintf( __( 'Removed %s from the cart because it is only available for first time customers.', 'woocommerce' ), $product_name ), 'error' );
				}
			}
		}

		return $cart_updated;
	}

}