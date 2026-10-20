/**
 * Root & Branch — motion (GSAP + ScrollTrigger, bundled locally).
 *
 * Everything is attached to the design-system classes, so editors never
 * manage animation markup and new content animates automatically.
 *
 * - Running text "types" in letter by letter from transparent, scrubbed to
 *   the scroll position (scrolling back up reverses it).
 * - Section titles rise word by word out of masks.
 * - Value rows: line draws, title slides, photo opens and follows the
 *   pointer in 3D.
 * - Mission cards flip in; tilt under the pointer.
 * - Collage photos fall in, then float gently up and down.
 * - Statement: pinned scene where the words type themselves while both
 *   photos arrive from the sides at exactly the same pace; the signature
 *   then writes itself.
 * - Smaller reveals (buttons, panels, photos, timeline) via IntersectionObserver.
 *
 * Nothing runs for prefers-reduced-motion, and content is never left hidden
 * (see the failsafe in inc/assets.php).
 */
( function () {
	'use strict';

	var root = document.documentElement;
	window.rbMotionReady = true;

	if ( ! root.classList.contains( 'rb-motion' ) || ! window.gsap || ! window.ScrollTrigger || ! ( 'IntersectionObserver' in window ) ) {
		root.classList.remove( 'rb-motion' );
		return;
	}

	var gsap = window.gsap;
	var ScrollTrigger = window.ScrollTrigger;
	gsap.registerPlugin( ScrollTrigger );

	var content = document.querySelector( '.wp-site-blocks' );
	if ( ! content ) {
		return;
	}

	var finePointer = window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches;
	var desktop = window.matchMedia( '(min-width: 1024px)' );

	/* ------------------------------------------------------------------ *
	 * Splitting
	 * ------------------------------------------------------------------ */

	/**
	 * Wrap words (and optionally letters) of an element in spans, keeping
	 * inline markup such as links and accent spans.
	 *
	 * @param {Element} el
	 * @param {boolean} letters Split each word into letters too.
	 * @return {Element[]} Animated units (letters or words).
	 */
	function split( el, letters ) {
		if ( el.dataset.rbSplit ) {
			return Array.prototype.slice.call( el.querySelectorAll( letters ? '.rb-ch' : '.rb-w__i' ) );
		}
		el.dataset.rbSplit = '1';
		var units = [];

		function wrapWord( text ) {
			var outer = document.createElement( 'span' );
			outer.className = letters ? 'rb-word' : 'rb-w';
			if ( letters ) {
				Array.prototype.forEach.call( text, function ( ch ) {
					var c = document.createElement( 'span' );
					c.className = 'rb-ch';
					c.textContent = ch;
					outer.appendChild( c );
					units.push( c );
				} );
			} else {
				var inner = document.createElement( 'span' );
				inner.className = 'rb-w__i';
				inner.textContent = text;
				outer.appendChild( inner );
				units.push( inner );
			}
			return outer;
		}

		( function walk( node ) {
			Array.prototype.slice.call( node.childNodes ).forEach( function ( child ) {
				if ( 3 === child.nodeType ) {
					var frag = document.createDocumentFragment();
					child.textContent.split( /(\s+)/ ).forEach( function ( part ) {
						if ( ! part ) {
							return;
						}
						frag.appendChild( /^\s+$/.test( part ) ? document.createTextNode( part ) : wrapWord( part ) );
					} );
					child.parentNode.replaceChild( frag, child );
				} else if ( 1 === child.nodeType && 'BR' !== child.tagName ) {
					walk( child );
				}
			} );
		} )( el );

		return units;
	}

	/**
	 * Letter-split a paragraph while keeping it readable for assistive tech:
	 * a hidden plain copy is read, the animated copy is hidden from it.
	 * Paragraphs with links keep their structure (links stay usable).
	 */
	function splitReadable( el ) {
		if ( ! el.querySelector( 'a, button, input, select, textarea' ) ) {
			var plain = el.textContent;
			var units = split( el, true );
			var live = document.createElement( 'span' );
			live.setAttribute( 'aria-hidden', 'true' );
			while ( el.firstChild ) {
				live.appendChild( el.firstChild );
			}
			var sr = document.createElement( 'span' );
			sr.className = 'screen-reader-text';
			sr.textContent = plain;
			el.appendChild( sr );
			el.appendChild( live );
			return units;
		}
		return split( el, true );
	}

	function inHero( el ) {
		return !! el.closest( '.is-style-rb-hero' );
	}

	/* ------------------------------------------------------------------ *
	 * Section titles — words rise out of masks
	 * ------------------------------------------------------------------ */

	content.querySelectorAll( '.wp-block-post-content h2.wp-block-heading, .rb-register__title, .rb-page-title, .wp-block-post-content .is-style-rb-timeline-item > h3' ).forEach( function ( el ) {
		if ( inHero( el ) || el.closest( '.is-style-rb-statement' ) ) {
			return;
		}
		var words = split( el, false );
		gsap.from( words, {
			yPercent: 115,
			rotate: 4,
			transformOrigin: '0% 100%',
			duration: 1.15,
			ease: 'expo.out',
			stagger: 0.06,
			scrollTrigger: { trigger: el, start: 'top 88%', toggleActions: 'play none none reverse' },
		} );
	} );

	/* ------------------------------------------------------------------ *
	 * Running text — typed in from transparency, scrubbed with the scroll
	 * ------------------------------------------------------------------ */

	var textSelector = [
		'.wp-block-post-content p',
		'.wp-block-post-content li',
		'.rb-single .wp-block-post-content p',
	].join( ',' );

	var skipText = '.is-style-rb-hero, .is-style-rb-statement, .is-style-rb-card, .is-style-rb-mission-card, .rb-card__label, .wp-block-buttons, .rb-signup, figcaption, .rb-price';

	content.querySelectorAll( textSelector ).forEach( function ( el ) {
		// Only plain text runs: never containers such as the cards in a query loop.
		if ( el.closest( skipText ) || el.querySelector( 'div, figure, p, ul, ol, h1, h2, h3, h4' ) || ! el.textContent.trim() ) {
			return;
		}
		var letters = splitReadable( el );
		gsap.fromTo(
			letters,
			{ opacity: 0.1 },
			{
				opacity: 1,
				ease: 'none',
				stagger: 0.05,
				duration: 0.5,
				scrollTrigger: { trigger: el, start: 'top 90%', end: 'bottom 62%', scrub: 0.6 },
			}
		);
	} );

	/* ------------------------------------------------------------------ *
	 * Value rows — scroll choreography + pointer depth
	 * ------------------------------------------------------------------ */

	content.querySelectorAll( '.is-style-rb-value-row' ).forEach( function ( row ) {
		var title = row.querySelector( '.wp-block-heading' );
		var figure = row.querySelector( '.wp-block-image' );
		var img = figure && figure.querySelector( 'img' );

		row.classList.add( 'rb-row-armed' );

		var tl = gsap.timeline( {
			scrollTrigger: { trigger: row, start: 'top 88%', end: 'top 38%', scrub: 0.8 },
		} );
		tl.fromTo( row, { '--rb-line': 0 }, { '--rb-line': 1, ease: 'power2.inOut', duration: 1 }, 0 );
		if ( title ) {
			tl.fromTo( title, { xPercent: -18, opacity: 0, filter: 'blur(8px)' }, { xPercent: 0, opacity: 1, filter: 'blur(0px)', ease: 'power3.out', duration: 0.8 }, 0.05 );
		}
		if ( figure ) {
			tl.fromTo(
				figure,
				{ clipPath: 'inset(8% 0% 8% 100% round 15px)' },
				{ clipPath: 'inset(0% 0% 0% 0% round 15px)', ease: 'power3.inOut', duration: 0.9 },
				0.1
			);
		}
		if ( img ) {
			gsap.fromTo( img, { yPercent: -7, scale: 1.18 }, {
				yPercent: 7,
				scale: 1.08,
				ease: 'none',
				scrollTrigger: { trigger: row, start: 'top bottom', end: 'bottom top', scrub: true },
			} );
		}

		if ( finePointer && figure ) {
			var rotY = gsap.quickTo( figure, 'rotationY', { duration: 0.6, ease: 'power3' } );
			var rotX = gsap.quickTo( figure, 'rotationX', { duration: 0.6, ease: 'power3' } );
			var shift = title ? gsap.quickTo( title, 'x', { duration: 0.8, ease: 'power3' } ) : null;
			gsap.set( figure, { transformPerspective: 900 } );
			row.addEventListener( 'pointermove', function ( e ) {
				var r = row.getBoundingClientRect();
				var px = ( e.clientX - r.left ) / r.width - 0.5;
				var py = ( e.clientY - r.top ) / r.height - 0.5;
				rotY( px * 16 );
				rotX( py * -10 );
				if ( shift ) {
					shift( px * -18 );
				}
				var f = figure.getBoundingClientRect();
				figure.style.setProperty( '--mx', ( ( e.clientX - f.left ) / f.width ) * 100 + '%' );
				figure.style.setProperty( '--my', ( ( e.clientY - f.top ) / f.height ) * 100 + '%' );
			} );
			row.addEventListener( 'pointerleave', function () {
				rotY( 0 );
				rotX( 0 );
				if ( shift ) {
					shift( 0 );
				}
			} );
		}
	} );

	/* ------------------------------------------------------------------ *
	 * Mission cards — flip in, tilt under the pointer
	 * ------------------------------------------------------------------ */

	content.querySelectorAll( '.rb-mission__cards, .rb-options' ).forEach( function ( group ) {
		var cards = Array.prototype.slice.call( group.children );
		gsap.fromTo(
			cards,
			{ rotationY: -100, y: 60, opacity: 0, transformPerspective: 1400, transformOrigin: '0% 50%' },
			{
				rotationY: 0,
				y: 0,
				opacity: 1,
				duration: 1.4,
				ease: 'expo.out',
				stagger: 0.14,
				scrollTrigger: { trigger: group, start: 'top 82%', toggleActions: 'play none none reverse' },
			}
		);

		if ( finePointer ) {
			cards.forEach( function ( card ) {
				var ry = gsap.quickTo( card, 'rotationY', { duration: 0.5, ease: 'power3' } );
				var rx = gsap.quickTo( card, 'rotationX', { duration: 0.5, ease: 'power3' } );
				card.addEventListener( 'pointermove', function ( e ) {
					var r = card.getBoundingClientRect();
					ry( ( ( e.clientX - r.left ) / r.width - 0.5 ) * 14 );
					rx( ( ( e.clientY - r.top ) / r.height - 0.5 ) * -14 );
				} );
				card.addEventListener( 'pointerleave', function () {
					ry( 0 );
					rx( 0 );
				} );
			} );
		}
	} );

	/* ------------------------------------------------------------------ *
	 * Collage — photos fall in, then breathe
	 * ------------------------------------------------------------------ */

	content.querySelectorAll( '.wp-block-gallery.is-style-rb-scatter' ).forEach( function ( gallery ) {
		var tiles = Array.prototype.slice.call( gallery.querySelectorAll( ':scope > .wp-block-image' ) );
		var floats = [];

		gsap.fromTo(
			tiles,
			{ yPercent: -60, opacity: 0, scale: 0.8, rotation: function () { return gsap.utils.random( -14, 14 ); } },
			{
				yPercent: 0,
				opacity: 1,
				scale: 1,
				rotation: 0,
				duration: 1.3,
				ease: 'back.out(1.5)',
				stagger: { each: 0.06, from: 'random' },
				scrollTrigger: { trigger: gallery, start: 'top 80%', toggleActions: 'play none none reverse' },
			}
		);

		tiles.forEach( function ( tile, i ) {
			var up = i % 2 === 0;
			floats.push(
				gsap.to( tile, {
					y: up ? gsap.utils.random( -22, -12 ) : gsap.utils.random( 12, 22 ),
					rotation: gsap.utils.random( -1.6, 1.6 ),
					duration: gsap.utils.random( 3.2, 5.6 ),
					ease: 'sine.inOut',
					yoyo: true,
					repeat: -1,
					delay: gsap.utils.random( 0, 1.5 ),
					paused: true,
				} )
			);
		} );

		ScrollTrigger.create( {
			trigger: gallery,
			start: 'top bottom',
			end: 'bottom top',
			onToggle: function ( self ) {
				floats.forEach( function ( t ) {
					return self.isActive ? t.play() : t.pause();
				} );
			},
		} );
	} );

	/* ------------------------------------------------------------------ *
	 * Statement — words type themselves, photos arrive in step
	 * ------------------------------------------------------------------ */

	content.querySelectorAll( '.is-style-rb-statement' ).forEach( function ( section ) {
		var text = section.querySelector( '.rb-statement__text' );
		var signature = section.querySelector( '.is-style-rb-signature' );
		var figures = section.querySelectorAll( '.rb-statement__photos .wp-block-image' );
		if ( ! text ) {
			return;
		}
		var letters = splitReadable( text );
		var sign = signature ? splitReadable( signature ) : [];

		gsap.matchMedia().add(
			{ wide: '(min-width: 1024px)', narrow: '(max-width: 1023px)' },
			function ( ctx ) {
				var wide = ctx.conditions.wide;
				var tl = gsap.timeline( {
					defaults: { ease: 'none' },
					scrollTrigger: wide
						? { trigger: section, start: 'top top', end: '+=130%', scrub: 0.8, pin: true, anticipatePin: 1, invalidateOnRefresh: true }
						: { trigger: section, start: 'top 75%', end: 'bottom 75%', scrub: 0.8, invalidateOnRefresh: true },
				} );
				var typing = letters.length * 0.05 + 0.3;

				tl.fromTo( letters, { opacity: 0.08 }, { opacity: 1, stagger: 0.05, duration: 0.3 }, 0 );
				// Photos glide in from the screen edges, in step with the typing.
				if ( figures[ 0 ] ) {
					tl.fromTo( figures[ 0 ], { x: function () { return -window.innerWidth * 0.55; }, rotation: -10 }, { x: 0, rotation: 0, duration: typing, ease: 'none' }, 0 );
				}
				if ( figures[ 1 ] ) {
					tl.fromTo( figures[ 1 ], { x: function () { return window.innerWidth * 0.55; }, rotation: 10 }, { x: 0, rotation: 0, duration: typing, ease: 'none' }, 0 );
				}
				if ( signature ) {
					tl.fromTo( signature, { '--rb-stem': 0 }, { '--rb-stem': 1, duration: 0.6 } );
					tl.fromTo( sign, { opacity: 0, yPercent: 30 }, { opacity: 1, yPercent: 0, stagger: 0.06, duration: 0.25 } );
				}
			}
		);
	} );

	/* ------------------------------------------------------------------ *
	 * Smaller reveals (class toggles; the movement is CSS)
	 * ------------------------------------------------------------------ */

	var map = {
		rise: [
			'.wp-block-post-content > * > .wp-block-buttons',
			'.wp-block-post-content .wp-block-buttons:not(.is-style-rb-notch)',
			'.rb-register__form',
			'.rb-single .wp-block-post-featured-image',
			'.rb-footer__brand',
			'.rb-footer__partners',
			'.rb-footer-bottom',
		],
		stagger: [ '.rb-card-grid', '.wp-block-gallery.is-style-rb-masonry', '.rb-footer__menus', '.rb-terms' ],
		unveil: [ '.rb-founders__photo', '.rb-founder__portrait .wp-block-image', '.rb-history > .wp-block-image', '.rb-timeline-item__body .wp-block-image' ],
		item: [ '.is-style-rb-timeline-item' ],
	};

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				entry.target.classList.toggle( 'is-in', entry.isIntersecting || entry.boundingClientRect.top < 0 );
			} );
		},
		{ rootMargin: '0px 0px -8% 0px', threshold: 0.1 }
	);

	Object.keys( map ).forEach( function ( type ) {
		content.querySelectorAll( map[ type ].join( ',' ) ).forEach( function ( el ) {
			if ( inHero( el ) || el.hasAttribute( 'data-rb-reveal' ) ) {
				return;
			}
			el.setAttribute( 'data-rb-reveal', type );
			if ( 'stagger' === type ) {
				Array.prototype.forEach.call( el.children, function ( child, i ) {
					child.style.setProperty( '--i', i );
				} );
			}
			observer.observe( el );
		} );
	} );

	// Olive branches grow from the stem (headings, founder columns).
	content.querySelectorAll( '.is-style-rb-ornament, .is-style-rb-ornament-offset, .rb-founder__bio, .rb-founder__more' ).forEach( function ( el ) {
		gsap.fromTo( el, { '--rb-stem': 0 }, {
			'--rb-stem': 1,
			duration: 1.6,
			ease: 'power2.inOut',
			scrollTrigger: { trigger: el, start: 'top 88%', toggleActions: 'play none none reverse' },
		} );
	} );

	/* ------------------------------------------------------------------ *
	 * Hero — headline words rise on load
	 * ------------------------------------------------------------------ */

	var hero = content.querySelector( '.is-style-rb-hero' );
	if ( hero ) {
		var title = hero.querySelector( 'h1' );
		if ( title ) {
			split( title, false ).forEach( function ( w, i ) {
				w.style.setProperty( '--w', i );
			} );
		}
		var start = function () {
			requestAnimationFrame( function () {
				hero.classList.add( 'is-in' );
			} );
		};
		if ( root.classList.contains( 'rb-loading' ) ) {
			document.addEventListener( 'rb:loaded', start, { once: true } );
		} else {
			start();
		}
	}

	// The first-visit loader locks scrolling; measure again once it lifts.
	document.addEventListener( 'rb:loaded', function () {
		ScrollTrigger.refresh();
	} );

	// Recalculate once images and fonts have settled.
	window.addEventListener( 'load', function () {
		ScrollTrigger.refresh();
	} );
	desktop.addEventListener( 'change', function () {
		ScrollTrigger.refresh();
	} );
} )();
