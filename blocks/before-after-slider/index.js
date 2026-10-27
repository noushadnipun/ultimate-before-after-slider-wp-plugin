( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var useEffect = wp.element.useEffect;
	var useRef = wp.element.useRef;
	var __ = wp.i18n.__;
	var be = wp.blockEditor;
	var c = wp.components;
	var useSelect = wp.data.useSelect;

	function ImagePicker( props ) {
		return el(
			be.MediaUploadCheck,
			null,
			el( be.MediaUpload, {
				allowedTypes: [ 'image' ],
				value: props.id,
				onSelect: props.onSelect,
				render: function ( obj ) {
					return el(
						c.Button,
						{ variant: props.url ? 'secondary' : 'primary', onClick: obj.open },
						props.url ? props.replaceLabel : props.label
					);
				},
			} )
		);
	}

	function Preview( props ) {
		var a = props.attributes;
		var vertical = a.orientation === 'vertical';
		var state = useState( a.offset );
		var pos = state[ 0 ];
		var setPos = state[ 1 ];
		var dragging = useRef( false );

		useEffect(
			function () {
				setPos( a.offset );
			},
			[ a.offset, a.orientation ]
		);

		function move( e ) {
			var r = e.currentTarget.getBoundingClientRect();
			var p = vertical ? ( e.clientY - r.top ) / r.height : ( e.clientX - r.left ) / r.width;
			setPos( Math.max( 0, Math.min( 100, p * 100 ) ) );
		}

		var style = { '--basfe-pos': pos + '%' };
		if ( props.autoWidth > 0 ) {
			style[ '--basfe-auto' ] = props.autoWidth + 'px';
		}
		if ( a.maxWidth > 0 ) {
			style[ '--basfe-max' ] = a.maxWidth + 'px';
		}
		var children = [
			el( 'div', { key: 'a', className: 'basfe-img basfe-after' }, el( 'img', { src: a.afterUrl, alt: '' } ) ),
			el( 'div', { key: 'b', className: 'basfe-img basfe-before' }, el( 'img', { src: a.beforeUrl, alt: '' } ) ),
		];

		if ( a.showLabels && a.beforeLabel ) {
			children.push( el( 'span', { key: 'lb', className: 'basfe-label basfe-label-before' }, a.beforeLabel ) );
		}
		if ( a.showLabels && a.afterLabel ) {
			children.push( el( 'span', { key: 'la', className: 'basfe-label basfe-label-after' }, a.afterLabel ) );
		}

		children.push(
			el(
				'div',
				{ key: 'h', className: 'basfe-handle' },
				el( 'span', { className: 'basfe-line' } ),
				el( 'span', { className: 'basfe-knob' } )
			)
		);

		return el(
			'div',
			{
				className: 'basfe',
				'data-orientation': a.orientation,
				'data-align': a.sliderAlign,
				style: style,
				onPointerDown: function ( e ) {
					dragging.current = true;
					try {
						e.currentTarget.setPointerCapture( e.pointerId );
					} catch ( err ) {}
					move( e );
				},
				onPointerMove: function ( e ) {
					if ( dragging.current ) {
						move( e );
					}
				},
				onPointerUp: function () {
					dragging.current = false;
				},
				onPointerCancel: function () {
					dragging.current = false;
				},
			},
			children
		);
	}

	function Edit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = be.useBlockProps();

		var imageSizes = useSelect( function ( select ) {
			var s = select( 'core/block-editor' ).getSettings();
			return s.imageSizes || [];
		}, [] );

		var autoWidth = useSelect(
			function ( select ) {
				if ( a.size === 'custom' ) {
					return a.customWidth > 0 ? a.customWidth : 0;
				}
				var core = select( 'core' );
				var widths = [];
				[ a.beforeId, a.afterId ].forEach( function ( id ) {
					if ( ! id ) {
						return;
					}
					var m = core.getMedia( id );
					if ( m && m.media_details ) {
						var sz = m.media_details.sizes && m.media_details.sizes[ a.size ];
						var w = sz ? sz.width : m.media_details.width;
						if ( w ) {
							widths.push( w );
						}
					}
				} );
				return widths.length ? Math.min.apply( null, widths ) : 0;
			},
			[ a.beforeId, a.afterId, a.size, a.customWidth ]
		);

		var sizeOptions = imageSizes
			.map( function ( s ) {
				return { label: s.name, value: s.slug };
			} )
			.concat( [
				{ label: __( 'Full Size', 'before-after-slider-for-elementor' ), value: 'full' },
				{ label: __( 'Custom', 'before-after-slider-for-elementor' ), value: 'custom' },
			] );

		var beforePicker = el( ImagePicker, {
			id: a.beforeId,
			url: a.beforeUrl,
			label: __( 'Select Before image', 'before-after-slider-for-elementor' ),
			replaceLabel: __( 'Replace Before image', 'before-after-slider-for-elementor' ),
			onSelect: function ( m ) {
				set( { beforeId: m.id, beforeUrl: m.url } );
			},
		} );

		var afterPicker = el( ImagePicker, {
			id: a.afterId,
			url: a.afterUrl,
			label: __( 'Select After image', 'before-after-slider-for-elementor' ),
			replaceLabel: __( 'Replace After image', 'before-after-slider-for-elementor' ),
			onSelect: function ( m ) {
				set( { afterId: m.id, afterUrl: m.url } );
			},
		} );

		var inspector = el(
			be.InspectorControls,
			null,
			el(
				c.PanelBody,
				{ title: __( 'Images', 'before-after-slider-for-elementor' ), initialOpen: true },
				el( 'div', { style: { marginBottom: '12px' } }, beforePicker ),
				el( 'div', null, afterPicker ),
				el( c.SelectControl, {
					label: __( 'Image size', 'before-after-slider-for-elementor' ),
					value: a.size,
					options: sizeOptions,
					onChange: function ( v ) {
						set( { size: v } );
					},
				} ),
				a.size === 'custom' &&
					el(
						Fragment,
						null,
						el( c.TextControl, {
							type: 'number',
							label: __( 'Width (px)', 'before-after-slider-for-elementor' ),
							value: a.customWidth,
							onChange: function ( v ) {
								set( { customWidth: parseInt( v, 10 ) || 0 } );
							},
						} ),
						el( c.TextControl, {
							type: 'number',
							label: __( 'Height (px)', 'before-after-slider-for-elementor' ),
							value: a.customHeight,
							onChange: function ( v ) {
								set( { customHeight: parseInt( v, 10 ) || 0 } );
							},
						} )
					)
			),
			el(
				c.PanelBody,
				{ title: __( 'Slider settings', 'before-after-slider-for-elementor' ), initialOpen: true },
				el( c.SelectControl, {
					label: __( 'Alignment', 'before-after-slider-for-elementor' ),
					value: a.sliderAlign,
					options: [
						{ label: __( 'Left', 'before-after-slider-for-elementor' ), value: 'left' },
						{ label: __( 'Center', 'before-after-slider-for-elementor' ), value: 'center' },
						{ label: __( 'Right', 'before-after-slider-for-elementor' ), value: 'right' },
					],
					onChange: function ( v ) {
						set( { sliderAlign: v } );
					},
				} ),
				el( c.TextControl, {
					type: 'number',
					label: __( 'Max width (px)', 'before-after-slider-for-elementor' ),
					help: __( 'Leave 0 to match the selected image size.', 'before-after-slider-for-elementor' ),
					value: a.maxWidth,
					onChange: function ( v ) {
						set( { maxWidth: parseInt( v, 10 ) || 0 } );
					},
				} ),
				el( c.SelectControl, {
					label: __( 'Orientation', 'before-after-slider-for-elementor' ),
					value: a.orientation,
					options: [
						{ label: __( 'Horizontal', 'before-after-slider-for-elementor' ), value: 'horizontal' },
						{ label: __( 'Vertical', 'before-after-slider-for-elementor' ), value: 'vertical' },
					],
					onChange: function ( v ) {
						set( { orientation: v } );
					},
				} ),
				el( c.RangeControl, {
					label: __( 'Starting position (%)', 'before-after-slider-for-elementor' ),
					value: a.offset,
					min: 0,
					max: 100,
					onChange: function ( v ) {
						set( { offset: v === undefined ? 50 : v } );
					},
				} ),
				el( c.ToggleControl, {
					label: __( 'Move on hover', 'before-after-slider-for-elementor' ),
					checked: a.hover,
					onChange: function ( v ) {
						set( { hover: v } );
					},
				} ),
				el( c.ToggleControl, {
					label: __( 'Show labels', 'before-after-slider-for-elementor' ),
					checked: a.showLabels,
					onChange: function ( v ) {
						set( { showLabels: v } );
					},
				} ),
				a.showLabels &&
					el(
						Fragment,
						null,
						el( c.TextControl, {
							label: __( 'Before label', 'before-after-slider-for-elementor' ),
							value: a.beforeLabel,
							onChange: function ( v ) {
								set( { beforeLabel: v } );
							},
						} ),
						el( c.TextControl, {
							label: __( 'After label', 'before-after-slider-for-elementor' ),
							value: a.afterLabel,
							onChange: function ( v ) {
								set( { afterLabel: v } );
							},
						} )
					)
			)
		);

		var body;
		if ( a.beforeUrl && a.afterUrl ) {
			body = el( Preview, { attributes: a, autoWidth: autoWidth } );
		} else {
			body = el(
				c.Placeholder,
				{
					icon: 'image-flip-horizontal',
					label: __( 'Before After Slider', 'before-after-slider-for-elementor' ),
					instructions: __( 'Choose the Before and After images to compare.', 'before-after-slider-for-elementor' ),
				},
				el( 'div', { style: { display: 'flex', gap: '8px', flexWrap: 'wrap' } }, beforePicker, afterPicker )
			);
		}

		return el( Fragment, null, inspector, el( 'div', blockProps, body ) );
	}

	wp.blocks.registerBlockType( 'basfe/before-after-slider', {
		edit: Edit,
		save: function () {
			return null;
		},
	} );
}( window.wp ) );
