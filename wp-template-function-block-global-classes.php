<?php
/*
 * Generate a list of classes to add to a block, based on admin settings in the block.
 * Reused in block template parts.
 */
function eq_get_block_global_classes() {

	$global_classes = '';

	// From field group "Clone – Background Color"
	$bg_color = get_field( 'bg_color' );
	if ( ! empty( $bg_color ) ) {
		$global_classes .= ' bg-' . $bg_color;
	}

	// From field group "Clone – Block Padding"
	$dp_top = get_field( 'top_padding_desktop' );
	if ( ! empty( $dp_top ) ) {
		$global_classes .= ' desktop-padding-top-' . $dp_top;
	}

	// From field group "Clone – Block Padding"
	$dp_bottom = get_field( 'bottom_padding_desktop' );
	if ( ! empty( $dp_bottom ) ) {
		$global_classes .= ' desktop-padding-bottom-' . $dp_bottom;
	}

	// From field group "Clone – Block Padding"
	$mp_top = get_field( 'top_padding' );
	if ( ! empty( $mp_top ) ) {
		$global_classes .= ' mobile-padding-top-' . $mp_top;
	}

	// From field group "Clone – Block Padding"
	$mp_bottom = get_field( 'bottom_padding' );
	if ( ! empty( $mp_bottom ) ) {
		$global_classes .= ' mobile-padding-bottom-' . $mp_bottom;
	}

	// From field group "Clone – Block Alignment"
	$horiz_alignment = get_field( 'alignment' );
	if ( ! empty( $horiz_alignment ) ) {
		$global_classes .= ' halign-' . $horiz_alignment;
	}

	// From field group "Clone – Block Borders"
	$block_border_top = get_field( 'block_border_top' );
	if ( $block_border_top ) {
		$global_classes .= ' border-top';
	}

	// From field group "Clone – Block Borders"
	$block_border_bottom = get_field( 'block_border_bottom' );
	if ( $block_border_bottom ) {
		$global_classes .= ' border-bottom';
	}

	// From field group "Clone – Block Width"
	$block_width = get_field( 'block_width' );
	if ( $block_width ) {
		$global_classes .= ' width-' . $block_width;
	}
	return $global_classes;
}
