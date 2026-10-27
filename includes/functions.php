<?php
/**
 * Options helpers and shortcode.
 *
 * @package BASFE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default option values.
 *
 * @return array
 */
function basfe_default_options() {
	return array(
		'orientation'  => 'horizontal',
		'offset'       => 50,
		'hover'        => 0,
		'show_labels'  => 1,
		'before_label' => __( 'Before', 'before-after-slider-for-elementor' ),
		'after_label'  => __( 'After', 'before-after-slider-for-elementor' ),
		'color'        => '#ffffff',
		'icon_color'   => '#333333',
		'line_width'   => 3,
		'knob_size'    => 44,
		'knob_opacity' => 100,

		'label_color'       => '#ffffff',
		'label_bg'          => '#000000',
		'label_bg_opacity'  => 55,
		'label_font_size'   => 14,
		'label_font_weight' => '400',
		'label_font_style'  => 'normal',
		'label_pad_y'       => 4,
		'label_pad_x'       => 10,
		'label_top'         => 12,
		'label_left'        => 12,
		'label_right'       => 12,
		'label_radius'      => 3,
	);
}

/**
 * Saved options merged with defaults.
 *
 * @return array
 */
function basfe_get_options() {
	$saved = get_option( 'basfe_options', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), basfe_default_options() );
}

/**
 * Build an <img> from an attachment ID or a URL.
 *
 * @param string $src  Attachment ID or URL.
 * @param string|array $size Image size name or array( width, height ).
 * @param string $alt  Alt text (used for URLs only).
 * @return string
 */
function basfe_image_html( $src, $size, $alt = '' ) {
	$src = trim( (string) $src );
	if ( '' === $src ) {
		return '';
	}
	if ( ctype_digit( $src ) ) {
		return wp_get_attachment_image( (int) $src, $size );
	}
	$url = esc_url( $src );
	if ( '' === $url ) {
		return '';
	}
	return '<img src="' . $url . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async" />';
}

/**
 * Smallest displayed width (px) of the given images at the given size.
 * Only attachment IDs can be measured; URLs are ignored.
 *
 * @param array        $sources Attachment IDs or URLs.
 * @param string|array $size    Image size.
 * @return int 0 when unknown.
 */
function basfe_auto_width( $sources, $size ) {
	$widths = array();
	foreach ( (array) $sources as $src ) {
		$src = trim( (string) $src );
		if ( '' !== $src && ctype_digit( $src ) && (int) $src > 0 ) {
			$img = wp_get_attachment_image_src( (int) $src, $size );
			if ( $img && ! empty( $img[1] ) ) {
				$widths[] = (int) $img[1];
			}
		}
	}
	return $widths ? min( $widths ) : 0;
}

