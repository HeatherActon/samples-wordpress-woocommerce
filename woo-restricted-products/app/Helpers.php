<?php

namespace Sitename\RestrictedProducts;

class Helpers {

	// Get all custom product restrictions and their settings.
	public static function get_restrictions() {

		$restrictions = array();

		$restrictions['repeat_customer'] = array(
			'type'        => 'checkbox',
			'label'       => 'Restrict this product to first time customer only?',
			'description' => 'Checking this box will disallow anyone but first time customers (paid_order_count = 0) from purchasing it.',
			'meta_key'    => 'siteprefix_first_time_customer_only',
			'option_name' => 'siteprefix_first_time_customer_only_product_ids',
			'active'      => true
		);

		$restrictions['cbn'] = array(
			'type'        => 'checkbox',
			'label'       => 'Does this product contain CBN?',
			'description' => 'Some states restrict the purchase of products containing CBN. Checking this box will ensure this product is restricted in those states.',
			'meta_key'    => 'siteprefix_product_contains_cbn',
			'option_name' => 'siteprefix_contains_cbn_product_ids',
			'active'      => true
		);

		// Add more restrictions as we have them.

		return $restrictions;
	}

	// Get all custom product restrictions and their settings.
	public static function get_restriction( $name ) {

		$restriction = false;

		$restrictions = self::get_restrictions();

		foreach ( $restrictions as $key => $value ) {

			if ( $name === $key ) {

				return $value;
			}
		}
	}

	// Determine if a product or variation ID is restricted in some way.
	public static function is_product_restricted( $product_id ) {

		$retricted = false;

		if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

			$product_id = wp_get_post_parent_id( $product_id );
		}

		$restrictions = self::get_restrictions();

		if ( ! empty( $restrictions ) ) {

			$all_restricted_ids = array();

			foreach ( $restrictions as $restriction ) {
				$restricted_ids     = array();
				$restricted_ids     = get_option( $restriction['option_name'], array() );
				$all_restricted_ids = array_merge( $restricted_ids, $all_restricted_ids );
			}

			if ( ! empty( $all_restricted_ids ) && in_array( $product_id, $all_restricted_ids ) ) {
				$restricted = true;
			}
		}

		return $restricted;
	}

	// Get the restriction(s) that apply to this product.
	public static function get_product_restrictions( $product_id ) {

		$product_restrictions = false;

		if ( wp_get_post_parent_id( $product_id ) !== 0 ) {

			$product_id = wp_get_post_parent_id( $product_id );
		}

		$restrictions = self::get_restrictions();

		if ( ! empty( $restrictions ) ) {
			foreach ( $restrictions as $restriction ) {

				$restricted_ids = array();
				$restricted_ids = get_option( $restriction['option_name'], array() );
				if ( ! empty( $restricted_ids ) && in_array( $product_id, $restricted_ids ) ) {
					$product_restrictions[] = $restriction[0];
				}
			}
		}

		return $product_restrictions;
	}

	// Get product IDs blocked by a restriction.
	public static function get_blocked_ids( $restriction_name ) {

		$product_ids = array();

		$restrictions = self::get_restrictions();

		foreach ( $restrictions as $key => $value ) {

			if ( $restriction_name === $key ) {

				$product_ids = get_option( $value['option_name'], array() );
			}
		}

		return array_map( 'absint', $product_ids );
	}
}
