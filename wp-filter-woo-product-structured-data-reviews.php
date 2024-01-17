<?php
// Filter product structured data ratings and reviews to use our custom reviews data (Woo core reviews were not used).

add_filter( 'woocommerce_structured_data_product', 'prefixredacted_reviews_structured_data_product', 99, 2 );
function prefixredacted_reviews_structured_data_product( $markup, $product ) {

	$reviews = array();

	if ( function_exists( 'prefixredacted_get_reviews_for_product_id' ) ) {
		$reviews = prefixredacted_get_reviews_for_product_id( $product->get_id(), '0', '1000000' );
	}

	if ( $reviews && 0 < count( $reviews ) ) {

    // Do markup for reviews since we have some.
    $markup['review'] = array();
		$i                = 0;
    
		foreach ( $reviews as $review ) {
			$i++;
			$markup['review'][] = array(
				'@type'         => 'Review',
				'reviewRating'  => array(
					'@type'       => 'Rating',
					'bestRating'  => '5',
					'ratingValue' => intval( $review->stars ),
					'worstRating' => '1',
				),
				'author'        => array(
					'@type' => 'Person',
					'name'  => esc_html( $review->name ),
				),
				'reviewBody'    => esc_html( $review->content ),
				'datePublished' => esc_html( $review->created_at ),
			);
			if ( 5 === $i ) {
				break;
			}
		}

    // Now do stars.
    $review_stars = 0;
  
    if ( function_exists( 'prefixredacted_get_stars_for_product_id' ) ) {
  		$review_stars = prefixredacted_get_stars_for_product_id( $product->get_id(), '0', '1000000' );
  	}

    if ( 0 < $review_stars ) {
  		$markup['aggregateRating'] = array(
  			'@type'       => 'AggregateRating',
  			'ratingValue' => round( floatval( $review_stars ), 1, PHP_ROUND_HALF_UP ),
  			'reviewCount' => count( $reviews ),
  		);
    }
	}

	return $markup;
}
