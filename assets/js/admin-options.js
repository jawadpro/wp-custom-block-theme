/* Media picker for image fields on the Theme Options page. */
( function () {
	document.addEventListener( 'click', function ( e ) {
		var pick = e.target.closest( '.jd-media' );
		var clear = e.target.closest( '.jd-media-clear' );
		if ( pick && window.wp && wp.media ) {
			e.preventDefault();
			var id = pick.getAttribute( 'data-target' );
			var frame = wp.media( { title: pick.textContent, library: { type: 'image' }, multiple: false } );
			frame.on( 'select', function () {
				var a = frame.state().get( 'selection' ).first().toJSON();
				document.getElementById( id ).value = a.id;
				var prev = document.getElementById( id + '_prev' );
				prev.src = ( a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url );
				prev.hidden = false;
			} );
			frame.open();
		}
		if ( clear ) {
			e.preventDefault();
			var cid = clear.getAttribute( 'data-target' );
			document.getElementById( cid ).value = '';
			document.getElementById( cid + '_prev' ).hidden = true;
		}
	} );
} )();
