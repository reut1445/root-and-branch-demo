/**
 * Header that stays within reach.
 *
 * After the first screen the header docks to the top of the window on a
 * cream bar; it slides away while reading down and returns as soon as the
 * visitor scrolls up. Also pauses the hero video for reduced motion.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var header = document.querySelector( '.wp-site-blocks > header.wp-block-template-part' );

	var video = document.querySelector( '.is-style-rb-hero video' );
	if ( video ) {
		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			video.removeAttribute( 'autoplay' );
			video.pause();
		} else {
			// Some browsers only honour autoplay when muted is set as a property.
			video.muted = true;
			var play = function () {
				var p = video.play();
				if ( p && p.catch ) {
					p.catch( function () {} );
				}
			};
			play();
			document.addEventListener( 'rb:loaded', play );
			document.addEventListener( 'visibilitychange', function () {
				if ( ! document.hidden ) {
					play();
				}
			} );
		}
	}

	/* Sign-up pop-up ---------------------------------------------------- */
	var modal = document.getElementById( 'join' );
	var opener = null;
	if ( modal && modal.showModal ) {
		document.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( '.rb-open-signup a, a.rb-open-signup, .rb-open-signup > .wp-block-navigation-item__content' );
			if ( ! link ) {
				return;
			}
			e.preventDefault();
			opener = link;
			var nav = link.closest( '.wp-block-navigation__responsive-container.is-menu-open' );
			if ( nav ) {
				var close = nav.querySelector( '.wp-block-navigation__responsive-container-close' );
				if ( close ) {
					close.click();
				}
			}
			modal.showModal();
			root.classList.add( 'rb-modal-open' );
			var first = modal.querySelector( '.rb-signup__input' );
			if ( first ) {
				first.focus();
			}
		} );

		// Click on the dimmed backdrop closes it.
		modal.addEventListener( 'click', function ( e ) {
			if ( e.target === modal ) {
				closeModal();
			}
		} );
		modal.addEventListener( 'cancel', function ( e ) {
			e.preventDefault();
			closeModal();
		} );
		modal.querySelector( '.rb-modal__close-form' ).addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			closeModal();
		} );
		modal.addEventListener( 'close', function () {
			root.classList.remove( 'rb-modal-open' );
			if ( opener ) {
				opener.focus();
			}
		} );
	}

	function closeModal() {
		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			modal.close();
			return;
		}
		modal.classList.add( 'is-closing' );
		setTimeout( function () {
			modal.classList.remove( 'is-closing' );
			modal.close();
		}, 240 );
	}

	if ( ! header ) {
		return;
	}

	var last = window.scrollY;
	var ticking = false;

	function threshold() {
		var hero = document.querySelector( '.is-style-rb-hero' );
		return hero ? hero.offsetTop + hero.offsetHeight * 0.6 : header.offsetHeight + 40;
	}

	var docked = false;

	function update() {
		var y = window.scrollY;
		var goingDown = y > last;
		var menuOpen = !! header.querySelector( '.is-menu-open' );

		// Dock past the hero (while hidden, so nothing jumps); undock only at the top.
		if ( ! docked && y > threshold() ) {
			docked = true;
			root.classList.add( 'rb-header-hidden' );
			root.classList.add( 'rb-header-docked' );
		} else if ( docked && y < 8 ) {
			docked = false;
			root.classList.remove( 'rb-header-docked', 'rb-header-hidden' );
		}

		if ( docked && Math.abs( y - last ) > 4 && ! menuOpen && ! header.contains( document.activeElement ) ) {
			root.classList.toggle( 'rb-header-hidden', goingDown );
		}
		last = y;
		ticking = false;
	}

	window.addEventListener(
		'scroll',
		function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( update );
				ticking = true;
			}
		},
		{ passive: true }
	);
	header.addEventListener( 'focusin', function () {
		root.classList.remove( 'rb-header-hidden' );
	} );
	update();
} )();
