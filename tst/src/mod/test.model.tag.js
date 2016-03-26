var WS = require("tfw.web-service");
var Crud = require("smart-crud");


function Tag( attribs ) {
    Crud.Model.call( this, attribs, ["name"], [] );
}

// Inheritance from Widget
Tag.prototype = Object.create(Crud.Model.prototype);
Tag.prototype.constructor = Tag;



module.exports.create = function( obj ) {
    return WS.get( 'test.Tag.create' );
};

module.exports.request = function( criteria ) {
    if( typeof criteria === 'undefined' ) criteria = {};
    return WS.get( 'test.Tag.request', criteria );
};

module.exports.update = function( obj ) {
    return WS.get( 'test.Tag.update' );
};

module.exports.delete = function( id ) {
    return WS.get( 'test.Tag.delete' );
};
