( function () {
	'use strict';

	function init( el ) {
		if ( el.getAttribute( 'data-basfe-ready' ) ) {
			return;
		}
		el.setAttribute( 'data-basfe-ready', '1' );

		var vertical = el.getAttribute( 'data-orientation' ) === 'vertical';
		var hover = el.getAttribute( 'data-hover' ) === 'yes';
		var handle = el.querySelector( '.basfe-handle' );
		var pos = parseFloat( el.getAttribute( 'data-offset' ) );
		var dragging = false;

		if ( isNaN( pos ) ) {
			pos = 50;
		}

		function set( p ) {
			pos = Math.max( 0, Math.min( 100, p ) );
			el.style.setProperty( '--basfe-pos', pos + '%' );
			if ( handle ) {
				handle.setAttribute( 'aria-valuenow', Math.round( pos ) );
			}
		}

		function fromEvent( e ) {
			var r = el.getBoundingClientRect();
			var p = vertical ? ( e.clientY - r.top ) / r.height : ( e.clientX - r.left ) / r.width;
			set( p * 100 );
		}

		el.addEventListener( 'pointerdown', function ( e ) {
			if ( e.button > 0 ) {
				return;
			}
			dragging = true;
			try {
				el.setPointerCapture( e.pointerId );
			} catch ( err ) {}
			fromEvent( e );
		} );

		el.addEventListener( 'pointermove', function ( e ) {
			if ( dragging || ( hover && e.pointerType === 'mouse' ) ) {
				fromEvent( e );
			}
		} );

		[ 'pointerup', 'pointercancel' ].forEach( function ( type ) {
			el.addEventListener( type, function () {
				dragging = false;
			} );
		} );

		if ( handle ) {
			handle.addEventListener( 'keydown', function ( e ) {
				var step = e.shiftKey ? 10 : 2;
				var k = e.key;
				if ( k === 'ArrowLeft' || k === 'ArrowUp' ) {
					set( pos - step );
				} else if ( k === 'ArrowRight' || k === 'ArrowDown' ) {
					set( pos + step );
				} else if ( k === 'Home' ) {
					set( 0 );
				} else if ( k === 'End' ) {
					set( 100 );
				} else {
					return;
				}
				e.preventDefault();
			} );
		}

		set( pos );
	}

	function boot() {
		var items = document.querySelectorAll( '.basfe' );
		for ( var i = 0; i < items.length; i++ ) {
			init( items[ i ] );
		}
	}

	function registerElementorHook() {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/basfe_before_after.default',
				function ( $scope ) {
					var el = $scope[ 0 ] && $scope[ 0 ].querySelector( '.basfe' );
					if ( el ) {
						init( el );
					}
				}
			);
		}
	}

	window.addEventListener( 'elementor/frontend/init', registerElementorHook );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
