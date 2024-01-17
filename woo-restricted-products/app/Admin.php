<?php

namespace Sitename\RestrictedProducts;

class Admin {

	public function __construct() {

		$this->restrictions = \Sitename\RestrictedProducts\Helpers::get_restrictions();
	}

	// Add custom product data tabs.
	public function add_product_data_tabs( $tabs ) {

		$tabs['siteprefix_product_purchase_restrictions'] = array(
			'label'    => 'Purchase Restrictions',
			'target'   => 'siteprefix_product_purchase_restrictions',
			'priority' => 20,
		);

		return $tabs;

	}

	// Change icons on our custom product data tabs.
	public function change_product_tab_icons() {

		echo '<style>
			#woocommerce-product-data ul.wc-tabs li.siteprefix_product_purchase_restrictions_options a::before {
				content: "\f534";
			}
		</style>';
	}

	// Add the necessary fields to the product data tabs.
	public function edit_product() {

		echo '<div id="siteprefix_product_purchase_restrictions" class="panel woocommerce_options_panel hidden">';
		echo '<div class="options_group">';

		foreach ( $this->restrictions as $restriction ) {

			if ( 'checkbox' === $restriction['type'] ) {
				woocommerce_wp_checkbox(
					array(
						'id'          => $restriction['meta_key'],
						'value'       => get_post_meta( get_the_ID(), $restriction['meta_key'], true ),
						'label'       => $restriction['label'],
						'desc_tip'    => true,
						'description' => $restriction['description'],
					)
				);
			} // can add more types later
		}

		echo '</div>';
		echo '</div>';
	}

	// Save data from the fields to product meta.
	public function save_product( $product_id ) {

		$product = wc_get_product( $product_id );

		foreach ( $this->restrictions as $restriction ) {

			$data_posted = isset( $_POST[$restriction['meta_key']] ) ? sanitize_text_field( $_POST[$restriction['meta_key']] ) : '';

			$product->update_meta_data( $restriction['meta_key'], $data_posted );
		}

		$product->save();
	}

	// Save the product IDs to a site option for performance.
	public function save_option( $product_id ) {

		$product = wc_get_product( $product_id );

		foreach ( $this->restrictions as $restriction ) {
			// Get the option that saves all products where this restriction is applicable.
			$option = get_option( $restriction['option_name'], array() );
			$option = array_map( 'absint', $option );

			if ( isset( $_POST[$restriction['meta_key']] ) && $_POST[$restriction['meta_key']] === 'yes' ) { // phpcs:ignore

				// Add product id to the list if its not in there already.
				if ( ! $option || '' === $option || ! in_array( $product_id, $option, true ) ) {
					$option[] = $product_id;
				}
			}
			else {

				// Remove product id from list if it's currently in the list.
				if ( is_array( $option ) && in_array( $product_id, $option, true ) ) {
					$option = array_diff( $option, array( $product_id ) );
				}
			}

			update_option( $restriction['option_name'], $option );
		}
	}
}