/**
 * Convert a shortcode flag to bool.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function basfe_is_yes( $value ) {
	return in_array( strtolower( (string) $value ), array( 'yes', 'true', '1', 'on' ), true );
}

/**
 * [before_after_slider before="ID or URL" after="ID or URL"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function basfe_shortcode( $atts ) {
	$o = basfe_get_options();

	$a = shortcode_atts(
		array(
			'before'       => '',
			'after'        => '',
			'size'         => 'large',
			'orientation'  => $o['orientation'],
			'offset'       => $o['offset'],
			'hover'        => $o['hover'] ? 'yes' : 'no',
			'labels'       => $o['show_labels'] ? 'yes' : 'no',
			'before_label' => $o['before_label'],
			'after_label'  => $o['after_label'],
			'alt_before'   => '',
			'alt_after'    => '',
			'width'        => '',
			'align'        => 'left',
		),
		$atts,
		'before_after_slider'
	);

	$size = sanitize_text_field( $a['size'] );
	// Custom size, e.g. size="800x600".
	if ( preg_match( '/^(\d{1,4})x(\d{1,4})$/i', $size, $m ) ) {
		$size = array( (int) $m[1], (int) $m[2] );
	}
	$before = basfe_image_html( $a['before'], $size, $a['alt_before'] );
	$after  = basfe_image_html( $a['after'], $size, $a['alt_after'] );

	if ( '' === $before || '' === $after ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return '<p>' . esc_html__( 'Before After Slider: please set valid "before" and "after" images (attachment ID or URL).', 'before-after-slider-for-elementor' ) . '</p>';
		}
		return '';
	}

	wp_enqueue_style( 'basfe-style' );
	wp_enqueue_script( 'basfe-view' );

	$orientation = ( 'vertical' === $a['orientation'] ) ? 'vertical' : 'horizontal';
	$offset      = max( 0, min( 100, (float) $a['offset'] ) );
	$hover       = basfe_is_yes( $a['hover'] ) ? 'yes' : 'no';
	$labels      = basfe_is_yes( $a['labels'] );

	$auto  = basfe_auto_width( array( $a['before'], $a['after'] ), $size );
	$align = in_array( $a['align'], array( 'left', 'center', 'right' ), true ) ? $a['align'] : 'left';
	$style = '--basfe-pos:' . $offset . '%;';

	if ( $auto > 0 ) {
		$style .= '--basfe-auto:' . (int) $auto . 'px;';
	}

	// Optional manual width: "600", "600px" or "80%".
	$width = trim( (string) $a['width'] );
	if ( preg_match( '/^\\d+(\\.\\d+)?(px|%)?$/', $width ) ) {
		$style .= '--basfe-max:' . $width . ( preg_match( '/(px|%)$/', $width ) ? '' : 'px' ) . ';';
	}

	$html  = '<div class="basfe" data-orientation="' . esc_attr( $orientation ) . '" data-offset="' . esc_attr( $offset ) . '" data-hover="' . esc_attr( $hover ) . '" data-align="' . esc_attr( $align ) . '" style="' . esc_attr( $style ) . '">';
	$html .= '<div class="basfe-img basfe-after">' . wp_kses_post( $after ) . '</div>';
	$html .= '<div class="basfe-img basfe-before">' . wp_kses_post( $before ) . '</div>';

	if ( $labels ) {
		if ( '' !== $a['before_label'] ) {
			$html .= '<span class="basfe-label basfe-label-before">' . esc_html( $a['before_label'] ) . '</span>';
		}
		if ( '' !== $a['after_label'] ) {
			$html .= '<span class="basfe-label basfe-label-after">' . esc_html( $a['after_label'] ) . '</span>';
		}
	}

	$html .= '<div class="basfe-handle" role="slider" tabindex="0" aria-label="' . esc_attr__( 'Image comparison slider', 'before-after-slider-for-elementor' ) . '" aria-orientation="' . esc_attr( $orientation ) . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( round( $offset ) ) . '">';
	$html .= '<span class="basfe-line"></span><span class="basfe-knob"></span></div>';
	$html .= '</div>';

	return $html;
}

/**
 * Register the Gutenberg block.
 */
function basfe_register_block() {
	register_block_type(
		BASFE_PATH . 'blocks/before-after-slider',
		array( 'render_callback' => 'basfe_render_block' )
	);
}

/**
 * Pick the attachment ID if it is still a valid image, otherwise the URL.
 *
 * @param mixed  $id  Attachment ID.
 * @param string $url Fallback URL.
 * @return string
 */
function basfe_block_image_source( $id, $url ) {
	$id = (int) $id;
	if ( $id > 0 && wp_attachment_is_image( $id ) ) {
		return (string) $id;
	}
	return (string) $url;
}

/**
 * Render callback for the block (reuses the shortcode renderer).
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function basfe_render_block( $attributes ) {
	$size = isset( $attributes['size'] ) ? $attributes['size'] : 'large';
	if ( 'custom' === $size ) {
		$w    = isset( $attributes['customWidth'] ) ? (int) $attributes['customWidth'] : 0;
		$h    = isset( $attributes['customHeight'] ) ? (int) $attributes['customHeight'] : 0;
		$size = ( $w > 0 && $h > 0 ) ? $w . 'x' . $h : 'large';
	}

	$html = basfe_shortcode(
		array(
			'before'       => basfe_block_image_source( isset( $attributes['beforeId'] ) ? $attributes['beforeId'] : 0, isset( $attributes['beforeUrl'] ) ? $attributes['beforeUrl'] : '' ),
			'after'        => basfe_block_image_source( isset( $attributes['afterId'] ) ? $attributes['afterId'] : 0, isset( $attributes['afterUrl'] ) ? $attributes['afterUrl'] : '' ),
			'size'         => $size,
			'orientation'  => isset( $attributes['orientation'] ) ? $attributes['orientation'] : 'horizontal',
			'offset'       => isset( $attributes['offset'] ) ? $attributes['offset'] : 50,
			'hover'        => ! empty( $attributes['hover'] ) ? 'yes' : 'no',
			'labels'       => ( ! isset( $attributes['showLabels'] ) || $attributes['showLabels'] ) ? 'yes' : 'no',
			'before_label' => isset( $attributes['beforeLabel'] ) ? $attributes['beforeLabel'] : '',
			'after_label'  => isset( $attributes['afterLabel'] ) ? $attributes['afterLabel'] : '',
			'width'        => ( ! empty( $attributes['maxWidth'] ) && (int) $attributes['maxWidth'] > 0 ) ? (int) $attributes['maxWidth'] : '',
			'align'        => isset( $attributes['sliderAlign'] ) ? $attributes['sliderAlign'] : 'left',
		)
	);

	if ( '' === $html ) {
		return '';
	}

	return '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>';
}

/**
 * Validate and sanitize the full options array.
 *
 * @param mixed $in Raw input.
 * @return array
 */
