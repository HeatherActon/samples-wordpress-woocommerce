<?php
/*
 * We have a color picker to choose background color on certain blocks. This function is used on those blocks to automagically set copy color.
 *
 * This function uses the background hex value to determine its luminance, and based on that value, will add a class to the block to set the copy color for the block.
 * The admin doesn't have to make another choice on their own (or forget to choose).
 */
function eq_auto_copy_color_class( $background_color = '' ) {

	$copy_color_class = '';

	if ( '' !== $background_color ) {

		list( $r, $g, $b ) = sscanf( $background_color, '#%02x%02x%02x' );

		$luminance = ( $r * 0.299 + $g * 0.587 + $b * 0.114 ) / 3;

		if ( ( 255 / 3 / 2 ) > $luminance ) {
			$copy_color_class = ' cc-white ';
		}
		else {
			$copy_color_class = ' cc-dark ';
		}
	}

	return $copy_color_class;
}
