<?php
/**
 * Settings page (Settings > Before After Slider).
 *
 * @package BASFE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'basfe_admin_menu' );
add_action( 'admin_init', 'basfe_register_settings' );
add_filter( 'plugin_action_links_' . plugin_basename( BASFE_FILE ), 'basfe_action_links' );

function basfe_admin_menu() {
	add_options_page(
		__( 'Before After Slider', 'before-after-slider-for-elementor' ),
		__( 'Before After Slider', 'before-after-slider-for-elementor' ),
		'manage_options',
		'basfe-settings',
		'basfe_render_settings_page'
	);
}

function basfe_action_links( $links ) {
	$url = admin_url( 'options-general.php?page=basfe-settings' );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'before-after-slider-for-elementor' ) . '</a>' );
	return $links;
}

/**
 * Sanitize all options.
 *
 * @param mixed $in Raw input.
 * @return array
 */
function basfe_sanitize_options( $in ) {
	$d   = basfe_default_options();
	$in  = is_array( $in ) ? $in : array();
	$out = array();

	$out['orientation']  = ( isset( $in['orientation'] ) && 'vertical' === $in['orientation'] ) ? 'vertical' : 'horizontal';
	$out['offset']       = isset( $in['offset'] ) ? max( 0, min( 100, (int) $in['offset'] ) ) : $d['offset'];
	$out['hover']        = empty( $in['hover'] ) ? 0 : 1;
	$out['show_labels']  = empty( $in['show_labels'] ) ? 0 : 1;
	$out['before_label'] = isset( $in['before_label'] ) ? sanitize_text_field( $in['before_label'] ) : $d['before_label'];
	$out['after_label']  = isset( $in['after_label'] ) ? sanitize_text_field( $in['after_label'] ) : $d['after_label'];

	$color             = isset( $in['color'] ) ? sanitize_hex_color( $in['color'] ) : '';
	$icon              = isset( $in['icon_color'] ) ? sanitize_hex_color( $in['icon_color'] ) : '';
	$out['color']      = $color ? $color : $d['color'];
	$out['icon_color'] = $icon ? $icon : $d['icon_color'];

	$out['line_width'] = isset( $in['line_width'] ) ? max( 1, min( 12, (int) $in['line_width'] ) ) : $d['line_width'];
	$out['knob_size']  = isset( $in['knob_size'] ) ? max( 20, min( 90, (int) $in['knob_size'] ) ) : $d['knob_size'];

	return $out;
}

function basfe_register_settings() {
	register_setting(
		'basfe_settings_group',
		'basfe_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'basfe_sanitize_options',
			'default'           => array(),
		)
	);

	add_settings_section(
		'basfe_main',
		__( 'Default settings for the shortcode', 'before-after-slider-for-elementor' ),
		'basfe_section_intro',
		'basfe-settings'
	);

	$td     = 'before-after-slider-for-elementor';
	$fields = array(
		array( 'orientation', __( 'Orientation', $td ), 'select', array(
			'horizontal' => __( 'Horizontal', $td ),
			'vertical'   => __( 'Vertical', $td ),
		) ),
		array( 'offset', __( 'Starting position (%)', $td ), 'number', array( 'min' => 0, 'max' => 100 ) ),
		array( 'hover', __( 'Move on hover', $td ), 'checkbox', __( 'Slide when the mouse moves over the image', $td ) ),
		array( 'show_labels', __( 'Labels', $td ), 'checkbox', __( 'Show Before/After labels', $td ) ),
		array( 'before_label', __( 'Before label', $td ), 'text', array() ),
		array( 'after_label', __( 'After label', $td ), 'text', array() ),
		array( 'color', __( 'Line & knob color', $td ), 'color', array() ),
		array( 'icon_color', __( 'Arrow color', $td ), 'color', array() ),
		array( 'line_width', __( 'Line width (px)', $td ), 'number', array( 'min' => 1, 'max' => 12 ) ),
		array( 'knob_size', __( 'Knob size (px)', $td ), 'number', array( 'min' => 20, 'max' => 90 ) ),
	);

	foreach ( $fields as $f ) {
		add_settings_field(
			'basfe_' . $f[0],
			$f[1],
			'basfe_render_field',
			'basfe-settings',
			'basfe_main',
			array(
				'key'  => $f[0],
				'type' => $f[2],
				'extra' => $f[3],
			)
		);
	}
}