function basfe_sanitize_options( $in ) {
	$d   = basfe_default_options();
	$in  = is_array( $in ) ? $in : array();
	$out = array();

	$int = function ( $key, $min, $max ) use ( $in, $d ) {
		return isset( $in[ $key ] ) ? max( $min, min( $max, (int) $in[ $key ] ) ) : $d[ $key ];
	};
	$hex = function ( $key ) use ( $in, $d ) {
		$c = isset( $in[ $key ] ) ? sanitize_hex_color( $in[ $key ] ) : '';
		return $c ? $c : $d[ $key ];
	};

	$out['orientation']  = ( isset( $in['orientation'] ) && 'vertical' === $in['orientation'] ) ? 'vertical' : 'horizontal';
	$out['offset']       = $int( 'offset', 0, 100 );
	$out['hover']        = empty( $in['hover'] ) ? 0 : 1;
	$out['show_labels']  = empty( $in['show_labels'] ) ? 0 : 1;
	$out['before_label'] = isset( $in['before_label'] ) ? sanitize_text_field( $in['before_label'] ) : $d['before_label'];
	$out['after_label']  = isset( $in['after_label'] ) ? sanitize_text_field( $in['after_label'] ) : $d['after_label'];

	$out['color']        = $hex( 'color' );
	$out['icon_color']   = $hex( 'icon_color' );
	$out['line_width']   = $int( 'line_width', 1, 12 );
	$out['knob_size']    = $int( 'knob_size', 20, 90 );
	$out['knob_opacity'] = $int( 'knob_opacity', 0, 100 );

	$out['label_color']      = $hex( 'label_color' );
	$out['label_bg']         = $hex( 'label_bg' );
	$out['label_bg_opacity'] = $int( 'label_bg_opacity', 0, 100 );
	$out['label_font_size']  = $int( 'label_font_size', 8, 48 );
	$out['label_pad_y']      = $int( 'label_pad_y', 0, 50 );
	$out['label_pad_x']      = $int( 'label_pad_x', 0, 50 );
	$out['label_top']        = $int( 'label_top', 0, 200 );
	$out['label_left']       = $int( 'label_left', 0, 200 );
	$out['label_right']      = $int( 'label_right', 0, 200 );
	$out['label_radius']     = $int( 'label_radius', 0, 50 );

	$weights                  = array( '300', '400', '500', '600', '700', '800' );
	$out['label_font_weight'] = ( isset( $in['label_font_weight'] ) && in_array( (string) $in['label_font_weight'], $weights, true ) ) ? (string) $in['label_font_weight'] : $d['label_font_weight'];
	$out['label_font_style']  = ( isset( $in['label_font_style'] ) && 'italic' === $in['label_font_style'] ) ? 'italic' : 'normal';

	return $out;
}

/**
 * Global CSS variables built from the settings page.
 * Elementor controls override these per widget.
 *
 * @return string
 */
function basfe_global_css() {
	$o = basfe_sanitize_options( basfe_get_options() );

	$vars = array(
		'--basfe-color'            => $o['color'],
		'--basfe-icon'             => $o['icon_color'],
		'--basfe-line'             => $o['line_width'] . 'px',
		'--basfe-knob'             => $o['knob_size'] . 'px',
		'--basfe-knob-opacity'     => $o['knob_opacity'] . '%',
		'--basfe-label-color'      => $o['label_color'],
		'--basfe-label-bg'         => $o['label_bg'],
		'--basfe-label-bg-opacity' => $o['label_bg_opacity'] . '%',
		'--basfe-label-size'       => $o['label_font_size'] . 'px',
		'--basfe-label-weight'     => $o['label_font_weight'],
		'--basfe-label-style'      => $o['label_font_style'],
		'--basfe-label-pad-y'      => $o['label_pad_y'] . 'px',
		'--basfe-label-pad-x'      => $o['label_pad_x'] . 'px',
		'--basfe-label-top'        => $o['label_top'] . 'px',
		'--basfe-label-left'       => $o['label_left'] . 'px',
		'--basfe-label-right'      => $o['label_right'] . 'px',
		'--basfe-label-radius'     => $o['label_radius'] . 'px',
	);

	$css = '';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	return ':root{' . $css . '}';
}
