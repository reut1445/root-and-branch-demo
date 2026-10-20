/**
 * Sign-up form: submit in place instead of reloading the page.
 * Without JavaScript the form still posts normally and redirects back.
 */
( function () {
	'use strict';

	function enhance( form ) {
		var status = form.querySelector( '.rb-signup__status' );
		var button = form.querySelector( '.rb-signup__submit' );

		form.addEventListener( 'submit', function ( event ) {
			if ( ! window.fetch || ! window.FormData ) {
				return;
			}
			event.preventDefault();

			if ( ! form.checkValidity() ) {
				form.reportValidity();
				return;
			}

			var data = new FormData( form );
			data.append( 'rb_ajax', '1' );

			form.setAttribute( 'aria-busy', 'true' );
			button.disabled = true;
			status.className = 'rb-signup__status';
			status.textContent = '';

			// getAttribute: the hidden "action" field shadows form.action.
			fetch( form.getAttribute( 'action' ), {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
				headers: { Accept: 'application/json' },
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( result ) {
					var ok = result && result.success;
					status.classList.add( ok ? 'is-success' : 'is-error' );
					status.textContent = ok
						? form.getAttribute( 'data-success' )
						: ( result && result.data && result.data.message ) || form.getAttribute( 'data-error' );
					if ( ok ) {
						form.reset();
					}
				} )
				.catch( function () {
					status.classList.add( 'is-error' );
					status.textContent = form.getAttribute( 'data-error' );
				} )
				.finally( function () {
					form.removeAttribute( 'aria-busy' );
					button.disabled = false;
				} );
		} );
	}

	document.querySelectorAll( 'form[data-rb-signup]' ).forEach( enhance );
} )();