function basfe_section_intro() {
	echo '<p>' . esc_html__( 'These defaults apply to the [before_after_slider] shortcode. Any value can be overridden per shortcode. The block uses its own sidebar options, but the color and size values below apply to it too. The Elementor widget uses its own controls inside the Elementor editor.', 'before-after-slider-for-elementor' ) . '</p>';
}

function basfe_render_field( $args ) {
	$o     = basfe_get_options();
	$key   = $args['key'];
	$name  = 'basfe_options[' . $key . ']';
	$value = isset( $o[ $key ] ) ? $o[ $key ] : '';
	$extra = $args['extra'];

	switch ( $args['type'] ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" name="%1$s" value="1" %2$s /> %3$s</label>',
				esc_attr( $name ),
				checked( ! empty( $value ), true, false ),
				esc_html( $extra )
			);
			break;
		case 'select':
			printf( '<select name="%s">', esc_attr( $name ) );
			foreach ( $extra as $val => $label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $val ),
					selected( $value, $val, false ),
					esc_html( $label )
				);
			}
			echo '</select>';
			break;
		case 'number':
			printf(
				'<input type="number" class="small-text" name="%1$s" value="%2$s" min="%3$d" max="%4$d" />',
				esc_attr( $name ),
				esc_attr( $value ),
				(int) $extra['min'],
				(int) $extra['max']
			);
			break;
		case 'color':
			printf( '<input type="color" name="%1$s" value="%2$s" />', esc_attr( $name ), esc_attr( $value ) );
			break;
		default:
			printf( '<input type="text" class="regular-text" name="%1$s" value="%2$s" />', esc_attr( $name ), esc_attr( $value ) );
	}
}

function basfe_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'basfe_settings_group' );
			do_settings_sections( 'basfe-settings' );
			submit_button();
			?>
		</form>

		<hr />
		<h2><?php esc_html_e( 'How to use', 'before-after-slider-for-elementor' ); ?></h2>
		<p><strong><?php esc_html_e( 'Elementor:', 'before-after-slider-for-elementor' ); ?></strong> <?php esc_html_e( 'search for "Before After Slider" in the widget panel.', 'before-after-slider-for-elementor' ); ?></p>
		<p><strong><?php esc_html_e( 'Block editor:', 'before-after-slider-for-elementor' ); ?></strong> <?php esc_html_e( 'add the "Before After Slider" block (Media category).', 'before-after-slider-for-elementor' ); ?></p>
		<p><strong><?php esc_html_e( 'Shortcode (posts, pages, Shortcode block, text widgets):', 'before-after-slider-for-elementor' ); ?></strong></p>
		<p><code>[before_after_slider before="123" after="124"]</code></p>
		<p><code>[before_after_slider before="https://example.com/a.jpg" after="https://example.com/b.jpg" orientation="vertical" offset="30" labels="no"]</code></p>
		<p><?php esc_html_e( 'The image can be a Media Library attachment ID or a full image URL.', 'before-after-slider-for-elementor' ); ?></p>
		<p><strong><?php esc_html_e( 'Theme template (PHP):', 'before-after-slider-for-elementor' ); ?></strong></p>
		<p><code>&lt;?php echo do_shortcode( '[before_after_slider before="123" after="124"]' ); ?&gt;</code></p>
		<p><strong><?php esc_html_e( 'Available attributes:', 'before-after-slider-for-elementor' ); ?></strong> before, after, size, orientation, offset, hover, labels, before_label, after_label, alt_before, alt_after, width, align</p>
	</div>
	<?php
}
