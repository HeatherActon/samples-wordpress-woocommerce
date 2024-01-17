<?php
namespace Sitename\RestrictedProducts\Restrictions;

class CBN {

	public function __construct() {

		$this->restriction   = \Sitename\RestrictedProducts\Helpers::get_restriction( 'cbn' );
		$this->blocked_ids   = \Sitename\RestrictedProducts\Helpers::get_blocked_ids( 'cbn' );
		$this->error_message = 'Product cannot be legally shipped to Oregon.';

	}

	public function maybe_block_purchase( $data, $errors ) {

		// Find out if the customer is in Oregon.
		$shipping_state = isset( $data['shipping_state'] ) ? $data['shipping_state'] : WC()->customer->get_shipping_state();

		// We have an Oreganian. Continue on.
		// TODO have a site options page where this can be set
		if ( 'OR' !== $shipping_state ) {
			return;
		}

		// See if we have a CBN product in the cart, and if we do throw an error.
		if ( WC()->cart && ! WC()->cart->is_empty() ) {

			// Get restricted product IDs.
			$restricted_product_ids = $this->blocked_ids;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$product_id = $cart_item['product_id'];

				if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

					$product_id = wp_get_post_parent_id( $product_id );
				}

				if ( in_array( $product_id, $restricted_product_ids, true ) ) {

					$errors->add( 'restricted_product', __( $this->error_message, 'woocommerce' ) );
					break;
				}
			}
		}
	}

	// TODO move this to Front? or Templates?
	// TODO dynamically populate the restricted product image, title, variation info
	// TODO let admins edit the content
	public function purchase_blocked_modal() {
		$blocked_ids = implode( ',', $this->blocked_ids );
		?>
		<style>
			#lightbox-restricted-product {
				text-align: center;
				padding-left: 10px;
				padding-right: 10px;
			}

			#lightbox-restricted-product h2 {
				font-family: "sofia-pro", sans-serif;
				font-size: 2rem;
				line-height: 1.2;
			}

			#lightbox-restricted-product h3 {
				font-weight: bold;
				font-size: 1.125rem;
				margin-bottom: .5rem;
			}

			#lightbox-restricted-product p,
			#lightbox-restricted-product .btn {
				font-size: 1.125rem;
				line-height: 1.5rem;
			}

			#impacted-products {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
				align-items: center;
			}

			#impacted-products>* {
				padding-left: .25rem;
				padding-right: .25rem;
			}

			#impacted-products img {
				display: none;
			}

			@media (min-width: 600px) {
				#impacted-products {
					width: 60%;
					margin-left: auto;
					margin-right: auto;
					margin-bottom: 1.5rem;
				}

				#impacted-products>* {
					width: 50%;
				}

				#lightbox-restricted-product h3 {
					margin-bottom: 1rem;
				}

				#impacted-products img {
					display: block;
				}
			}
		</style>
		<script>
			(function ($) {
				$(document).on("checkout_error", function (code, message) {
					ourMessage = "<?php echo esc_html( $this->error_message ); ?>";
					if (message.indexOf(ourMessage) != -1) {
						$("#lightbox-restricted-product-container").show();
					}
				});
			})(jQuery);
		</script>
		<div id="lightbox-restricted-product-container" class="featherlight">
			<div class="featherlight-content">
				<div id="lightbox-restricted-product" class="generic-lightbox">
					<h2>We apologize for the inconvenience.</h2>

					<?php if ( 'discover' === SITEPREFIX_HOST ) { ?>
						<p>Due to updated state regulations we are no longer able to ship products with certain ingredients to
							Oregon.</p>
					<?php }
					else { ?>
						<p>Due to updated state regulations we are no longer able to ship products containing CBN to Oregon. <a
								href="https://support.myeq.com/hc/en-us/articles/8661364416788" target="_blank">Learn more</a></p>
					<?php } ?>

					<p>We recommend reaching out to a <a href="/dosage/" target="_blank">Dosage Specialist</a> to discuss
						alternative product options!</p>
					<button id="remove-restricted-products" class="btn btn-default"
						data-remove-product-id="<?php echo esc_attr( $blocked_ids ); ?>">Remove impacted product(s) from cart
						and return to
						checkout</button>
					<button class="featherlight-close-icon featherlight-close" aria-label="Close"
						onclick="jQuery('#lightbox-restricted-product-container').hide();">✕</button>
				</div>
			</div>
		</div>
		<?php
	}

	// This is an AJAX action.
	public function remove_restricted_products_from_cart() {

		if ( ! isset( $_REQUEST['customer_id'], $_REQUEST['security'] ) ) { //phpcs:ignore
			wp_send_json_error( new \WP_Error( 'missing', 'Missing customer' ) );
		}

		$customer_id = (int) $_REQUEST['customer_id']; //phpcs:ignore;

		if ( ! wp_verify_nonce( $_REQUEST['security'], 'remove-restricted-product-' . $customer_id ) ) { //phpcs:ignore
			wp_send_json_error( new \WP_Error( 'security', 'Security Check' ) );
		}

		// productID may be a single ID, or a comma separated list of IDs.
		$remove_product_ids = isset( $_REQUEST['productID'] ) ? sanitize_text_field( $_REQUEST['productID'] ) : '';

		if ( str_contains( $remove_product_ids, ',' ) ) {
			$remove_product_ids = array_map( 'intval', explode( ',', $_REQUEST['productID'] ) ); //phpcs:ignore;
		}
		else {
			$remove_product_ids = (int) $_REQUEST['productID'];
		}

		$return = array();

		if ( WC()->cart && ! WC()->cart->is_empty() ) {

			$counter = 0;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				if ( ( is_array( $remove_product_ids ) && in_array( $cart_item['product_id'], $remove_product_ids, true ) ) || $cart_item['product_id'] === $remove_product_ids ) {

					/* if cart item is a child of a bundle */
					if ( $cart_item['composite_parent'] ) {

						/* remove the bundle */
						WC()->cart->remove_cart_item( $cart_item['composite_parent'] );
					}
					/* if cart item is not the child of a bundle */
					else {

						/* remove the product */
						WC()->cart->remove_cart_item( $cart_item_key );
					}

					$counter++;
					$return['product_removed'][$counter]['id'] = $cart_item['product_id'];
				}
			}
		}

		if ( WC()->cart->is_empty() ) {
			$return['redirect'] = true;
		}
		else {
			$return['redirect'] = false;
		}

		wp_send_json_success( $return, 200 );

		wp_die();
	}
}
