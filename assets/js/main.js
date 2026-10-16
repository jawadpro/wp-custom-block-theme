/**
 * Jawad theme front-end controller.
 * Everything degrades: without this file the site is fully readable and every link works.
 * Animation only runs when GSAP is loaded (Theme Options > Motion) and the visitor allows motion.
 */
( function () {
	'use strict';

	var d = document;
	var html = d.documentElement;
	var cfg = window.JD || {};
	var t = cfg.i18n || {};
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var fine = window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches;
	var G = window.gsap;
	var ST = window.ScrollTrigger;
	var motion = !! ( cfg.motion && G && ST && ! reduce );
	var lenis = null;

	function $( sel, root ) {
		return ( root || d ).querySelector( sel );
	}
	function $$( sel, root ) {
		return Array.prototype.slice.call( ( root || d ).querySelectorAll( sel ) );
	}
	function safe( name, fn ) {
		try {
			fn();
		} catch ( e ) {
			if ( window.console ) {
				console.warn( '[jd] ' + name, e ); // eslint-disable-line no-console
			}
		}
	}

	if ( motion ) {
		G.registerPlugin( ST );
	}

	/* Smooth scroll ------------------------------------------------------- */
	safe( 'lenis', function () {
		if ( ! motion || ! cfg.smooth || ! window.Lenis ) {
			return;
		}
		lenis = new window.Lenis( {
			duration: 1.15,
			easing: function ( x ) {
				return Math.min( 1, 1.001 - Math.pow( 2, -10 * x ) );
			},
			smoothWheel: true,
		} );
		lenis.on( 'scroll', ST.update );
		G.ticker.add( function ( time ) {
			lenis.raf( time * 1000 );
		} );
		G.ticker.lagSmoothing( 0 );
	} );

	function scrollToEl( el ) {
		if ( ! el ) {
			return;
		}
		if ( lenis ) {
			lenis.scrollTo( el, { offset: -100 } );
		} else {
			el.scrollIntoView( { behavior: reduce ? 'auto' : 'smooth', block: 'start' } );
		}
	}

	// Same-page anchor links.
	d.addEventListener( 'click', function ( e ) {
		var a = e.target.closest ? e.target.closest( 'a[href*="#"]' ) : null;
		if ( ! a || a.hasAttribute( 'data-filter' ) ) {
			return;
		}
		var url;
		try {
			url = new URL( a.href, location.href );
		} catch ( err ) {
			return;
		}
		if ( url.pathname !== location.pathname || ! url.hash || url.hash.length < 2 ) {
			return;
		}
		var target = d.getElementById( decodeURIComponent( url.hash.slice( 1 ) ) );
		if ( target ) {
			e.preventDefault();
			scrollToEl( target );
			try {
				history.replaceState( null, '', url.hash );
			} catch ( err ) {}
		}
	} );

	/* Nav: hide on scroll down, show on scroll up ------------------------- */
	safe( 'nav', function () {
		var nav = $( '.jd-nav' );
		if ( ! nav ) {
			return;
		}
		var last = 0;
		function onScroll( y ) {
			if ( html.classList.contains( 'is-menu-open' ) ) {
				return;
			}
			nav.classList.toggle( 'is-hidden', y > last && y > 240 );
			last = y;
		}
		if ( lenis ) {
			lenis.on( 'scroll', function ( e ) {
				onScroll( e.scroll );
			} );
		} else {
			window.addEventListener(
				'scroll',
				function () {
					onScroll( window.scrollY );
				},
				{ passive: true }
			);
		}
		// Keyboard users always see the nav.
		nav.addEventListener( 'focusin', function () {
			nav.classList.remove( 'is-hidden' );
		} );
	} );

	/* Overlay menu -------------------------------------------------------- */
	safe( 'menu', function () {
		var btn = $( '.jd-burger' );
		var overlay = $( '#jd-overlay' );
		if ( ! btn || ! overlay ) {
			return;
		}
		// The nav uses transform + backdrop-filter, which would trap a fixed child. Move the overlay out.
		d.body.appendChild( overlay );
		var closeTimer = null;

		function setOrigin() {
			var r = btn.getBoundingClientRect();
			overlay.style.setProperty( '--ox', r.left + r.width / 2 + 'px' );
			overlay.style.setProperty( '--oy', r.top + r.height / 2 + 'px' );
		}
		function open() {
			clearTimeout( closeTimer );
			setOrigin();
			overlay.hidden = false;
			void overlay.offsetWidth; // Commit the closed state so the reveal transitions.
			overlay.classList.add( 'is-open' );
			html.classList.add( 'is-menu-open' );
			btn.setAttribute( 'aria-expanded', 'true' );
			btn.setAttribute( 'aria-label', btn.getAttribute( 'data-close' ) || 'Close menu' );
			var nav = $( '.jd-nav' );
			if ( nav ) {
				nav.classList.remove( 'is-hidden' );
			}
			if ( lenis ) {
				lenis.stop();
			}
			if ( motion ) {
				G.fromTo( $$( '.jd-overlay__t', overlay ), { yPercent: 110 }, { yPercent: 0, duration: 0.9, stagger: 0.06, ease: 'expo.out', delay: 0.2, overwrite: true } );
				G.fromTo( overlay.querySelectorAll( '.jd-overlay__aside > *' ), { y: 20, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.07, ease: 'expo.out', delay: 0.35, overwrite: true } );
			}
			var first = $( '.jd-overlay__nav a', overlay );
			if ( first ) {
				setTimeout( function () {
					first.focus( { preventScroll: true } );
				}, 60 );
			}
		}
		function close( instant ) {
			if ( ! html.classList.contains( 'is-menu-open' ) ) {
				return;
			}
			setOrigin();
			overlay.classList.remove( 'is-open' );
			html.classList.remove( 'is-menu-open' );
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', btn.getAttribute( 'data-open' ) || 'Open menu' );
			if ( lenis ) {
				lenis.start();
			}
			clearTimeout( closeTimer );
			closeTimer = setTimeout( function () {
				overlay.hidden = true;
			}, instant || reduce ? 0 : 800 );
		}
		btn.setAttribute( 'data-open', btn.getAttribute( 'aria-label' ) || 'Open menu' );
		btn.setAttribute( 'data-close', btn.getAttribute( 'data-close' ) || 'Close menu' );
		btn.addEventListener( 'click', function () {
			if ( html.classList.contains( 'is-menu-open' ) ) {
				close();
			} else {
				open();
			}
		} );
		d.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && html.classList.contains( 'is-menu-open' ) ) {
				close();
				btn.focus();
			}
		} );
		overlay.addEventListener( 'click', function ( e ) {
			var a = e.target.closest( 'a' );
			if ( a ) {
				// Let the navigation happen; hide instantly so back/forward cache never restores an open menu.
				close( true );
			}
		} );
		window.addEventListener( 'pageshow', function () {
			close( true );
		} );
	} );

	/* Split display headings into lines (on <br>) --------------------------- */
	safe( 'split', function () {
		$$( '.jd-split' ).forEach( function ( h ) {
			var parts = h.innerHTML.split( /<br\s*\/?>/i );
			h.innerHTML = parts
				.map( function ( part ) {
					return '<span class="jd-ln"><span>' + part + '</span></span>';
				} )
				.join( '' );
			h.classList.add( 'is-split' );
			if ( motion ) {
				G.from( h.querySelectorAll( '.jd-ln > span' ), { yPercent: 110, duration: 1.15, stagger: 0.09, ease: 'expo.out', delay: 0.1 } );
			}
		} );
		// Underline the highlighted hero word.
		$$( '.jd-hero__title strong' ).forEach( function ( s ) {
			s.insertAdjacentHTML( 'beforeend', '<svg class="jd-hl__line" viewBox="0 0 200 20" preserveAspectRatio="none" aria-hidden="true"><path d="M3 14 C 50 4, 120 4, 197 10" pathLength="1"/></svg>' );
			var path = s.querySelector( 'path' );
			if ( motion && path ) {
				G.fromTo( path, { strokeDasharray: 1, strokeDashoffset: 1 }, { strokeDashoffset: 0, duration: 1.1, ease: 'power3.inOut', delay: 0.75 } );
			}
		} );
	} );

	/* Speed ring ---------------------------------------------------------- */
	safe( 'ring', function () {
		if ( ! motion ) {
			return;
		}
		$$( '.jd-ring' ).forEach( function ( ring ) {
			var to = parseInt( ring.getAttribute( 'data-value' ), 10 ) || 0;
			var label = ring.querySelector( 'b' );
			var o = { v: 0 };
			G.to( o, {
				v: to,
				duration: 2,
				ease: 'expo.out',
				delay: 0.5,
				scrollTrigger: { trigger: ring, start: 'top 95%' },
				onUpdate: function () {
					ring.style.setProperty( '--v', Math.round( o.v ) );
					label.textContent = Math.round( o.v );
				},
			} );
		} );
	} );

	/* Hero dot field ------------------------------------------------------ */
	safe( 'field', function () {
		var screen = $( '.jd-screen' );
		if ( ! screen || ! cfg.field || ! cfg.motion || reduce ) {
			return;
		}
		var cv = d.createElement( 'canvas' );
		cv.className = 'jd-dotfield';
		cv.setAttribute( 'aria-hidden', 'true' );
		screen.insertBefore( cv, screen.firstChild );
		var ctx = cv.getContext( '2d' );
		var W = 0, H = 0, mx = -999, my = -999, tick = 0, visible = true;
		function size() {
			var r = cv.getBoundingClientRect();
			var dpr = Math.min( 2, window.devicePixelRatio || 1 );
			W = r.width;
			H = r.height;
			cv.width = W * dpr;
			cv.height = H * dpr;
			ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
		}
		function draw() {
			ctx.clearRect( 0, 0, W, H );
			var gap = W < 700 ? 24 : 30;
			var tt = tick * 0.012;
			for ( var y = gap / 2; y < H; y += gap ) {
				for ( var x = gap / 2; x < W; x += gap ) {
					var w = Math.sin( x * 0.006 + tt ) + Math.cos( y * 0.008 - tt * 0.8 ) + Math.sin( ( x + y ) * 0.003 + tt * 0.6 );
					var dist = Math.hypot( x - mx, y - my );
					var m = Math.max( 0, 1 - dist / 220 );
					var k = Math.max( 0, ( w + 1.4 ) / 4.4 ) * ( 0.35 + 0.65 * ( x / W ) ) + m * 0.9;
					var r = 0.6 + k * 2.1;
					var oy = Math.sin( x * 0.01 + tt ) * 3 - m * 10 * ( y > my ? 1 : -1 );
					ctx.fillStyle = m > 0.25 ? 'rgba(92,242,200,' + ( 0.25 + m * 0.7 ) + ')' : 'rgba(91,116,255,' + ( 0.12 + k * 0.55 ) + ')';
					ctx.beginPath();
					ctx.arc( x, y + oy, r, 0, 6.283 );
					ctx.fill();
				}
			}
		}
		function loop() {
			if ( visible && ! d.hidden ) {
				tick++;
				draw();
			}
			window.requestAnimationFrame( loop );
		}
		size();
		draw();
		window.addEventListener( 'resize', function () {
			size();
			draw();
		} );
		screen.addEventListener( 'pointermove', function ( e ) {
			var r = cv.getBoundingClientRect();
			mx = e.clientX - r.left;
			my = e.clientY - r.top;
		} );
		screen.addEventListener( 'pointerleave', function () {
			mx = my = -999;
		} );
		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver( function ( entries ) {
				visible = entries[ 0 ].isIntersecting;
			} ).observe( cv );
		}
		window.requestAnimationFrame( loop );
	} );

	/* Count-up numbers ("500+") ------------------------------------------- */
	safe( 'counts', function () {
		$$( '.jd-stat__n' ).forEach( function ( el ) {
			var m = /^\s*(\d+)(.*)$/.exec( el.textContent );
			if ( ! m ) {
				return;
			}
			var to = parseInt( m[ 1 ], 10 );
			el.innerHTML = '<span class="jd-num">' + m[ 1 ] + '</span><span class="jd-suffix">' + m[ 2 ].replace( /[<>&]/g, '' ) + '</span>';
			if ( ! motion ) {
				return;
			}
			var num = el.querySelector( '.jd-num' );
			var o = { v: 0 };
			G.to( o, {
				v: to,
				duration: 1.8,
				ease: 'expo.out',
				scrollTrigger: { trigger: el, start: 'top 92%' },
				onUpdate: function () {
					num.textContent = Math.round( o.v );
				},
			} );
		} );
	} );

	/* Statement lights up word by word -------------------------------------- */
	safe( 'fill', function () {
		if ( ! motion ) {
			return;
		}
		$$( '.jd-fill' ).forEach( function ( p ) {
			var words = [];
			Array.prototype.slice.call( p.childNodes ).forEach( function ( n ) {
				if ( 3 === n.nodeType ) {
					var frag = d.createDocumentFragment();
					n.textContent.split( /(\s+)/ ).forEach( function ( s ) {
						if ( ! s ) {
							return;
						}
						if ( /^\s+$/.test( s ) ) {
							frag.appendChild( d.createTextNode( s ) );
							return;
						}
						var sp = d.createElement( 'span' );
						sp.textContent = s;
						frag.appendChild( sp );
						words.push( sp );
					} );
					p.replaceChild( frag, n );
				} else if ( 1 === n.nodeType ) {
					words.push( n );
				}
			} );
			G.set( words, { color: '#3A4466' } );
			G.to( words, {
				color: function ( i, el ) {
					return 'EM' === el.tagName ? '#5CF2C8' : '#F2F4FA';
				},
				stagger: 0.1,
				ease: 'none',
				scrollTrigger: { trigger: p, start: 'top 80%', end: 'bottom 45%', scrub: true },
			} );
		} );
	} );

	/* Section reveals ----------------------------------------------------- */
	safe( 'reveals', function () {
		if ( ! motion ) {
			return;
		}
		$$( '.jd-section__title, .jd-cta__title, .jd-reveal' ).forEach( function ( el ) {
			G.from( el, { y: 50, autoAlpha: 0, duration: 1.1, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 90%' } } );
		} );
		$$( '.jd-plans, .jd-princ, .jd-pay' ).forEach( function ( wrap ) {
			G.from( wrap.children, { y: 60, autoAlpha: 0, stagger: 0.1, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: wrap, start: 'top 85%' } } );
		} );
		$$( '.jd-steps' ).forEach( function ( steps ) {
			// The line is a CSS pseudo-element; scroll position drives its fill (0 to 1).
			steps.style.setProperty( '--progress', '0' );
			ST.create( {
				trigger: steps,
				start: 'top 85%',
				end: 'top 35%',
				scrub: true,
				onUpdate: function ( self ) {
					steps.style.setProperty( '--progress', self.progress.toFixed( 4 ) );
				},
				onRefresh: function ( self ) {
					steps.style.setProperty( '--progress', self.progress.toFixed( 4 ) );
				},
			} );
			G.from( $$( ':scope > .wp-block-group', steps ), { y: 40, autoAlpha: 0, stagger: 0.12, duration: 0.9, ease: 'expo.out', scrollTrigger: { trigger: steps, start: 'top 80%' } } );
		} );
		// Featured image opens out from a rounded window and settles its zoom as you scroll in.
		$$( '.jd-feature-img' ).forEach( function ( fig ) {
			var img = $( 'img', fig );
			if ( ! img ) {
				return;
			}
			var st = { trigger: fig, start: 'top 95%', end: 'top 30%', scrub: true };
			G.fromTo( fig, { clipPath: 'inset(14% 7% 0% 7% round 32px)' }, { clipPath: 'inset(0% 0% 0% 0% round 32px)', ease: 'none', scrollTrigger: st } );
			G.fromTo( img, { scale: 1.18 }, { scale: 1, ease: 'none', scrollTrigger: Object.assign( {}, st ) } );
		} );
		$$( '.jd-portrait' ).forEach( function ( el ) {
			G.from( el, { clipPath: 'inset(100% 0 0 0)', duration: 1.4, ease: 'expo.inOut', delay: 0.2 } );
		} );
		$$( '.jd-cta' ).forEach( function ( cta ) {
			var o = { x: 0, y: 0 };
			G.to( o, {
				x: -120,
				y: 120,
				ease: 'none',
				scrollTrigger: { trigger: cta, start: 'top bottom', end: 'bottom top', scrub: true },
				onUpdate: function () {
					cta.style.setProperty( '--orb-x', o.x + 'px' );
					cta.style.setProperty( '--orb-y', o.y + 'px' );
				},
			} );
		} );
		$$( '.jd-wordmark' ).forEach( function ( w ) {
			G.from( w.children, { yPercent: 100, stagger: 0.035, ease: 'none', scrollTrigger: { trigger: w, start: 'top bottom', end: 'bottom 95%', scrub: true } } );
		} );
	} );

	/* Footer wordmark: size the name to fill its container exactly ------------- */
	safe( 'wordmark-fit', function () {
		var marks = $$( '.jd-wordmark' );
		if ( ! marks.length ) {
			return;
		}
		function fit() {
			marks.forEach( function ( w ) {
				w.style.fontSize = '100px';
				var a = w.firstElementChild;
				var z = w.lastElementChild;
				var natural = a && z ? z.getBoundingClientRect().right - a.getBoundingClientRect().left : w.scrollWidth;
				var avail = w.clientWidth;
				if ( natural > 0 && avail > 0 ) {
					w.style.fontSize = Math.min( 340, Math.floor( ( 100 * avail ) / natural * 0.995 ) ) + 'px';
				}
			} );
		}
		fit();
		if ( d.fonts && d.fonts.ready ) {
			d.fonts.ready.then( fit );
		}
		var t;
		window.addEventListener( 'resize', function () {
			clearTimeout( t );
			t = setTimeout( fit, 120 );
		} );
	} );

	/* Hero screen shrinks as you leave it --------------------------------- */
	safe( 'screen', function () {
		if ( ! motion ) {
			return;
		}
		$$( '.jd-screen' ).forEach( function ( screen ) {
			var inner = $( ':scope > .jd-wrap', screen );
			if ( inner ) {
				G.to( inner, { yPercent: 12, opacity: 0.35, ease: 'none', scrollTrigger: { trigger: screen, start: 'top top', end: 'bottom top', scrub: true } } );
			}
			G.to( screen, { scale: 0.94, borderRadius: 48, ease: 'none', scrollTrigger: { trigger: screen, start: 'center center', end: 'bottom top', scrub: true } } );
		} );
	} );

	/* Service cards stack; work track scrolls sideways --------------------- */
	safe( 'pins', function () {
		if ( ! motion ) {
			return;
		}
		ST.matchMedia( {
			'(min-width: 701px) and (min-height: 720px)': function () {
				$$( '.jd-stack' ).forEach( function ( stack ) {
					var cards = $$( '.jd-scard', stack );
					cards.forEach( function ( card, i ) {
						if ( i === cards.length - 1 ) {
							return;
						}
						var next = cards[ i + 1 ];
						// Start only when the next card reaches this card's lower half; finish as it locks on top.
						// Before that, this card stays at full brightness and size, so it is always readable.
						var startAt = function () {
							return 'top ' + Math.round( card.getBoundingClientRect().height * 0.5 + parseFloat( getComputedStyle( card ).top || 0 ) ) + 'px';
						};
						var endAt = function () {
							return 'top ' + Math.round( parseFloat( getComputedStyle( next ).top || 0 ) ) + 'px';
						};
						var o = { d: 0 };
						G.timeline( {
							scrollTrigger: { trigger: next, start: startAt, end: endAt, scrub: true, invalidateOnRefresh: true },
						} )
							.to( card, { scale: 0.94, ease: 'none' }, 0 )
							.to( o, { d: 0.55, ease: 'none', onUpdate: function () {
								card.style.setProperty( '--dim', o.d.toFixed( 3 ) );
							} }, 0 );
					} );
				} );
			},
			'(min-width: 821px)': function () {
				$$( '.jd-hwork' ).forEach( function ( section ) {
					var track = $( '.jd-track', section );
					if ( ! track ) {
						return;
					}
					var dist = function () {
						return Math.max( 0, track.scrollWidth - window.innerWidth );
					};
					if ( dist() < 40 ) {
						return;
					}
					G.to( track, {
						x: function () {
							return -dist();
						},
						ease: 'none',
						// Pin only once the whole row (cards, titles and descriptions) is on screen.
						scrollTrigger: { trigger: $( '.jd-track-wrap', section ) || section, pin: section, start: 'bottom bottom-=32', end: function () {
							return '+=' + dist();
						}, scrub: 1, invalidateOnRefresh: true, anticipatePin: 1 },
					} );
				} );
			},
		} );
	} );

	/* Marquee reacts to scroll speed --------------------------------------- */
	safe( 'marquee', function () {
		if ( ! motion ) {
			return;
		}
		$$( '.jd-marquee__track' ).forEach( function ( track ) {
			var x = 0, v = 0, lastY = window.scrollY, onScreen = true;
			if ( lenis ) {
				lenis.on( 'scroll', function ( e ) {
					v = e.velocity || 0;
				} );
			}
			if ( 'IntersectionObserver' in window ) {
				new IntersectionObserver( function ( en ) {
					onScreen = en[ 0 ].isIntersecting;
				} ).observe( track );
			}
			function step() {
				if ( ! lenis ) {
					v = ( window.scrollY - lastY ) * 0.5;
					lastY = window.scrollY;
				}
				if ( onScreen && ! d.hidden ) {
					x -= 0.6 + Math.abs( v ) * 0.25;
					var half = track.scrollWidth / 2;
					if ( -x >= half ) {
						x += half;
					}
					track.style.transform = 'translateX(' + x + 'px) skewX(' + Math.max( -12, Math.min( 12, -v * 0.5 ) ) + 'deg)';
				}
				v *= 0.9;
				window.requestAnimationFrame( step );
			}
			window.requestAnimationFrame( step );
		} );
	} );

	/* Reviews slider -------------------------------------------------------- */
	safe( 'reviews', function () {
		$$( '.jd-reviews' ).forEach( function ( wrap ) {
			var slides = $$( '.jd-review', wrap );
			var count = $( '.jd-reviews__count', wrap );
			if ( slides.length < 2 ) {
				return;
			}
			wrap.classList.add( 'is-ready' );
			var i = 0;
			function pad( n ) {
				return ( n < 10 ? '0' : '' ) + n;
			}
			function show( dir ) {
				var prev = slides[ i ];
				i = ( i + dir + slides.length ) % slides.length;
				var next = slides[ i ];
				prev.classList.remove( 'is-on' );
				prev.setAttribute( 'aria-hidden', 'true' );
				next.classList.add( 'is-on' );
				next.removeAttribute( 'aria-hidden' );
				if ( count ) {
					count.textContent = pad( i + 1 ) + ' / ' + pad( slides.length );
				}
				if ( motion ) {
					G.fromTo( next.querySelector( '.jd-review__q' ), { yPercent: 20 * dir, autoAlpha: 0 }, { yPercent: 0, autoAlpha: 1, duration: 0.6, ease: 'expo.out', overwrite: true } );
				}
			}
			$$( '.jd-rbtn', wrap ).forEach( function ( b ) {
				b.addEventListener( 'click', function () {
					show( parseInt( b.getAttribute( 'data-dir' ), 10 ) || 1 );
				} );
			} );
		} );
	} );

	/* Work filters (progressive: links go to real term archives) ----------- */
	safe( 'filters', function () {
		$$( '.jd-work' ).forEach( function ( work ) {
			var chips = $$( '.jd-filters [data-filter]', work );
			var cards = $$( '.jd-gcard', work );
			if ( ! chips.length ) {
				return;
			}
			chips.forEach( function ( chip ) {
				chip.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					var f = chip.getAttribute( 'data-filter' );
					chips.forEach( function ( c ) {
						var on = c === chip;
						c.classList.toggle( 'is-on', on );
						if ( on ) {
							c.setAttribute( 'aria-current', 'page' );
						} else {
							c.removeAttribute( 'aria-current' );
						}
					} );
					var shown = [];
					cards.forEach( function ( card ) {
						var cats = ( card.getAttribute( 'data-cats' ) || '' ).split( ' ' );
						var match = '*' === f || cats.indexOf( f ) !== -1;
						card.hidden = ! match;
						if ( match ) {
							shown.push( card );
						}
					} );
					if ( motion && shown.length ) {
						G.fromTo( shown, { y: 40, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.8, stagger: 0.06, ease: 'expo.out', overwrite: true } );
					}
					try {
						history.replaceState( null, '', chip.href );
					} catch ( err ) {}
					if ( ST ) {
						ST.refresh();
					}
				} );
			} );
		} );
	} );

	/* Quote form ------------------------------------------------------------ */
	safe( 'form', function () {
		$$( '.jd-form' ).forEach( function ( form ) {
			if ( 'FORM' !== form.tagName ) {
				return;
			}
			var steps = $$( '.jd-step', form );
			var bars = $$( '.jd-prog i', form );
			var next = $( '.jd-form__next', form );
			var back = $( '.jd-form__back', form );
			var err = $( '.jd-err', form );
			var cur = 1;
			form.classList.add( 'is-stepped' );
			function show( n ) {
				cur = n;
				steps.forEach( function ( s ) {
					s.classList.toggle( 'is-on', parseInt( s.getAttribute( 'data-step' ), 10 ) === n );
				} );
				bars.forEach( function ( b, idx ) {
					b.classList.toggle( 'is-on', idx < n );
				} );
				back.hidden = n === 1;
				next.textContent = n === steps.length ? t.send || 'Send request' : t.next || 'Continue';
				next.type = n === steps.length ? 'submit' : 'button';
				err.textContent = '';
				if ( motion ) {
					G.from( $( '.jd-step.is-on', form ), { x: 30, autoAlpha: 0, duration: 0.5, ease: 'expo.out' } );
				}
			}
			show( 1 );
			next.addEventListener( 'click', function ( e ) {
				if ( cur < steps.length ) {
					e.preventDefault();
					show( cur + 1 );
					var focusable = $( '.jd-step.is-on input', form );
					if ( focusable ) {
						focusable.focus( { preventScroll: true } );
					}
				}
			} );
			back.addEventListener( 'click', function () {
				show( Math.max( 1, cur - 1 ) );
			} );
			form.addEventListener( 'submit', function ( e ) {
				e.preventDefault();
				if ( cur < steps.length ) {
					show( cur + 1 );
					return;
				}
				var name = form.elements.name.value.trim();
				var email = form.elements.email.value.trim();
				if ( ! name ) {
					err.textContent = 'Add your name so I know who to reply to.';
					form.elements.name.focus();
					return;
				}
				if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email ) ) {
					err.textContent = 'That email looks incomplete. Check it and try again.';
					form.elements.email.focus();
					return;
				}
				next.disabled = true;
				next.textContent = t.sending || 'Sending…';
				fetch( form.action, { method: 'POST', body: new FormData( form ), headers: { Accept: 'application/json' }, credentials: 'same-origin' } )
					.then( function ( r ) {
						return r.json().catch( function () {
							return { success: false };
						} );
					} )
					.then( function ( res ) {
						if ( res && res.success ) {
							var done = d.createElement( 'div' );
							done.className = 'jd-form jd-form--done';
							done.setAttribute( 'role', 'status' );
							done.innerHTML = '<p class="jd-form__title"></p><p class="jd-muted"></p>';
							done.firstChild.textContent = 'Request received';
							done.lastChild.textContent = ( res.data && res.data.message ) || '';
							form.replaceWith( done );
							done.setAttribute( 'tabindex', '-1' );
							done.focus();
						} else {
							next.disabled = false;
							next.textContent = t.send || 'Send request';
							err.textContent = ( res && res.data && res.data.message ) || t.error || 'Something went wrong.';
						}
					} )
					.catch( function () {
						next.disabled = false;
						next.textContent = t.send || 'Send request';
						err.textContent = t.error || 'Something went wrong.';
					} );
			} );
		} );
	} );

	/* Cost estimator -------------------------------------------------------- */
	safe( 'estimator', function () {
		$$( '.jd-est' ).forEach( function ( est ) {
			var c;
			try {
				c = JSON.parse( est.getAttribute( 'data-config' ) );
			} catch ( e ) {
				return;
			}
			var form = $( '.jd-est__form', est );
			var cur = est.getAttribute( 'data-currency' ) || '$';
			var quote = est.getAttribute( 'data-quote' );
			var priceEl = $( '.jd-est__price', est );
			var timeEl = $( '.jd-est__time', est );
			var careEl = $( '.jd-est__care', est );
			var list = $( '.jd-est__list', est );
			var cta = $( '.jd-est__cta', est );
			var shown = { lo: 0, hi: 0 };
			var i18n = c.i18n || {};

			function money( n ) {
				return cur + Math.round( n ).toLocaleString( 'en-US' );
			}
			function fmt( str ) {
				var args = Array.prototype.slice.call( arguments, 1 );
				return str.replace( /%(\d)\$s|%s/g, function ( m, n ) {
					return n ? args[ n - 1 ] : args[ 0 ];
				} );
			}
			function val( name ) {
				var el = form.querySelector( 'input[name="' + name + '"]:checked' );
				return el ? el.value : '';
			}
			function paint( lo, hi ) {
				priceEl.textContent = money( lo ) + '–' + Math.round( hi ).toLocaleString( 'en-US' );
			}
			function calc() {
				var type = c.types[ val( 'type' ) ];
				if ( ! type ) {
					return;
				}
				var size = type.sized ? c.sizes[ val( 'size' ) ] : null;
				var design = type.designed ? c.designs[ val( 'design' ) ] : null;
				var pace = c.paces[ val( 'pace' ) ];
				var care = c.care[ val( 'care' ) ];
				var x = ( size ? size.x : 1 ) * ( design ? design.x : 1 );
				var lo = type.min * x;
				var hi = type.max * x;
				var lines = [ type.label + ( size ? ', ' + size.label.toLowerCase() : '' ) ];
				if ( design ) {
					lines.push( design.label );
				}
				$$( 'input[name="extras[]"]:checked', form ).forEach( function ( box ) {
					var ex = c.extras[ box.value ];
					if ( ex ) {
						lo += ex.min;
						hi += ex.max;
						lines.push( ex.label );
					}
				} );
				if ( pace ) {
					lo *= pace.x;
					hi *= pace.x;
					if ( pace.x > 1 ) {
						lines.push( i18n.rush || 'Rush timeline' );
					}
				}
				lo = Math.floor( lo / 50 ) * 50;
				hi = Math.ceil( hi / 50 ) * 50;

				var wx = ( size ? size.wx : 1 ) * ( design ? design.wx : 1 );
				var w0 = type.weeks[ 0 ] * wx;
				var w1 = type.weeks[ 1 ] * wx;
				if ( pace && pace.x > 1 ) {
					w0 *= 0.7;
					w1 *= 0.7;
				}
				var time = w1 < 1.5
					? fmt( i18n.days || 'About %1$s–%2$s days', Math.max( 2, Math.round( w0 * 5 ) ), Math.max( 3, Math.round( w1 * 5 ) ) )
					: fmt( i18n.weeks || 'About %1$s–%2$s weeks', Math.max( 1, Math.round( w0 ) ), Math.max( 2, Math.round( w1 ) ) );
				timeEl.textContent = time;

				careEl.hidden = ! ( care && care.monthly );
				if ( care && care.monthly ) {
					careEl.textContent = fmt( i18n.care || 'Plus %s a month for care', money( care.monthly ) );
					lines.push( care.label + ' (' + money( care.monthly ) + '/mo)' );
				}
				list.innerHTML = '';
				lines.forEach( function ( l ) {
					var li = d.createElement( 'li' );
					li.textContent = l;
					list.appendChild( li );
				} );

				$$( '[data-when]', form ).forEach( function ( fs ) {
					fs.hidden = ! type[ fs.getAttribute( 'data-when' ) ];
				} );

				var summary = ( i18n.intro || 'Estimate from the website' ) + ': ' + lines.join( ', ' ) + '. ' + money( lo ) + '–' + Math.round( hi ).toLocaleString( 'en-US' ) + ', ' + time.toLowerCase() + '.';
				var mid = ( lo + hi ) / 2;
				var params = new URLSearchParams( { jd_type: type.form || '', jd_budget: mid < 1500 ? 0 : mid < 3500 ? 1 : mid < 6000 ? 2 : 3, jd_est: summary } );
				cta.href = quote + ( quote.indexOf( '?' ) === -1 ? '?' : '&' ) + params.toString() + '#contact';

				if ( motion ) {
					G.to( shown, { lo: lo, hi: hi, duration: 0.6, ease: 'expo.out', overwrite: true, onUpdate: function () {
						paint( shown.lo, shown.hi );
					} } );
					G.fromTo( priceEl, { scale: 1.06 }, { scale: 1, duration: 0.5, ease: 'back.out(3)', overwrite: 'auto' } );
				} else {
					shown.lo = lo;
					shown.hi = hi;
					paint( lo, hi );
				}
			}
			est.classList.add( 'is-live' );
			form.addEventListener( 'change', calc );
			form.addEventListener( 'submit', function ( e ) {
				e.preventDefault();
			} );
			calc();
		} );
	} );

	/* Quote form: fill in what the estimator sent over ----------------------- */
	safe( 'prefill', function () {
		var q = new URLSearchParams( location.search );
		if ( ! q.get( 'jd_est' ) ) {
			return;
		}
		$$( 'form.jd-form' ).forEach( function ( form ) {
			var pick = function ( name, value ) {
				$$( 'input[name="' + name + '"]', form ).forEach( function ( r ) {
					if ( r.value === value ) {
						r.checked = true;
					}
				} );
			};
			pick( 'type', q.get( 'jd_type' ) );
			var b = parseInt( q.get( 'jd_budget' ), 10 );
			var budgets = $$( 'input[name="budget"]', form );
			if ( budgets[ b ] ) {
				budgets[ b ].checked = true;
			}
			if ( form.elements.details && ! form.elements.details.value ) {
				form.elements.details.value = q.get( 'jd_est' ).slice( 0, 1000 ) + '\n\n';
			}
		} );
	} );

	/* Copy buttons ---------------------------------------------------------- */
	d.addEventListener( 'click', function ( e ) {
		var b = e.target.closest ? e.target.closest( '[data-copy]' ) : null;
		if ( ! b ) {
			return;
		}
		var text = b.getAttribute( 'data-copy' );
		var reset = function () {
			setTimeout( function () {
				b.textContent = t.copy || 'Copy';
			}, 1800 );
		};
		var fallback = function () {
			var span = b.parentNode.querySelector( '.jd-copy__text' );
			if ( span ) {
				var r = d.createRange();
				r.selectNodeContents( span );
				var sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange( r );
			}
			b.textContent = t.selected || 'Selected';
			reset();
		};
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( text ).then( function () {
				b.textContent = t.copied || 'Copied';
				reset();
			}, fallback );
		} else {
			fallback();
		}
	} );

	/* Clocks ---------------------------------------------------------------- */
	safe( 'clocks', function () {
		var cells = $$( '.jd-clocks__t[data-tz]' );
		var live = $( '.jd-live' );
		if ( ! cells.length && ! live ) {
			return;
		}
		function tick() {
			var now = new Date();
			cells.forEach( function ( el ) {
				try {
					el.textContent = new Intl.DateTimeFormat( 'en-US', { hour: 'numeric', minute: '2-digit', timeZone: el.getAttribute( 'data-tz' ) } ).format( now );
				} catch ( e ) {}
			} );
			if ( live ) {
				try {
					var parts = new Intl.DateTimeFormat( 'en-US', { hour: 'numeric', hourCycle: 'h23', weekday: 'short', timeZone: cfg.liveTz || 'America/New_York' } ).formatToParts( now );
					var h = 0, day = 'Mon';
					parts.forEach( function ( p ) {
						if ( 'hour' === p.type ) {
							h = parseInt( p.value, 10 );
						}
						if ( 'weekday' === p.type ) {
							day = p.value;
						}
					} );
					var on = h >= ( cfg.liveStart || 9 ) && h < ( cfg.liveEnd || 14 ) && 'Sat' !== day && 'Sun' !== day;
					live.classList.toggle( 'is-online', on );
					var txt = $( '.jd-live__text', live );
					if ( txt ) {
						txt.textContent = on ? t.online || 'Online now' : t.offline || 'Offline';
					}
				} catch ( e ) {}
			}
		}
		tick();
		setInterval( tick, 30000 );
	} );

	/* Cursor + magnetic buttons ---------------------------------------------- */
	safe( 'cursor', function () {
		if ( ! motion || ! cfg.cursor || ! fine ) {
			return;
		}
		var dot = d.createElement( 'div' );
		var ring = d.createElement( 'div' );
		dot.className = 'jd-cur';
		ring.className = 'jd-cur-ring';
		dot.setAttribute( 'aria-hidden', 'true' );
		ring.setAttribute( 'aria-hidden', 'true' );
		d.body.appendChild( dot );
		d.body.appendChild( ring );
		var dx = G.quickTo( dot, 'x', { duration: 0.12 } );
		var dy = G.quickTo( dot, 'y', { duration: 0.12 } );
		var rx = G.quickTo( ring, 'x', { duration: 0.45, ease: 'power3' } );
		var ry = G.quickTo( ring, 'y', { duration: 0.45, ease: 'power3' } );
		G.set( [ dot, ring ], { x: -100, y: -100 } );
		window.addEventListener( 'pointermove', function ( e ) {
			dx( e.clientX );
			dy( e.clientY );
			rx( e.clientX );
			ry( e.clientY );
		} );
		d.addEventListener( 'pointerover', function ( e ) {
			var el = e.target.closest ? e.target.closest( 'a, button, [data-cursor], summary, label' ) : null;
			ring.classList.remove( 'is-big', 'is-hover' );
			ring.textContent = '';
			if ( ! el ) {
				return;
			}
			if ( el.getAttribute( 'data-cursor' ) ) {
				ring.classList.add( 'is-big' );
				ring.textContent = el.getAttribute( 'data-cursor' );
			} else {
				ring.classList.add( 'is-hover' );
			}
		} );
		$$( '.jd-mag' ).forEach( function ( el ) {
			var xTo = G.quickTo( el, 'x', { duration: 0.5, ease: 'elastic.out(1,.4)' } );
			var yTo = G.quickTo( el, 'y', { duration: 0.5, ease: 'elastic.out(1,.4)' } );
			el.addEventListener( 'pointermove', function ( e ) {
				var r = el.getBoundingClientRect();
				xTo( ( e.clientX - r.left - r.width / 2 ) * 0.3 );
				yTo( ( e.clientY - r.top - r.height / 2 ) * 0.35 );
			} );
			el.addEventListener( 'pointerleave', function () {
				xTo( 0 );
				yTo( 0 );
			} );
		} );
	} );

	/* Playful extras: progress bar, floating doodles, springy cards ------------ */
	safe( 'playful', function () {
		if ( ! motion || ! cfg.playful ) {
			return;
		}
		var bar = d.createElement( 'div' );
		bar.className = 'jd-progress';
		bar.setAttribute( 'aria-hidden', 'true' );
		d.body.appendChild( bar );
		G.to( bar, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: d.body, start: 'top top', end: 'bottom bottom', scrub: 0.3 } } );

		// Brand-colored shapes that pop in and drift past at their own speed. Decorative only.
		var shapes = {
			diamond: '<rect x="9" y="9" width="22" height="22" rx="4" transform="rotate(45 20 20)" fill="currentColor"/>',
			ring: '<circle cx="20" cy="20" r="13" fill="none" stroke="currentColor" stroke-width="5"/>',
			plus: '<path d="M20 6v28M6 20h28" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>',
			squiggle: '<path d="M3 24c5-10 9-10 12 0s7 10 11 0 8-10 11 0" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round"/>',
			arc: '<path d="M7 30a13 13 0 0 1 26 0" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>',
			dots: '<g fill="currentColor"><circle cx="8" cy="8" r="3.5"/><circle cx="20" cy="8" r="3.5"/><circle cx="32" cy="8" r="3.5"/><circle cx="8" cy="20" r="3.5"/><circle cx="20" cy="20" r="3.5"/><circle cx="32" cy="20" r="3.5"/><circle cx="8" cy="32" r="3.5"/><circle cx="20" cy="32" r="3.5"/><circle cx="32" cy="32" r="3.5"/></g>',
		};
		var names = Object.keys( shapes );
		var spots = [ [ 3, 12 ], [ 91, 6 ], [ 94, 64 ], [ 2, 72 ] ];
		$$( '.jd-section' )
			.filter( function ( sec ) {
				return ! sec.classList.contains( 'jd-hwork' ) && ! sec.closest( '.jd-screen, .jd-cta' ) && sec.offsetHeight > 280;
			} )
			.forEach( function ( sec, i ) {
				var box = d.createElement( 'div' );
				box.className = 'jd-doodles';
				box.setAttribute( 'aria-hidden', 'true' );
				[ 0, 1 ].forEach( function ( k ) {
					var name = names[ ( i * 2 + k ) % names.length ];
					var spot = spots[ ( i + k * 2 ) % spots.length ];
					var el = d.createElement( 'span' );
					el.className = 'jd-doodle jd-doodle--' + name;
					el.style.left = spot[ 0 ] + '%';
					el.style.top = spot[ 1 ] + '%';
					el.innerHTML = '<svg viewBox="0 0 40 40" focusable="false">' + shapes[ name ] + '</svg>';
					box.appendChild( el );
					var dir = k ? -1 : 1;
					var drift = 50 + ( i % 3 ) * 30;
					G.from( el.firstChild, { scale: 0, rotation: -120 * dir, duration: 1.2, ease: 'back.out(2.2)', scrollTrigger: { trigger: sec, start: 'top 75%' } } );
					G.fromTo( el, { y: drift * dir }, { y: -drift * dir, rotation: 140 * dir, ease: 'none', scrollTrigger: { trigger: sec, start: 'top bottom', end: 'bottom top', scrub: true } } );
				} );
				sec.classList.add( 'jd-has-doodles' );
				sec.appendChild( box );
			} );

		// Cards land with a small tilt and a springy settle.
		$$( '.jd-box, .jd-gcard, .jd-faq__item, .jd-faq-core .wp-block-details, .jd-facts-row > div, .jd-case__cols > div, .jd-case__quote, .jd-case-aside' )
			.filter( function ( el ) {
				return ! el.closest( '.jd-plans, .jd-princ, .jd-pay, .jd-steps, .jd-reveal' );
			} )
			.forEach( function ( el, i ) {
				G.from( el, { y: 40, rotation: i % 2 ? 2.5 : -2.5, scale: 0.95, autoAlpha: 0, duration: 0.9, ease: 'back.out(1.7)', delay: ( i % 3 ) * 0.06, scrollTrigger: { trigger: el, start: 'top 92%' } } );
			} );
	} );

	/* Recalculate pinned sections once fonts and images settle ---------------- */
	if ( motion ) {
		// Triggers are created section by section, so a reveal set up before a pin further up the
		// page would measure without that pin's spacer and fire early. Sort by page position first.
		ST.sort();
		ST.refresh();
		if ( d.fonts && d.fonts.ready ) {
			d.fonts.ready.then( function () {
				ST.refresh();
			} );
		}
		window.addEventListener( 'load', function () {
			ST.refresh();
		} );
	}
} )();
