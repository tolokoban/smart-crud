/**********************************************************************
 require( 'crud.controller.input' )
 -----------------------------------------------------------------------
 
 **********************************************************************/
var Listeners = require("tfw.listeners");


/**
 * @param element {object} - <input> element.
 * @param validator {function( value )} - function returning `true` if the `value` is a valid one.
 */
exports.setValidator = function( element, validator ) {
    if( !typeof validator === 'function' ) {
        throw Error( "[crud.controller.input]" );
    }
    if( element.$ctrl ) {
        element.$ctrl.validator = validator.bind( element, element.value );
    } else {
        element.$ctrl = {
            eventValidation: new Listeners(),
            validator: validator.bind( element, element.value )
        };
        
    }
};
