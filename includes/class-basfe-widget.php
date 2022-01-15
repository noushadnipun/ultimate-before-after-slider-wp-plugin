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

	protected function register_controls() {
		$td = 'before-after-slider-for-elementor';

		$this->start_controls_section(
			'section_images',
			array(
				'label' => esc_html__( 'Images', $td ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'   => esc_html__( 'Before Image', $td ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'   => esc_html__( 'After Image', $td ),
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
				'label' => esc_html__( 'Settings', $td ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => esc_html__( 'Orientation', $td ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', $td ),
					'vertical'   => esc_html__( 'Vertical', $td ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'      => esc_html__( 'Starting Position (%)', $td ),
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
				'label'        => esc_html__( 'Move on Hover', $td ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => esc_html__( 'Show Labels', $td ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'before_label',
			array(
				'label'     => esc_html__( 'Before Label', $td ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Before', $td ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'after_label',
			array(
				'label'     => esc_html__( 'After Label', $td ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'After', $td ),
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->end_controls_section();

		// Style: handle.
		$this->start_controls_section(
			'section_style_handle',
			array(
				'label' => esc_html__( 'Handle', $td ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'handle_color',
			array(
				'label'     => esc_html__( 'Line & Knob Color', $td ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .basfe' => '--basfe-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Arrow Color', $td ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array( '{{WRAPPER}} .basfe' => '--basfe-icon: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'line_width',
			array(
				'label'      => esc_html__( 'Line Width', $td ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 12,
					),
				),
				'default'    => array( 'size' => 3 ),
				'selectors'  => array( '{{WRAPPER}} .basfe' => '--basfe-line: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'knob_size',
			array(
				'label'      => esc_html__( 'Knob Size', $td ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 90,
					),
				),
				'default'    => array( 'size' => 44 ),
				'selectors'  => array( '{{WRAPPER}} .basfe' => '--basfe-knob: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		// Style: labels.
		$this->start_controls_section(
			'section_style_labels',
			array(
				'label'     => esc_html__( 'Labels', $td ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Text Color', $td ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array( '{{WRAPPER}} .basfe-label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'label_bg',
			array(
				'label'     => esc_html__( 'Background', $td ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.55)',
				'selectors' => array( '{{WRAPPER}} .basfe-label' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .basfe-label',
			)
		);

		$this->end_controls_section();

		// Style: image.
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => esc_html__( 'Image', $td ),
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
				'label'      => esc_html__( 'Border Radius', $td ),
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
