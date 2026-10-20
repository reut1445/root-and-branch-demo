/**
 * Smooth scrolling (Lenis), driven by GSAP's ticker so ScrollTrigger scenes
 * stay frame-accurate.
 *
 * - Mouse wheel / trackpad only; touch devices keep native scrolling.
 * - Off for prefers-reduced-motion.
 * - In-page links (#sign-up, #register-options…) glide to their target,
 *   leaving room for the docked header.
 * - Paused while the first-visit loader, the sign-up pop-up or the mobile
 *   menu is open.
 */
( function () {
	'use strict';

	var root = document.documentElement;

	if ( ! window.Lenis || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var lenis = new window.Lenis( {
		duration: 1.15,
		easing: function ( t ) {
			return Math.min( 1, 1.001 - Math.pow( 2, -10 * t ) );
		},
		smoothWheel: true,
		syncTouch: false,
	} );
	window.rbLenis = lenis;

	if ( window.gsap && window.ScrollTrigger ) {
		lenis.on( 'scroll', window.ScrollTrigger.update );
		window.gsap.ticker.add( function ( time ) {
			lenis.raf( time * 1000 );
		} );
		window.gsap.ticker.lagSmoothing( 0 );
	} else {
		( function frame( time ) {
			lenis.raf( time );
			requestAnimationFrame( frame );
		} )( performance.now() );
	}

	// In-page links glide instead of jumping.
	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest( 'a[href*="#"]' );
		if ( ! link || e.defaultPrevented || link.closest( '.rb-open-signup' ) ) {
			return;
		}
		var url = new URL( link.href, window.location.href );
		if ( url.pathname !== window.location.pathname || ! url.hash || '#' === url.hash ) {
			return;
		}
		var target = document.getElementById( decodeURIComponent( url.hash.slice( 1 ) ) );
		if ( ! target ) {
			return;
		}
		e.preventDefault();
		lenis.scrollTo( target, { offset: -90 } );
		if ( window.history && window.history.pushState ) {
			window.history.pushState( null, '', url.hash );
		}
	} );

	// Pause while something modal is in front.
	function sync() {
		var locked = root.classList.contains( 'rb-loading' ) || root.classList.contains( 'rb-modal-open' ) || !! document.querySelector( '.wp-block-navigation__responsive-container.is-menu-open' );
		if ( locked ) {
			lenis.stop();
		} else {
			lenis.start();
		}
	}
	new MutationObserver( sync ).observe( root, { attributes: true, attributeFilter: [ 'class' ] } );
	document.addEventListener( 'click', function () {
		setTimeout( sync, 50 );
	} );
	sync();
} )();
