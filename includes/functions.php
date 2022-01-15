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

	$defaults = basfe_default_options();
	$color    = sanitize_hex_color( $o['color'] ) ? $o['color'] : $defaults['color'];
	$icon     = sanitize_hex_color( $o['icon_color'] ) ? $o['icon_color'] : $defaults['icon_color'];
	$line     = max( 1, min( 12, (int) $o['line_width'] ) );
	$knob     = max( 20, min( 90, (int) $o['knob_size'] ) );

	$auto  = basfe_auto_width( array( $a['before'], $a['after'] ), $size );
	$align = in_array( $a['align'], array( 'left', 'center', 'right' ), true ) ? $a['align'] : 'left';

	$style = sprintf(
		'--basfe-pos:%s%%;--basfe-color:%s;--basfe-icon:%s;--basfe-line:%dpx;--basfe-knob:%dpx;',
		$offset,
		$color,
		$icon,
		$line,
		$knob
	);

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

	$o = basfe_get_options();
	$d = basfe_default_options();

	$data = array(
		'color' => sanitize_hex_color( $o['color'] ) ? $o['color'] : $d['color'],
		'icon'  => sanitize_hex_color( $o['icon_color'] ) ? $o['icon_color'] : $d['icon_color'],
		'line'  => max( 1, min( 12, (int) $o['line_width'] ) ),
		'knob'  => max( 20, min( 90, (int) $o['knob_size'] ) ),
	);

	wp_add_inline_script(
		generate_block_asset_handle( 'basfe/before-after-slider', 'editorScript' ),
		'window.basfeDefaults = ' . wp_json_encode( $data ) . ';',
		'before'
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
