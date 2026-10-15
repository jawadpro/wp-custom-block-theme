/**
 * Registers the theme's dynamic blocks in the editor with a live server-side preview
 * and auto-generated sidebar controls for their attributes. No build step required.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var c = wp.components;
	var SSR = wp.serverSideRender;
	var __ = wp.i18n.__;

	function controlFor( name, schema, value, set ) {
		var label = ( schema.label || name ).replace( /([A-Z])/g, ' $1' ).replace( /^./, function ( s ) {
			return s.toUpperCase();
		} );
		var onChange = function ( v ) {
			var o = {};
			o[ name ] = v;
			set( o );
		};
		if ( schema.enum ) {
			return el( c.SelectControl, {
				key: name,
				label: label,
				value: value,
				options: schema.enum.map( function ( v ) {
					return { label: String( v ), value: v };
				} ),
				onChange: onChange,
				__nextHasNoMarginBottom: true,
			} );
		}
		if ( schema.type === 'boolean' ) {
			return el( c.ToggleControl, { key: name, label: label, checked: !! value, onChange: onChange, __nextHasNoMarginBottom: true } );
		}
		if ( schema.type === 'number' || schema.type === 'integer' ) {
			return el( c.RangeControl, { key: name, label: label, value: value, min: schema.minimum || 0, max: schema.maximum || 24, onChange: onChange, __nextHasNoMarginBottom: true } );
		}
		return el( c.TextControl, { key: name, label: label, value: value || '', onChange: onChange, __nextHasNoMarginBottom: true } );
	}

	var names = window.jdBlockNames || [];

	function register( name ) {
		if ( wp.blocks.getBlockType( name ) ) {
			return;
		}
		wp.blocks.registerBlockType( name, {
			edit: function ( props ) {
				var type = wp.blocks.getBlockType( name );
				var attrs = ( type && type.attributes ) || {};
				var controls = Object.keys( attrs )
					.filter( function ( k ) {
						return [ 'className', 'lock', 'metadata', 'style', 'align', 'anchor' ].indexOf( k ) === -1;
					} )
					.map( function ( k ) {
						return controlFor( k, attrs[ k ], props.attributes[ k ], props.setAttributes );
					} );
				var blockProps = be.useBlockProps ? be.useBlockProps() : {};
				return el(
					Fragment,
					null,
					controls.length
						? el( be.InspectorControls, null, el( c.PanelBody, { title: __( 'Settings', 'jawad' ) }, controls ) )
						: null,
					el( 'div', blockProps, el( SSR, { block: name, attributes: props.attributes, skipBlockSupportAttributes: true } ) )
				);
			},
			save: function () {
				return null;
			},
		} );
	}

	names.forEach( register );
} )( window.wp );
