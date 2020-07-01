/* jshint unused:false */

function redux_hook( object, functionName, callback, before ) {
	(function( originalFunction ) {
		object[functionName] = function() {
			var returnValue;

			if ( true === before ) {
				callback.apply( this, [returnValue, originalFunction, arguments] );
			}

			returnValue = originalFunction.apply( this, arguments );

			if ( true !== before ) {
				callback.apply( this, [returnValue, originalFunction, arguments] );
			}

			return returnValue;
		};
	}( object[functionName] ) );
}
