var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function {{NAME}}( attribs ) {
    Crud.Model.call( this, attribs, {{FIELDS}}, [] );
}

// Inheritance from Widget
{{NAME}}.prototype = Object.create(Crud.Model.prototype);
{{NAME}}.prototype.constructor = {{NAME}};



module.exports.create = function( obj ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( '{{PREFIX}}.{{NAME}}.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.update' );
};

module.exports.delete = function( id ) {
    return WS.get( '{{PREFIX}}.{{NAME}}.delete' );
};
