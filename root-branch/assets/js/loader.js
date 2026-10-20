/**
 * First-visit loader: the Root & Branch seal grows like a tree.
 *
 * Printed inline right after <body> (inc/loader.php) so it starts before the
 * page paints. Runs once per browser session. Native Web Animations API.
 *
 * 1. The whole seal appears as a faint ghost.
 * 2. A soft edge rises from the roots to the crown, filling the tree with
 *    its colour as it climbs (washed-out to full colour).
 * 3. The outer ring and lettering settle in; the caption fades up.
 * 4. The seal contracts into a ball and the opening screen grows out of it.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var box = document.getElementById( 'rb-loader' );
	if ( ! box || ! root.classList.contains( 'rb-loading' ) ) {
		return;
	}

	var seal = box.querySelector( '.rb-loader__seal' );
	var caption = box.querySelector( '.rb-loader__caption' );
	var finished = false;

	var GROW = 3400; // roots to crown
	var START = 500; // ghost first

	function finish() {
		if ( finished ) {
			return;
		}
		finished = true;

		var hero = document.querySelector( '.is-style-rb-hero' );
		var canAnimate = !! ( seal && seal.animate );

		if ( canAnimate ) {
			// The seal gathers into a small ball…
			seal.animate(
				[ { transform: 'scale(1)', opacity: 1 }, { transform: 'scale(0.06)', opacity: 0.9 } ],
				{ duration: 650, easing: 'cubic-bezier(0.65, 0, 0.35, 1)', fill: 'forwards' }
			);
			if ( caption ) {
				caption.animate( [ { opacity: 1 }, { opacity: 0 } ], { duration: 300, fill: 'forwards' } );
			}
		}

		setTimeout(
			function () {
				// …and the opening screen opens out of it.
				if ( hero && hero.animate ) {
					hero.animate(
						[
							{ clipPath: 'circle(1.5% at 50% 50%)' },
							{ clipPath: 'circle(75% at 50% 50%)' },
						],
						{ duration: 1300, easing: 'cubic-bezier(0.7, 0, 0.2, 1)' }
					);
				}
				box.classList.add( 'is-done' );
				root.classList.remove( 'rb-loading' );
				document.dispatchEvent( new CustomEvent( 'rb:loaded' ) );
				setTimeout( function () {
					box.remove();
				}, 900 );
			},
			canAnimate ? 560 : 0
		);
	}

	// Never hold the page hostage.
	setTimeout( finish, 8000 );

	fetch( box.getAttribute( 'data-src' ) )
		.then( function ( r ) {
			return r.text();
		} )
		.then( function ( markup ) {
			var ghost = document.createElement( 'div' );
			var colour = document.createElement( 'div' );
			ghost.className = 'rb-loader__ghost';
			colour.className = 'rb-loader__colour';
			ghost.innerHTML = markup;
			colour.innerHTML = markup;
			seal.appendChild( ghost );
			seal.appendChild( colour );
			if ( ! colour.animate ) {
				return finish();
			}
			grow( ghost, colour );
		} )
		.catch( finish );

	function grow( ghost, colour ) {
		var svg = colour.querySelector( 'svg' );
		var vb = svg.viewBox.baseVal;
		var cx = vb.x + vb.width / 2;
		var cy = vb.y + vb.height / 2;
		var radius = Math.min( vb.width, vb.height ) / 2;

		// The frame (outer ring, lettering, laurels) comes after the tree.
		var frame = Array.prototype.filter.call( svg.querySelectorAll( 'path, ellipse, rect, circle, polygon' ), function ( el ) {
			var kind = el.getAttribute( 'data-k' );
			if ( 'bg' === kind ) {
				return false;
			}
			var b = el.getBBox();
			var x = b.x + b.width / 2;
			var y = b.y + b.height / 2;
			var dist = Math.sqrt( ( x - cx ) * ( x - cx ) + ( y - cy ) * ( y - cy ) );
			return 'ring' === kind || b.width > vb.width * 0.8 || dist > radius * 0.8;
		} );
		frame.forEach( function ( el ) {
			el.style.opacity = '0';
		} );

		var soft = 'cubic-bezier(0.45, 0, 0.25, 1)';

		// 1. Faint ghost of the whole seal.
		ghost.animate( [ { opacity: 0 }, { opacity: 0.13 } ], { duration: 900, easing: 'ease-out', fill: 'forwards' } );

		// 2. Growth: a soft edge climbs from the roots to the crown.
		colour.animate(
			[
				{ webkitMaskPosition: '0 0%', maskPosition: '0 0%', opacity: 0.35, filter: 'saturate(0.15) brightness(1.15)' },
				{ opacity: 0.8, filter: 'saturate(0.6) brightness(1.05)', offset: 0.55 },
				{ webkitMaskPosition: '0 100%', maskPosition: '0 100%', opacity: 1, filter: 'saturate(1) brightness(1)' },
			],
			{ duration: GROW, delay: START, easing: soft, fill: 'forwards' }
		);

		// 3. Frame and caption.
		frame.forEach( function ( el, i ) {
			el.animate( [ { opacity: 0 }, { opacity: 1 } ], {
				duration: 900,
				delay: START + GROW * 0.78 + i * ( 500 / Math.max( frame.length, 1 ) ),
				easing: 'ease-out',
				fill: 'forwards',
			} );
		} );
		if ( caption ) {
			caption.animate(
				[ { opacity: 0, transform: 'translateY(8px)', letterSpacing: '0.42em' }, { opacity: 1, transform: 'none', letterSpacing: '0.28em' } ],
				{ duration: 1600, delay: START + 900, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'forwards' }
			);
		}

		// 4. Hold the finished seal for a breath, then open the page.
		setTimeout( finish, START + GROW + 900 );
	}
} )();
