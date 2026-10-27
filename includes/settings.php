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

	add_settings_section( 'basfe_main', __( 'Slider defaults', 'before-after-slider-for-elementor' ), 'basfe_section_intro', 'basfe-settings' );
	add_settings_section( 'basfe_handle', __( 'Handle', 'before-after-slider-for-elementor' ), '__return_null', 'basfe-settings' );
	add_settings_section( 'basfe_labels', __( 'Labels (Before / After badges)', 'before-after-slider-for-elementor' ), 'basfe_labels_intro', 'basfe-settings' );

	$weights = array(
		'300' => __( 'Light (300)', 'before-after-slider-for-elementor' ),
		'400' => __( 'Normal (400)', 'before-after-slider-for-elementor' ),
		'500' => __( 'Medium (500)', 'before-after-slider-for-elementor' ),
		'600' => __( 'Semi-bold (600)', 'before-after-slider-for-elementor' ),
		'700' => __( 'Bold (700)', 'before-after-slider-for-elementor' ),
		'800' => __( 'Extra-bold (800)', 'before-after-slider-for-elementor' ),
	);

	// key, label, type, extra, section.
	$fields = array(
		array( 'orientation', __( 'Orientation', 'before-after-slider-for-elementor' ), 'select', array(
			'horizontal' => __( 'Horizontal', 'before-after-slider-for-elementor' ),
			'vertical'   => __( 'Vertical', 'before-after-slider-for-elementor' ),
		), 'basfe_main' ),
		array( 'offset', __( 'Starting position', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 100, 'suffix' => '%' ), 'basfe_main' ),
		array( 'hover', __( 'Move on hover', 'before-after-slider-for-elementor' ), 'checkbox', __( 'Slide when the mouse moves over the image', 'before-after-slider-for-elementor' ), 'basfe_main' ),
		array( 'show_labels', __( 'Labels', 'before-after-slider-for-elementor' ), 'checkbox', __( 'Show Before/After labels', 'before-after-slider-for-elementor' ), 'basfe_main' ),
		array( 'before_label', __( 'Before label text', 'before-after-slider-for-elementor' ), 'text', array(), 'basfe_main' ),
		array( 'after_label', __( 'After label text', 'before-after-slider-for-elementor' ), 'text', array(), 'basfe_main' ),

		array( 'color', __( 'Line & knob color', 'before-after-slider-for-elementor' ), 'color', array(), 'basfe_handle' ),
		array( 'knob_opacity', __( 'Knob background opacity', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 100, 'suffix' => '%' ), 'basfe_handle' ),
		array( 'icon_color', __( 'Arrow color', 'before-after-slider-for-elementor' ), 'color', array(), 'basfe_handle' ),
		array( 'line_width', __( 'Line width', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 1, 'max' => 12, 'suffix' => 'px' ), 'basfe_handle' ),
		array( 'knob_size', __( 'Knob size', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 20, 'max' => 90, 'suffix' => 'px' ), 'basfe_handle' ),

		array( 'label_color', __( 'Text color', 'before-after-slider-for-elementor' ), 'color', array(), 'basfe_labels' ),
		array( 'label_bg', __( 'Background color', 'before-after-slider-for-elementor' ), 'color', array(), 'basfe_labels' ),
		array( 'label_bg_opacity', __( 'Background opacity', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 100, 'suffix' => '%' ), 'basfe_labels' ),
		array( 'label_font_size', __( 'Font size', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 8, 'max' => 48, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_font_weight', __( 'Font weight', 'before-after-slider-for-elementor' ), 'select', $weights, 'basfe_labels' ),
		array( 'label_font_style', __( 'Font style', 'before-after-slider-for-elementor' ), 'select', array(
			'normal' => __( 'Normal', 'before-after-slider-for-elementor' ),
			'italic' => __( 'Italic', 'before-after-slider-for-elementor' ),
		), 'basfe_labels' ),
		array( 'label_pad_y', __( 'Padding (top / bottom)', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 50, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_pad_x', __( 'Padding (left / right)', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 50, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_top', __( 'Position: top', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 200, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_left', __( 'Position: left', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 200, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_right', __( 'Position: right', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 200, 'suffix' => 'px' ), 'basfe_labels' ),
		array( 'label_radius', __( 'Border radius', 'before-after-slider-for-elementor' ), 'number', array( 'min' => 0, 'max' => 50, 'suffix' => 'px' ), 'basfe_labels' ),
	);

	foreach ( $fields as $f ) {
		add_settings_field(
			'basfe_' . $f[0],
			$f[1],
			'basfe_render_field',
			'basfe-settings',
			$f[4],
			array(
				'key'   => $f[0],
				'type'  => $f[2],
				'extra' => $f[3],
			)
		);
	}
}

function basfe_section_intro() {
	echo '<p>' . esc_html__( 'These are the global defaults for every slider: the Elementor widget, the block and the shortcode. In Elementor you can override any style value per widget; leave a control empty there to use the global value.', 'before-after-slider-for-elementor' ) . '</p>';
}

function basfe_labels_intro() {
	echo '<p>' . esc_html__( 'In vertical mode the "After" label sits at the bottom, so "Position: top" is used as its bottom offset and "Position: left" as its left offset.', 'before-after-slider-for-elementor' ) . '</p>';
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
					selected( (string) $value, (string) $val, false ),
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
			if ( ! empty( $extra['suffix'] ) ) {
				echo ' ' . esc_html( $extra['suffix'] );
			}
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
