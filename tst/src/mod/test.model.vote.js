var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Vote( attribs ) {
    Crud.Model.call( this, attribs, ["vote"], [] );
}

// Inheritance from Widget
Vote.prototype = Object.create(Crud.Model.prototype);
Vote.prototype.constructor = Vote;



module.exports.create = function( obj ) {
    return WS.get( 'test.Vote.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( 'test.Vote.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Vote.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Vote.delete' );
};
