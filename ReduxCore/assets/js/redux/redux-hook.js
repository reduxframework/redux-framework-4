function redux_hook( object, functionName, callback, before ) {
    (function( originalFunction ) {
        object[functionName] = function() {
            if ( before === true ) {
                callback.apply( this, [returnValue, originalFunction, arguments] );
            }
            
            var returnValue = originalFunction.apply( this, arguments );
            
            if ( before !== true ) {
                callback.apply( this, [returnValue, originalFunction, arguments] );
            }

            return returnValue;
        };
    }( object[functionName] ));
}