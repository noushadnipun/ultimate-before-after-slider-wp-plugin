<?php
/**
 * Before/After widget.
 *
 * @package BASFE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

class Basfe_Widget extends Widget_Base {

	public function get_name() {
		return 'basfe_before_after';
	}

	public function get_title() {
		return esc_html__( 'Before After Slider', 'before-after-slider-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_categories() {
		return array( 'general' );
	}

	public function get_keywords() {
		return array( 'before', 'after', 'compare', 'comparison', 'slider', 'image' );
	}

	public function get_style_depends() {
		return array( 'basfe-style' );
	}

	public function get_script_depends() {
		return array( 'basfe-script' );
	}

	private function add_info_notice( $id ) {
		$this->add_control(
			$id,
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'%1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a>',
					esc_html__( 'Leave a control empty to use the global value from Settings.', 'before-after-slider-for-elementor' ),
					esc_url( admin_url( 'options-general.php?page=basfe-settings' ) ),
					esc_html__( 'Open global settings', 'before-after-slider-for-elementor' )
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
	}

	private function add_var_slider( $id, $label, $var, $min, $max, $unit = 'px' ) {
		$this->add_control(
			$id,
			array(
				'label'      => $label,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( $unit ),
				'range'      => array(
					$unit => array(
						'min' => $min,
						'max' => $max,
					),
				),
				'selectors'  => array( '{{WRAPPER}} .basfe' => $var . ': {{SIZE}}{{UNIT}};' ),
			)
		);
	}

	private function add_var_color( $id, $label, $var ) {
		$this->add_control(
			$id,
			array(
				'label'     => $label,
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .basfe' => $var . ': {{VALUE}};' ),
			)
		);
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_images',
			array(
				'label' => esc_html__( 'Images', 'before-after-slider-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'   => esc_html__( 'Before Image', 'before-after-slider-for-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'   => esc_html__( 'After Image', 'before-after-slider-for-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'image',
				'default' => 'large',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Settings', 'before-after-slider-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => esc_html__( 'Orientation', 'before-after-slider-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'before-after-slider-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'before-after-slider-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'      => esc_html__( 'Starting Position (%)', 'before-after-slider-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 50,
					'unit' => '%',
				),
			)
		);

		$this->add_control(
			'move_on_hover',
			array(
				'label'        => esc_html__( 'Move on Hover', 'before-after-slider-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => esc_html__( 'Show Labels', 'before-after-slider-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'before_label',
			array(
				'label'     => esc_html__( 'Before Label', 'before-after-slider-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Before', 'before-after-slider-for-elementor' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'after_label',
			array(
				'label'     => esc_html__( 'After Label', 'before-after-slider-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'After', 'before-after-slider-for-elementor' ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->end_controls_section();

		// Style: handle.
		$this->start_controls_section(
			'section_style_handle',
			array(
				'label' => esc_html__( 'Handle', 'before-after-slider-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_info_notice( 'handle_notice' );
		$this->add_var_color( 'handle_color', esc_html__( 'Line & Knob Color', 'before-after-slider-for-elementor' ), '--basfe-color' );
		$this->add_var_slider( 'knob_opacity', esc_html__( 'Knob Background Opacity (%)', 'before-after-slider-for-elementor' ), '--basfe-knob-opacity', 0, 100, '%' );
		$this->add_var_color( 'icon_color', esc_html__( 'Arrow Color', 'before-after-slider-for-elementor' ), '--basfe-icon' );
		$this->add_var_slider( 'line_width', esc_html__( 'Line Width', 'before-after-slider-for-elementor' ), '--basfe-line', 1, 12 );
		$this->add_var_slider( 'knob_size', esc_html__( 'Knob Size', 'before-after-slider-for-elementor' ), '--basfe-knob', 20, 90 );
		$this->end_controls_section();

		// Style: labels.
		$this->start_controls_section(
			'section_style_labels',
			array(
				'label'     => esc_html__( 'Labels', 'before-after-slider-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);
		$this->add_info_notice( 'labels_notice' );
		$this->add_var_color( 'label_color', esc_html__( 'Text Color', 'before-after-slider-for-elementor' ), '--basfe-label-color' );
		$this->add_var_color( 'label_bg', esc_html__( 'Background Color', 'before-after-slider-for-elementor' ), '--basfe-label-bg' );
		$this->add_var_slider( 'label_bg_opacity', esc_html__( 'Background Opacity (%)', 'before-after-slider-for-elementor' ), '--basfe-label-bg-opacity', 0, 100, '%' );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .basfe-label',
			)
		);
		$this->add_var_slider( 'label_pad_y', esc_html__( 'Padding Top / Bottom', 'before-after-slider-for-elementor' ), '--basfe-label-pad-y', 0, 50 );
		$this->add_var_slider( 'label_pad_x', esc_html__( 'Padding Left / Right', 'before-after-slider-for-elementor' ), '--basfe-label-pad-x', 0, 50 );
		$this->add_var_slider( 'label_top', esc_html__( 'Position: Top', 'before-after-slider-for-elementor' ), '--basfe-label-top', 0, 200 );
		$this->add_var_slider( 'label_left', esc_html__( 'Position: Left', 'before-after-slider-for-elementor' ), '--basfe-label-left', 0, 200 );
		$this->add_var_slider( 'label_right', esc_html__( 'Position: Right', 'before-after-slider-for-elementor' ), '--basfe-label-right', 0, 200 );
		$this->add_var_slider( 'label_radius', esc_html__( 'Border Radius', 'before-after-slider-for-elementor' ), '--basfe-label-radius', 0, 50 );
		$this->end_controls_section();

		// Style: image.
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => esc_html__( 'Image', 'before-after-slider-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slider_width',
			array(
				'label'       => esc_html__( 'Max Width', 'before-after-slider-for-elementor' ),
				'description' => esc_html__( 'Leave empty to match the selected image size. Use 100% to fill the column.', 'before-after-slider-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'vw' ),
				'range'       => array(
					'px' => array(
						'min' => 50,
						'max' => 2000,
					),
					'%'  => array(
						'min' => 5,
						'max' => 100,
					),
					'vw' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'   => array( '{{WRAPPER}} .basfe' => '--basfe-max: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'slider_align',
			array(
				'label'                => esc_html__( 'Alignment', 'before-after-slider-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'before-after-slider-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'before-after-slider-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'before-after-slider-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'margin-left:0;margin-right:auto;',
					'center' => 'margin-left:auto;margin-right:auto;',
					'right'  => 'margin-left:auto;margin-right:0;',
				),
				'selectors'            => array( '{{WRAPPER}} .basfe' => '{{VALUE}}' ),
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'before-after-slider-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .basfe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		if ( empty( $s['before_image']['url'] ) || empty( $s['after_image']['url'] ) ) {
			return;
		}

		$orientation = ( isset( $s['orientation'] ) && 'vertical' === $s['orientation'] ) ? 'vertical' : 'horizontal';
		$offset      = isset( $s['offset']['size'] ) ? max( 0, min( 100, (float) $s['offset']['size'] ) ) : 50;
		$hover       = ( isset( $s['move_on_hover'] ) && 'yes' === $s['move_on_hover'] ) ? 'yes' : 'no';
		$show_labels = ( isset( $s['show_labels'] ) && 'yes' === $s['show_labels'] );

		$before_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'before_image' );
		$after_html  = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'after_image' );

		$size_key = ! empty( $s['image_size'] ) ? $s['image_size'] : 'large';
		if ( 'custom' === $size_key ) {
			$auto = isset( $s['image_custom_dimension']['width'] ) ? (int) $s['image_custom_dimension']['width'] : 0;
		} else {
			$auto = basfe_auto_width(
				array(
					isset( $s['before_image']['id'] ) ? $s['before_image']['id'] : '',
					isset( $s['after_image']['id'] ) ? $s['after_image']['id'] : '',
				),
				$size_key
			);
		}

		$this->add_render_attribute(
			'wrap',
			array(
				'class'            => 'basfe',
				'data-orientation' => $orientation,
				'data-offset'      => $offset,
				'data-hover'       => $hover,
				'style'            => '--basfe-pos:' . $offset . '%;' . ( $auto > 0 ? '--basfe-auto:' . $auto . 'px;' : '' ),
			)
		);

		$this->add_render_attribute(
			'handle',
			array(
				'class'             => 'basfe-handle',
				'role'              => 'slider',
				'tabindex'          => '0',
				'aria-label'        => esc_attr__( 'Image comparison slider', 'before-after-slider-for-elementor' ),
				'aria-orientation'  => $orientation,
				'aria-valuemin'     => '0',
				'aria-valuemax'     => '100',
				'aria-valuenow'     => (string) round( $offset ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrap' ); ?>>
			<div class="basfe-img basfe-after"><?php echo wp_kses_post( $after_html ); ?></div>
			<div class="basfe-img basfe-before"><?php echo wp_kses_post( $before_html ); ?></div>
			<?php if ( $show_labels ) : ?>
				<span class="basfe-label basfe-label-before"><?php echo esc_html( $s['before_label'] ); ?></span>
				<span class="basfe-label basfe-label-after"><?php echo esc_html( $s['after_label'] ); ?></span>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string( 'handle' ); ?>>
				<span class="basfe-line"></span>
				<span class="basfe-knob"></span>
			</div>
		</div>
		<?php
	}
}
