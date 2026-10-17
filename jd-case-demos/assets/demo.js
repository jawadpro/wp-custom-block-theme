( function () {
	'use strict';

	var d = document;
	var data;
	try {
		data = JSON.parse( d.getElementById( 'jdcd-data' ).textContent );
	} catch ( e ) {
		return;
	}
	var list = data.projects || [];
	if ( ! list.length ) {
		return;
	}
	var $ = function ( s, c ) {
		return ( c || d ).querySelector( s );
	};
	var $$ = function ( s, c ) {
		return Array.prototype.slice.call( ( c || d ).querySelectorAll( s ) );
	};

	var state = { i: data.current || 0, device: 'desktop', mode: 'live', shot: 0 };
	var device = $( '.jdcd-device' );
	var screen = $( '.jdcd-screen' );
	var frame = $( '.jdcd-frame' );
	var shots = $( '.jdcd-shots' );
	var shotImg = $( 'img', shots );
	var loading = $( '.jdcd-loading' );
	var thumbs = $( '.jdcd-thumbs' );
	var hint = $( '.jdcd-hint' );
	var loadTimer = 0;
	var widths = { desktop: 1440, tablet: 820, phone: 390 };

	function fit() {
		// Render the site at its real device width, then scale it down to fit the frame.
		var s = screen.clientWidth / widths[ state.device ];
		frame.style.setProperty( '--s', Math.min( 1, s ).toFixed( 4 ) );
		shots.style.setProperty( '--sh', screen.clientHeight + 'px' );
	}

	function press( group, value ) {
		$$( '[data-' + group + ']', $( '.jdcd-tools' ) ).forEach( function ( b ) {
			b.setAttribute( 'aria-pressed', String( b.getAttribute( 'data-' + group ) === value ) );
		} );
	}

	function showShot( n ) {
		var p = list[ state.i ];
		if ( ! p.shots.length ) {
			return;
		}
		state.shot = ( n + p.shots.length ) % p.shots.length;
		shots.classList.remove( 'is-scroll' );
		shotImg.src = p.shots[ state.shot ].src;
		shotImg.alt = p.shots[ state.shot ].alt || p.title;
		$$( 'button', thumbs ).forEach( function ( b, k ) {
			b.setAttribute( 'aria-pressed', String( k === state.shot ) );
		} );
		// Give a slow scroll-through of the full screenshot once it loads.
		shotImg.onload = function () {
			fit();
			setTimeout( function () {
				shots.classList.add( 'is-scroll' );
			}, 600 );
		};
	}

	function setMode( mode ) {
		var p = list[ state.i ];
		if ( 'live' === mode && ! p.url ) {
			mode = 'shots';
		}
		if ( 'shots' === mode && ! p.shots.length ) {
			mode = 'live';
		}
		state.mode = mode;
		press( 'mode', mode );
		clearTimeout( loadTimer );
		if ( 'live' === mode ) {
			shots.hidden = true;
			thumbs.hidden = true;
			frame.hidden = false;
			hint.hidden = ! p.shots.length;
			if ( frame.getAttribute( 'data-src' ) !== p.url ) {
				loading.classList.remove( 'is-done' );
				frame.setAttribute( 'data-src', p.url );
				frame.src = p.url;
				// Some sites refuse to be framed; after a while, offer the screenshots instead.
				loadTimer = setTimeout( function () {
					loading.classList.add( 'is-done' );
				}, 9000 );
			}
		} else {
			frame.hidden = true;
			shots.hidden = false;
			hint.hidden = true;
			loading.classList.add( 'is-done' );
			thumbs.hidden = p.shots.length < 2;
			showShot( state.shot );
		}
		$$( '[data-mode]' ).forEach( function ( b ) {
			var m = b.getAttribute( 'data-mode' );
			b.disabled = ( 'live' === m && ! p.url ) || ( 'shots' === m && ! p.shots.length );
		} );
	}

	function setDevice( dev ) {
		state.device = dev;
		device.setAttribute( 'data-device', dev );
		press( 'device', dev );
		// Wait for the frame resize transition before measuring.
		setTimeout( fit, 20 );
		setTimeout( fit, 650 );
	}

	function text( sel, value ) {
		var el = $( sel );
		el.textContent = value || '';
		el.hidden = ! value;
	}

	function render( push ) {
		var p = list[ state.i ];
		d.title = p.title + ' · Live demo';
		text( '.jdcd-scope', p.scope );
		text( '.jdcd-title', p.title );
		text( '.jdcd-client', p.client && p.client !== p.title ? p.client : '' );
		text( '.jdcd-summary', p.summary );
		$( '.jdcd-addr' ).textContent = p.url ? p.url.replace( /^https?:\/\//, '' ).replace( /\/$/, '' ) : p.title;

		var res = $( '.jdcd-results' );
		res.innerHTML = '';
		p.results.forEach( function ( r ) {
			var li = d.createElement( 'li' );
			var b = d.createElement( 'b' );
			var s = d.createElement( 'span' );
			b.textContent = r[ 0 ];
			s.textContent = r[ 1 ];
			li.appendChild( b );
			li.appendChild( s );
			res.appendChild( li );
		} );
		[ 'problem', 'did' ].forEach( function ( k ) {
			var box = $( '.jdcd-block[data-k="' + k + '"]' );
			$( 'p', box ).textContent = p[ k ] || '';
			box.hidden = ! p[ k ];
		} );

		var live = $( '.jdcd-live' );
		live.hidden = ! p.url;
		if ( p.url ) {
			live.href = p.url;
		}
		$( '.jdcd-case' ).href = p.case;
		$( '.jdcd-count' ).textContent = ( state.i + 1 ) + ' / ' + list.length;

		thumbs.innerHTML = '';
		p.shots.forEach( function ( sh, k ) {
			var b = d.createElement( 'button' );
			var img = d.createElement( 'img' );
			b.type = 'button';
			b.setAttribute( 'aria-label', sh.alt || 'Screenshot ' + ( k + 1 ) );
			img.src = sh.src;
			img.alt = '';
			img.loading = 'lazy';
			b.appendChild( img );
			b.addEventListener( 'click', function () {
				showShot( k );
			} );
			thumbs.appendChild( b );
		} );

		$$( '.jdcd-pills a' ).forEach( function ( a ) {
			if ( parseInt( a.getAttribute( 'data-i' ), 10 ) === state.i ) {
				a.setAttribute( 'aria-current', 'page' );
				a.scrollIntoView( { block: 'nearest', inline: 'center' } );
			} else {
				a.removeAttribute( 'aria-current' );
			}
		} );

		state.shot = 0;
		frame.removeAttribute( 'data-src' );
		setMode( p.url ? 'live' : 'shots' );
		fit();
		if ( push ) {
			history.pushState( { i: state.i }, '', data.base + p.slug + '/' );
		}
	}

	function go( n ) {
		state.i = ( n + list.length ) % list.length;
		render( true );
	}

	frame.addEventListener( 'load', function () {
		if ( frame.src ) {
			clearTimeout( loadTimer );
			loading.classList.add( 'is-done' );
		}
	} );
	$$( '[data-device]', $( '.jdcd-tools' ) ).forEach( function ( b ) {
		b.addEventListener( 'click', function () {
			setDevice( b.getAttribute( 'data-device' ) );
		} );
	} );
	$$( '[data-mode]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () {
			setMode( b.getAttribute( 'data-mode' ) );
		} );
	} );
	$( '.jdcd-to-shots' ).addEventListener( 'click', function () {
		setMode( 'shots' );
	} );
	$$( '.jdcd-pills a' ).forEach( function ( a ) {
		a.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			go( parseInt( a.getAttribute( 'data-i' ), 10 ) );
		} );
	} );
	$( '.jdcd-prev' ).addEventListener( 'click', function () {
		go( state.i - 1 );
	} );
	$( '.jdcd-next' ).addEventListener( 'click', function () {
		go( state.i + 1 );
	} );
	d.addEventListener( 'keydown', function ( e ) {
		if ( /input|textarea|select/i.test( e.target.tagName ) ) {
			return;
		}
		if ( 'ArrowRight' === e.key ) {
			go( state.i + 1 );
		} else if ( 'ArrowLeft' === e.key ) {
			go( state.i - 1 );
		}
	} );
	window.addEventListener( 'popstate', function ( e ) {
		if ( e.state && 'number' === typeof e.state.i ) {
			state.i = e.state.i;
			render( false );
		}
	} );
	window.addEventListener( 'resize', fit );

	// Start phones on the phone frame, so the preview is readable.
	if ( window.matchMedia( '(max-width: 700px)' ).matches ) {
		setDevice( 'phone' );
	}
	history.replaceState( { i: state.i }, '' );
	render( false );
}() );
